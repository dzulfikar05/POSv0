<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\AdminModel;
use App\Models\PenjualanDetailModel;
use App\Models\PenjualanModel;
use App\Models\StokModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Dashboard',
            'list' => ['Home', 'Dashboard']
        ];

        $page = (object) [
            'title' => 'Dashboard'
        ];

        $activeMenu = 'dashboard';

        $level = LevelModel::all();

        return view('dashboard.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function getCardData(Request $request)
    {
        $penjualan = PenjualanModel::with('detail')
            ->where(function ($query) {
                $query->where('status', 'completed')
                    ->orWhere('status', 'paid_off');
            });

        if ($request->tahun) {
            $penjualan->whereYear('penjualan_tanggal', $request->tahun);
        }

        if ($request->bulan) {
            $penjualan->whereMonth('penjualan_tanggal', $request->bulan);
        }

        $getPenjualan = $penjualan->get()->sum(function ($penjualan) {
            return $penjualan->detail->sum('harga');
        });



        $pembelanjaan = StokModel::select('harga_total');
        if ($request->tahun) {
            $pembelanjaan->whereYear('stok_tanggal', $request->tahun);
        }
        if ($request->bulan) {
            $pembelanjaan->whereMonth('stok_tanggal', $request->bulan);
        }
        $getPembelanjaan = $pembelanjaan->sum('harga_total');


        $getMargin = $getPenjualan - $getPembelanjaan;

        return response()->json([
            'penjualan' => $getPenjualan,
            'pembelanjaan' => $getPembelanjaan,
            'margin' => $getMargin
        ]);
    }

    public function getChartData(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');

        // Penjualan per bulan
        $penjualanBulanan = PenjualanModel::with('detail')
            ->where(function ($q) {
                $q->where('status', 'completed')
                    ->orWhere('status', 'paid_off');
            })
            ->whereYear('penjualan_tanggal', $tahun)
            ->get()
            ->groupBy(function ($item) {
                return (int) date('m', strtotime($item->penjualan_tanggal));
            })
            ->map(function ($group) {
                return $group->sum(function ($p) {
                    return $p->detail->sum('harga');
                });
            });

        $dataBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataBulanan[] = $penjualanBulanan[$i] ?? 0;
        }

        // Item terlaris top 10 berdasarkan nama barang
        $itemTerlaris = PenjualanDetailModel::with(['barang', 'penjualan'])
            ->get()
            ->filter(function ($item) use ($tahun, $bulan) {
                $tanggal = $item->penjualan->penjualan_tanggal ?? null;
                return $tanggal &&
                    date('Y', strtotime($tanggal)) == $tahun &&
                    date('m', strtotime($tanggal)) == $bulan;
            })
            ->groupBy('barang_id')
            ->map(function ($group) {
                return [
                    'barang_nama' => $group->first()->barang->barang_nama ?? '-',
                    'total' => $group->sum('jumlah')
                ];
            })
            ->sortByDesc('total')
            ->take(10)
            ->values(); // reset key


        return response()->json([
            'bulan' => [
                'labels' => [
                    'Januari',
                    'Februari',
                    'Maret',
                    'April',
                    'Mei',
                    'Juni',
                    'Juli',
                    'Agustus',
                    'September',
                    'Oktober',
                    'November',
                    'Desember',
                ],
                'data' => $dataBulanan,
            ],
            'item' => [
                'labels' => $itemTerlaris->pluck('barang_nama'), // Ambil nama barang
                'data' => $itemTerlaris->pluck('total'),
            ],
        ]);
    }
}
