<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\BarangModel;
use App\Models\CustomerModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $page = (object) [
            'title' => ''
        ];

        $activeMenu = 'penjualan';

        return view('penjualan.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

  public function list(Request $request)
{
    $query = DB::table('t_penjualan')
        ->join('m_user as u1', 'u1.user_id', '=', 't_penjualan.user_id') // user pembuat
        ->join('m_user as u2', 'u2.user_id', '=', 't_penjualan.customer_id') // customer
        ->leftJoin(DB::raw('(SELECT penjualan_id, SUM(harga * jumlah) as total_harga FROM t_penjualan_detail GROUP BY penjualan_id) as tpd'), 'tpd.penjualan_id', '=', 't_penjualan.penjualan_id')
        ->select([
            't_penjualan.penjualan_id',
            't_penjualan.penjualan_kode',
            't_penjualan.penjualan_tanggal',
            't_penjualan.status',
            'u1.nama as user_nama',
            'u2.nama as customer_nama',
            'u2.wa as customer_wa',
            'tpd.total_harga'
        ])
        ->whereIn('t_penjualan.status', ['paid_off', 'completed']);

    if ($request->tahun) {
        $query->whereYear('t_penjualan.penjualan_tanggal', $request->tahun);
    }

    if ($request->bulan) {
        $query->whereMonth('t_penjualan.penjualan_tanggal', $request->bulan);
    }

    return DataTables::of($query)
        ->addIndexColumn()
        ->editColumn('penjualan_tanggal', function ($row) {
            return \Carbon\Carbon::parse($row->penjualan_tanggal)
                ->locale('id')
                ->translatedFormat('d F Y - H:i');
        })
        ->editColumn('total_harga', function ($row) {
            return number_format($row->total_harga ?? 0, 0, ',', '.');
        })
        ->filterColumn('user_nama', function ($query, $keyword) {
            $query->where('u1.nama', 'like', "%{$keyword}%");
        })
        ->filterColumn('customer_nama', function ($query, $keyword) {
            $query->where('u2.nama', 'like', "%{$keyword}%");
        })
        ->editColumn('status', function ($row) {
            return match ($row->status) {
                'paid_off' => '<span class="badge badge-primary">Lunas - Disiapkan</span>',
                'completed' => '<span class="badge badge-success">Selesai</span>',
                default => $row->status,
            };
        })
        ->addColumn('customer_wa', function ($row) {
            return '<a href="https://wa.me/' . $row->customer_wa . '" target="_blank" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i> ' . $row->customer_wa . '</a>';
        })
        ->addColumn('aksi', function ($row) {
            return '
                <button onclick="modalAction(\'' . url('/penjualan/' . $row->penjualan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm"><i class="fa fa-eye"></i></button>
                <button onclick="modalAction(\'' . url('/penjualan/' . $row->penjualan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                <a target="_blank" href="/penjualan/' . $row->penjualan_id . '/print_struk" class="btn btn-primary btn-sm mx-1"><i class="fa fa-file"></i></a>
            ';
        })
        ->rawColumns(['status', 'aksi', 'customer_wa'])
        ->make(true);
}


    public function create_ajax()
    {
        $barangs = BarangModel::all();
        return view('penjualan.create_ajax', compact('barangs'));
    }

    public function store_ajax(Request $request)
    {
        $request->merge(['user_id' => auth()->user()->user_id]);
        $request->merge(['penjualan_tanggal' => now()->format('Y-m-d H:i:s')]);
        $request->merge(['penjualan_kode' => 'PJ' . now()->format('YmdHis')]);

        $rules = [
            'user_id' => 'required|exists:m_user,user_id',
            'customer_id' => 'required|string|max:100',
            'penjualan_kode' => 'required|string|unique:t_penjualan,penjualan_kode',
            'penjualan_tanggal' => 'required|date',
            'barang_id.*' => 'required|exists:m_barang,barang_id',
            'harga.*' => 'required|numeric|min:0',
            'jumlah.*' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msgField' => $validator->errors()
            ]);
        }

        DB::beginTransaction();
        try {
            $penjualan = PenjualanModel::create([
                'user_id' => auth()->user()->user_id,
                'customer_id' => $request->customer_id,
                'penjualan_kode' => $request->penjualan_kode,
                'penjualan_tanggal' => $request->penjualan_tanggal
            ]);

            foreach ($request->barang_id as $i => $barangId) {
                $barang = BarangModel::find($barangId);
                $barang->update([
                    'stok' => $barang->stok - $request->jumlah[$i]
                ]);

                PenjualanDetailModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $barangId,
                    'harga' => $request->harga[$i],
                    'jumlah' => $request->jumlah[$i]
                ]);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data penjualan berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Gagal menyimpan data']);
        }
    }

    public function edit_ajax($id)
    {
        $penjualan = PenjualanModel::with('detail')->findOrFail($id);
        $barangs = BarangModel::all();
        $customers = CustomerModel::all();
        return view('penjualan.edit_ajax', compact('penjualan', 'barangs', 'customers'));
    }

    public function update_ajax(Request $request, $id)
    {
        $rules = [
            'customer_id' => 'required|string|max:100',
            'penjualan_kode' => "required|string|unique:t_penjualan,penjualan_kode,$id,penjualan_id",
            'barang_id.*' => 'required|exists:m_barang,barang_id',
            'harga.*' => 'required|numeric|min:0',
            'jumlah.*' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msgField' => $validator->errors()
            ]);
        }

        DB::beginTransaction();
        try {
            $penjualan = PenjualanModel::findOrFail($id);
            $penjualan->update([
                'customer_id' => $request->customer_id,
            ]);



            PenjualanDetailModel::where('penjualan_id', $id)->delete();

            foreach ($request->barang_id as $i => $barangId) {


                PenjualanDetailModel::create([
                    'penjualan_id' => $id,
                    'barang_id' => $barangId,
                    'harga' => $request->harga[$i],
                    'jumlah' => $request->jumlah[$i]
                ]);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data penjualan berhasil diperbarui']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Gagal memperbarui data']);
        }
    }

    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);
        return view('penjualan.confirm_ajax', compact('penjualan'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax()) {
            $penjualan = PenjualanModel::with('detail')->find($id);
            if ($penjualan) {

                PenjualanDetailModel::where('penjualan_id', $id)->delete();
                $penjualan->delete();

                return response()->json(['status' => true, 'message' => 'Data berhasil dihapus']);
            } else {
                return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
            }
        }

        return redirect('/');
    }

    public function export_excel(Request $request)
    {
        $penjualan = PenjualanModel::with(['user', 'customer', 'detail.barang'])
            ->whereIn('status', ['paid_off', 'completed'])
            ->withSum('detail as total_harga', DB::raw('harga * jumlah'))
            ->when($request->tahun, function ($query, $tahun) {
                $query->whereYear('penjualan_tanggal', $tahun);
            })
            ->when($request->bulan, function ($query, $bulan) {
                $query->whereMonth('penjualan_tanggal', $bulan);
            })
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Keterangan filter export
        $bulanNama = $request->bulan ? \Carbon\Carbon::create()->month($request->bulan)->locale('id')->translatedFormat('F') : 'Semua Bulan';
        $tahunNama = $request->tahun ?? 'Semua Tahun';

        $sheet->setCellValue('A1', 'Export Data Penjualan Bulan ' . $bulanNama . ' Tahun ' . $tahunNama);
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true);

        // Header kolom utama mulai dari baris ke-3
        $sheet->setCellValue('A3', 'ID');
        $sheet->setCellValue('B3', 'Kode Penjualan');
        $sheet->setCellValue('C3', 'Tanggal');
        $sheet->setCellValue('D3', 'Pembeli');
        $sheet->setCellValue('E3', 'Total Harga');
        $sheet->setCellValue('F3', 'User Pembuat');

        $row = 4;

        foreach ($penjualan as $p) {
            // Baris utama penjualan
            $sheet->setCellValue('A' . $row, $p->penjualan_id);
            $sheet->setCellValue('B' . $row, $p->penjualan_kode);
            $sheet->setCellValue('C' . $row, $p->penjualan_tanggal);
            $sheet->setCellValue('D' . $row, $p->customer->nama ?? '-');
            $sheet->setCellValue('E' . $row, $p->total_harga);
            $sheet->setCellValue('F' . $row, $p->user->nama ?? '-');
            $row++;

            // Header Detail Produk
            $sheet->setCellValue('B' . $row, 'No');
            $sheet->setCellValue('C' . $row, 'Nama Barang');
            $sheet->setCellValue('D' . $row, 'Harga');
            $sheet->setCellValue('E' . $row, 'Jumlah');
            $sheet->setCellValue('F' . $row, 'Subtotal');
            $row++;

            foreach ($p->detail as $i => $d) {
                $sheet->setCellValue('B' . $row, $i + 1);
                $sheet->setCellValue('C' . $row, $d->barang->barang_nama ?? '-');
                $sheet->setCellValue('D' . $row, $d->harga);
                $sheet->setCellValue('E' . $row, $d->jumlah);
                $sheet->setCellValue('F' . $row, $d->harga * $d->jumlah);
                $row++;
            }

            $row++; // Spasi antar penjualan
        }

        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Penjualan_' . now()->format('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }



    public function export_pdf(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? null;

        // Nama bulan dalam Bahasa Indonesia
        $bulanIndonesia = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        // Format nama bulan
        $bulanNama = $bulan ? ($bulanIndonesia[$bulan] ?? $bulan) : 'Semua Bulan';

        // Ambil data penjualan dengan filter tahun dan bulan
        $penjualan = PenjualanModel::with(['user', 'customer', 'detail.barang'])
            ->whereIn('status', ['paid_off', 'completed'])
            ->withSum('detail as total_harga', DB::raw('harga * jumlah'))
            ->when($tahun, function ($q) use ($tahun) {
                $q->whereYear('penjualan_tanggal', $tahun);
            })
            ->when($bulan, function ($q) use ($bulan) {
                $q->whereMonth('penjualan_tanggal', $bulan);
            })
            ->orderByDesc('penjualan_tanggal')
            ->get();

        // Generate PDF dari view
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penjualan.export_pdf', [
            'penjualan'  => $penjualan,
            'bulanNama'  => $bulanNama,
            'tahunNama'  => $tahun,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Laporan_Penjualan_' . ($bulan ? $bulan . '_' : '') . $tahun . '_' . now()->format('His') . '.pdf';

        return $pdf->stream($filename);
    }



    public function print_struk($id)
    {
        $penjualan = PenjualanModel::with(['user', 'detail.barang'])
            ->withSum('detail as total_harga', DB::raw('harga * jumlah'))
            ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pesanan.struk_pdf', compact('penjualan'));
        $pdf->setPaper('A7', 'portrait');
        return $pdf->stream('Struk_' . now()->format('Ymd_His') . '.pdf');


        return $pdf->stream('Struk_' . now()->format('Ymd_His') . '.pdf');
    }

    public function update_status(Request $request)
    {
        DB::beginTransaction();
        try {
            $penjualan = PenjualanModel::findOrFail($request->id);
            $penjualan->update([
                'status' => $request->status
            ]);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
