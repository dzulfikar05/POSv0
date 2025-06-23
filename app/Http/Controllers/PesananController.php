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

class PesananController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Pesanan',
            'list' => ['Home', 'Pesanan']
        ];

        $page = (object) [
            'title' => 'Daftar pesanan pelanggan yang tercatat dalam sistem'
        ];

        $activeMenu = 'pesanan';

        return view('pesanan.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        $penjualan = PenjualanModel::with('user', 'customer')
            ->select([
                'penjualan_id',
                'user_id',
                'customer_id',
                'penjualan_kode',
                'penjualan_tanggal',
                'status',
                DB::raw('(SELECT SUM(harga * jumlah) FROM t_penjualan_detail WHERE t_penjualan_detail.penjualan_id = t_penjualan.penjualan_id) as total_harga')
            ])
            ->where(function($q){
                $q->where('status','validate_payment')
                  ->orWhere('status','rejected');
            })->orderBy('penjualan_tanggal', 'desc');


        if ($request->tahun) {
            $penjualan->whereYear('penjualan_tanggal', $request->tahun);
        }

        if ($request->bulan) {
            $penjualan->whereMonth('penjualan_tanggal', $request->bulan);
        }

        return DataTables::of($penjualan->get())
            ->addIndexColumn()
            ->addColumn('user_nama', function ($stok) {
                return $stok->user->nama ?? '-';
            })
            ->addColumn('customer_nama', function ($stok) {
                return $stok->customer->nama;
            })
            ->addColumn('status_penjualan', function ($row) {
                return $row->status;
            })
            ->addColumn('customer_wa', function ($stok) {
                return '<a href="https://wa.me/' . $stok->customer->wa . '" target="_blank" class="btn btn-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                    <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                </svg>
            ' . $stok->customer->wa . '</a>';
            })
            ->addColumn('penjualan_tanggal', function ($row) {
                return \Carbon\Carbon::parse($row->tanggal)
                    ->locale('id')
                    ->translatedFormat('d F Y - H:i');
            })
            ->addColumn('total_harga', function ($row) {
                return number_format($row->total_harga ?? 0, 0, ',', '.');
            })
            ->addColumn('aksi', function ($row) {
                $btn = "";

                if ($row->status != 'rejected') {

                    $btn .= '<button onclick="onValidatePayment(' . $row->penjualan_id . ')" class="btn btn-success btn-sm"><i class="fa fa-check"></i></button> ';
                    $btn .= '<button onclick="onReject(' . $row->penjualan_id . ')" class="btn btn-danger btn-sm mr-3"><i class="fa fa-times"></i></button> ';

                    $btn .= '<button onclick="modalAction(\'' . url('/pesanan/' . $row->penjualan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm"><i class="fa fa-eye"></i></button> ';
                }

                $btn .= '<button onclick="modalAction(\'' . url('/pesanan/' . $row->penjualan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>';

                $btn .= '<a target="_blank" href="/pesanan/' . $row->penjualan_id . '/print_struk" class="btn btn-primary btn-sm mx-1"><i class="fa fa-file"></i></a>';
                return $btn;
            })
            ->rawColumns(['aksi', 'customer_wa'])
            ->make(true);
    }


    public function create_ajax()
    {
        $barangs = BarangModel::all();
        $customers = CustomerModel::all();
        return view('pesanan.create_ajax', compact('barangs', 'customers'));
    }

    public function store_ajax(Request $request)
    {
        $request->merge(['user_id' => auth()->user()->user_id]);
        $request->merge(['penjualan_tanggal' => now()->format('Y-m-d H:i:s')]);
        $request->merge(['penjualan_kode' => 'PJ' . now()->format('YmdHis')]);

        $rules = [
            'user_id' => 'required|exists:m_user,user_id',
            'customer_id' => 'required|exists:m_user,user_id',
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

        try {
            DB::beginTransaction();

            $penjualan = PenjualanModel::create([
                'user_id' => auth()->user()->user_id,
                'customer_id' => $request->customer_id,
                'penjualan_kode' => $request->penjualan_kode,
                'penjualan_tanggal' => $request->penjualan_tanggal,
                'status' => 'validate_payment'
            ]);


            $dataDetail = [];
            foreach ($request->barang_id as $i => $barangId) {
                $dataDetail[] = [
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $barangId,
                    'harga' => $request->harga[$i],
                    'jumlah' => $request->jumlah[$i]
                ];
            }

            PenjualanDetailModel::insert($dataDetail);

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
        return view('pesanan.edit_ajax', compact('penjualan', 'barangs', 'customers'));
    }

    public function update_ajax(Request $request, $id)
    {
        $rules = [
            'customer_id' => 'required|exists:m_user,user_id',
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

            foreach ($request->barang_id as $i => $barangId) {
                $barang = BarangModel::find($barangId);
                $barang->update([
                    'stok' => $barang->stok + $request->jumlah[$i]
                ]);
            }

            PenjualanDetailModel::where('penjualan_id', $id)->delete();

            foreach ($request->barang_id as $i => $barangId) {
                $barang = BarangModel::find($barangId);
                $barang->update([
                    'stok' => $barang->stok - $request->jumlah[$i]
                ]);

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
        return view('pesanan.confirm_ajax', compact('penjualan'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax()) {
            $penjualan = PenjualanModel::with('detail')->find($id);
            if ($penjualan) {
                foreach ($penjualan->detail ?? [] as $detail) {
                    $barang = BarangModel::find($detail->barang_id);
                    $barang->update([
                        'stok' => $barang->stok + $detail->jumlah
                    ]);
                }
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

            // Header detail barang
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
        $bulanNama = $bulan ? ($bulanIndonesia[$bulan] ?? $bulan) : 'Semua Bulan';

        $penjualan = PenjualanModel::with(['user', 'detail.barang'])
            ->withSum('detail as total_harga', DB::raw('harga * jumlah'))
            ->when($request->tahun, function ($q) use ($tahun) {
                $q->whereYear('penjualan_tanggal', $tahun);
            })
            ->when($request->bulan, function ($q) use ($bulan) {
                $q->whereMonth('penjualan_tanggal', $bulan);
            })
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penjualan.export_pdf', [
            'penjualan'  => $penjualan,
            'bulanNama'  => $bulanNama,
            'tahunNama'  => $tahun,
        ]);

        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Laporan_Penjualan_' . now()->format('Ymd_His') . '.pdf');
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
