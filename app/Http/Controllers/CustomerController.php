<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\CustomerModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Customer',
            'list' => ['Home', 'Customer']
        ];

        $page = (object) [
            'title' => 'Daftar customer yang terdaftar dalam sistem'
        ];

        $activeMenu = 'customer';

        $level = LevelModel::all();

        return view('customer.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }


    public function list(Request $request)
    {
        $users = CustomerModel::with('level');

        if ($request->level_id) {
            $users->where('level_id', $request->level_id);
        }

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('aksi', function ($user) {
                $btn = '<button onclick="modalAction(\'' . url('/customer/' . $user->user_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/customer/' . $user->user_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('customer.create_ajax')
            ->with('level', $level);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'username' => 'required|string|min:3|unique:m_user,username',
                'nama' => 'required|string|max:100',
                'password' => 'required|min:6',
                'jk' => 'required',
                'alamat' => 'required|string',
                'wa' => 'required|integer',
                'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            $photoName = null;

            if ($request->hasFile('photo')) {

                $file = $request->file('photo');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/uploads/photo', $filename);
                unset($request['photo']);
                $photoName = $filename;
            }

            CustomerModel::create([
                'level_id' => 2,
                'username' => $request->username,
                'nama' => $request->nama,
                'jk' => $request->jk,
                'alamat' => $request->alamat,
                'wa' => $request->wa,
                'password' => bcrypt($request->password),
                'photo' => $photoName,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil disimpan'
            ]);
        }

        return redirect('/');
    }


    public function edit_ajax(string $id)
    {
        $user = CustomerModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('customer.edit_ajax', ['user' => $user, 'level' => $level]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'username' => 'required|max:20|unique:m_user,username,' . $id . ',user_id',
                'nama'    => 'required|max:100',
                'password' => 'nullable|min:6|max:20',
                'jk' => 'required',
                'alamat' => 'required|string',
                'wa' => 'required|integer',
                'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'msgField' => $validator->errors()
                ]);
            }

            $user = CustomerModel::find($id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            if (!$request->filled('password')) {
                $request->request->remove('password');
            } else {
                $request->merge([
                    'password' => bcrypt($request->password)
                ]);
            }

            $params = $request->all();

            if ($request->hasFile('photo')) {
                if ($user->photo && Storage::exists('public/uploads/photo/' . $user->photo)) {
                    Storage::delete('public/uploads/photo/' . $user->photo);
                }

                $file = $request->file('photo');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/uploads/photo', $filename);
                if(isset($params['photo'])) unset($params['photo']);
                $params['photo'] = $filename;
            }

            if(isset($params['_token'])) unset($params['_token']);
            if(isset($params['_method'])) unset($params['_method']);

            CustomerModel::where('user_id', $id)->update($params);
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diupdate'
            ]);
        }

        return redirect('/');
    }


    public function confirm_ajax(string $id)
    {
        $user = CustomerModel::find($id);

        return view('customer.confirm_ajax', ['user' => $user]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $user = CustomerModel::find($id);
            if ($user) {
                $user->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }

        return redirect('/');
    }

    public function import()
    {
        return view('customer.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_user' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
            $file = $request->file('file_user');

            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            $data = $sheet->toArray(null, false, true, true);

            $insert = [];
            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    if ($baris > 1) {
                        $insert[] = [
                            'nama' => $value['A'],
                            'username' => $value['B'],
                            'jk' => $value['C'],
                            'alamat' => $value['D'],
                            'wa' => $value['E'],
                            'level_id' => 2,
                            'password' => Hash::make("password_".$value['B']),
                            'created_at' => now(),
                        ];
                    }
                }

                if (count($insert) > 0) {
                    CustomerModel::insertOrIgnore($insert);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport'
                ]);
            }
        }
        return redirect('/');
    }

    public function export_excel()
    {
        $user = CustomerModel::with('level')
            ->orderBy('user_id')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Username');
        $sheet->setCellValue('D1', 'Jenis Kelamin');
        $sheet->setCellValue('E1', 'Alamat');
        $sheet->setCellValue('F1', 'Nomor Whatsapp');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;

        foreach ($user as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->nama);
            $sheet->setCellValue('C' . $baris, $value->username);
            $sheet->setCellValue('D' . $baris, $value->jk == 'male'?"Laki-laki":"Perempuan");
            $sheet->setCellValue('E' . $baris, $value->alamat);
            $sheet->setCellValue('F' . $baris, $value->wa);
            $baris++;
            $no++;
        }

        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Customer');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Customer ' . date('Y-m-d H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $user = CustomerModel::with('level')
        ->orderBy('user_id')
        ->get();

        $pdf = Pdf::loadView('customer.export_pdf', ['user' => $user]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", false);
        $pdf->render();

        return $pdf->stream('Data Customer ' . date('Y-m-d H:i:s') . '.pdf');
    }

}
