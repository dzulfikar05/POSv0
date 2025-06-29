<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use App\Models\SupplierModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yajra\DataTables\Facades\DataTables;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Data Stok Pengeluaran Pembelanjaan',
            'list' => ['Home', 'Stok Pembelanjaan']
        ];

        $page = (object)[
            'title' => ''
        ];

        $activeMenu = 'stok';

        return view('stok.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        $stok = StokModel::select(
            't_stok.*',
            'm_user.nama as user_nama',
            'm_supplier.supplier_nama'
        )
            ->leftJoin('m_user', 'm_user.user_id', '=', 't_stok.user_id')
            ->leftJoin('m_supplier', 'm_supplier.supplier_id', '=', 't_stok.supplier_id');

        if ($request->tahun) {
            $stok->whereYear('stok_tanggal', $request->tahun);
        }

        if ($request->bulan) {
            $stok->whereMonth('stok_tanggal', $request->bulan);
        }

        return DataTables::of($stok)
            ->addIndexColumn()
            ->editColumn('user_nama', function ($stok) {
                return $stok->user_nama ?? '-';
            })
            ->editColumn('supplier_nama', function ($stok) {
                return $stok->supplier_nama ?? '-';
            })
            ->addColumn('aksi', function ($stok) {
                return '
                <button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></button>
                <button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
            ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $supplier = SupplierModel::all();

        return view('stok.create_ajax', compact('supplier'));
    }

    public function store_ajax_single(Request $request)
    {
        $request->merge(['user_id' => auth()->user()->user_id]);

        if ($request->ajax()) {
            $rules = [
                'barang_id' => 'required|exists:m_barang,barang_id',
                'user_id' => 'required|exists:m_user,user_id',
                'stok_tanggal' => 'required|date',
                'stok_jumlah' => 'required|integer',
                'supplier_id' => 'required|exists:m_supplier,supplier_id',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msgField' => $validator->errors()
                ]);
            }

            $barang = BarangModel::find($request->barang_id);
            $barang->update([
                'stok' => $barang->stok + $request->stok_jumlah
            ]);

            StokModel::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil disimpan'
            ]);
        }
        return redirect('/');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax()) {

            $user_id = auth()->user()->user_id;
            $items = $request->item;
            $jumlahs = $request->stok_jumlah;
            $tanggal = $request->stok_tanggal;
            $suppliers = $request->supplier_id;
            $hargas = $request->harga_total;
            $keterangans = $request->keterangan;

            $errors = [];

            foreach ($items as $i => $item) {
                $data = [
                    'item' => $item,
                    'stok_jumlah' => $jumlahs[$i],
                    'stok_tanggal' => $tanggal . date(' H:i:s'),
                    'supplier_id' => $suppliers[$i],
                    'harga_total' => $hargas[$i] ?? '',
                    'keterangan' => $keterangans[$i] ?? '',
                    'user_id' => $user_id
                ];

                $validator = Validator::make($data, [
                    'item' => 'required|string',
                    'stok_jumlah' => 'required|integer|min:1',
                    'harga_total' => 'required|integer',
                    'stok_tanggal' => 'required|date',
                    'supplier_id' => 'required|exists:m_supplier,supplier_id',
                    'user_id' => 'required|exists:m_user,user_id',
                    'keterangan' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    $errors[$i] = $validator->errors();
                    continue;
                }

                StokModel::create($data);
            }

            if (!empty($errors)) {
                return response()->json([
                    'status' => false,
                    'msgField' => $errors,
                    'message' => 'Beberapa data gagal divalidasi'
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Semua data stok berhasil disimpan'
            ]);
        }

        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $stok = StokModel::find($id);
        $supplier = SupplierModel::all();

        return view('stok.edit_ajax', compact('stok', 'supplier'));
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax()) {
            $rules = [
                'item' => 'required|string',
                'stok_jumlah' => 'required|integer|min:1',
                'harga_total' => 'required|integer',
                'supplier_id' => 'required|exists:m_supplier,supplier_id',
                'keterangan' => 'nullable|string',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msgField' => $validator->errors()
                ]);
            }

            $stok = StokModel::find($id);
            if ($stok) {
                $stok->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil diperbarui'
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $stok = StokModel::find($id);
        return view('stok.confirm_ajax', compact('stok'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax()) {

            $stok = StokModel::find($id);
            if ($stok) {

                $stok->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil dihapus'
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return redirect('/');
    }

    public function import()
    {
        return view('stok.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax()) {
            $rules = [
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_stok');
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];
            foreach ($data as $index => $row) {
                if ($index > 1) {
                    $insert[] = [
                        'item' => $row['A'],
                        'user_id' => $row['B'],
                        'stok_tanggal' => $row['C'],
                        'stok_jumlah' => $row['D'],
                        'supplier_id' => $row['E'],
                        'created_at' => now()
                    ];
                }
            }

            if (!empty($insert)) {
                StokModel::insertOrIgnore($insert);
                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil diimport'
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Data kosong'
            ]);
        }

        return redirect('/');
    }

    public function export_excel(Request $request)
    {
        $stok = StokModel::with(['user', 'supplier'])
            ->when($request->tahun, function ($query, $tahun) {
                $query->whereYear('stok_tanggal', $tahun);
            })
            ->when($request->bulan, function ($query, $bulan) {
                $query->whereMonth('stok_tanggal', $bulan);
            })->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

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

        $tahun = $request->tahun ?? 'Semua';
        $bulan = $request->bulan ? ($bulanIndonesia[$request->bulan] ?? $request->bulan) : 'Semua';

        // Keterangan filter
        $sheet->setCellValue('A1', "Data Stok Tahun: $tahun");
        $sheet->setCellValue('A2', "Bulan: $bulan");

        // Header kolom (mulai baris 4)
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Tanggal');
        $sheet->setCellValue('C4', 'Nama Item');
        $sheet->setCellValue('D4', 'Jumlah');
        $sheet->setCellValue('E4', 'Supplier');
        $sheet->setCellValue('F4', 'Keterangan');
        $sheet->setCellValue('G4', 'User');

        // Isi data
        $row = 5;
        foreach ($stok as $index => $s) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, \Carbon\Carbon::parse($s->stok_tanggal)->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $s->item ?? '-');
            $sheet->setCellValue('D' . $row, $s->stok_jumlah);
            $sheet->setCellValue('E' . $row, $s->supplier->supplier_nama ?? '-');
            $sheet->setCellValue('F' . $row, $s->keterangan ?? '');
            $sheet->setCellValue('G' . $row, $s->user->nama ?? '-');
            $row++;
        }

        // Auto size kolom
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Export
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Stok_' . now()->format('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }


    public function export_pdf(Request $request)
    {
        $stok = StokModel::with(['user', 'supplier'])
            ->when($request->tahun, function ($query, $tahun) {
                $query->whereYear('stok_tanggal', $tahun);
            })
            ->when($request->bulan, function ($query, $bulan) {
                $query->whereMonth('stok_tanggal', $bulan);
            })->get();

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

        $tahun = $request->tahun ?? 'Semua';
        $bulan = $request->bulan ? ($bulanIndonesia[$request->bulan] ?? $request->bulan) : 'Semua';

        $pdf = Pdf::loadView('stok.export_pdf', compact('stok', 'tahun', 'bulan'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Data_Stok_' . now()->format('Y-m-d_His') . '.pdf');
    }


    public function rekap_per_bulan(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        // Inisialisasi nama bulan
        $bulanIndonesia = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        // Ambil data stok masuk berdasarkan tahun yang dipilih
        $stokPerBulan = DB::table('t_stok')
            ->selectRaw('MONTH(stok_tanggal) as bulan, SUM(harga_total) as total_harga')
            ->whereYear('stok_tanggal', $tahun)
            ->groupByRaw('MONTH(stok_tanggal)')
            ->pluck('total_harga', 'bulan'); // key: bulan, value: total_harga

        // Format data agar semua bulan tetap ada meskipun tidak ada transaksi
        $data = [];
        foreach ($bulanIndonesia as $bulan => $namaBulan) {
            $data[] = [
                'bulan' => $bulan,
                'bulan_nama' => $namaBulan,
                'total_harga' => round($stokPerBulan[$bulan] ?? 0, 2),
            ];
        }

        return response()->json($data);
    }
}
