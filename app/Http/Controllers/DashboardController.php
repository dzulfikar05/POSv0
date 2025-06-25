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
            ->whereIn('status', ['completed', 'paid_off']);

        if (!empty($request->tahun)) {
            $penjualan->whereYear('penjualan_tanggal', $request->tahun);
        }

        if (!empty($request->bulan)) {
            $penjualan->whereMonth('penjualan_tanggal', $request->bulan);
        }

        $getPenjualan = $penjualan->get()->sum(fn($p) => $p->detail->sum('harga'));

        $pembelanjaan = StokModel::select('harga_total');
        if (!empty($request->tahun)) {
            $pembelanjaan->whereYear('stok_tanggal', $request->tahun);
        }
        if (!empty($request->bulan)) {
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
        $penjualan = PenjualanModel::with('detail')->whereIn('status', ['completed', 'paid_off']);

        if (!empty($request->tahun)) {
            $penjualan->whereYear('penjualan_tanggal', $request->tahun);
        }

        $data = $penjualan->get();

        $perBulan = $data->groupBy(fn($d) => (int) date('m', strtotime($d->penjualan_tanggal)))
            ->map(fn($group) => $group->sum(fn($p) => $p->detail->sum('harga')));

        $dataBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataBulanan[] = $perBulan[$i] ?? 0;
        }

        $terlaris = PenjualanDetailModel::with(['barang', 'penjualan'])
            ->whereHas('penjualan', function ($q) use ($request) {
                $q->whereIn('status', ['completed', 'paid_off']);
                if (!empty($request->tahun)) {
                    $q->whereYear('penjualan_tanggal', $request->tahun);
                }
                if (!empty($request->bulan)) {
                    $q->whereMonth('penjualan_tanggal', $request->bulan);
                }
            })->get();

        $itemTerlaris = $terlaris->groupBy('barang_id')
            ->map(fn($group) => [
                'barang_nama' => $group->first()->barang->barang_nama ?? '-',
                'total' => $group->sum('jumlah')
            ])
            ->sortByDesc('total')->take(10)->values();

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
                    'Desember'
                ],
                'data' => $dataBulanan
            ],
            'item' => [
                'labels' => $itemTerlaris->pluck('barang_nama'),
                'data' => $itemTerlaris->pluck('total')
            ]
        ]);
    }
}
