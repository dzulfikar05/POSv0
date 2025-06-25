<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\BarangModel;
use App\Models\CustomerModel;
use App\Models\SettingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class SettingController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Pengaturan Sistem',
            'list' => ['Home', 'Pengaturan Sistem']
        ];

        $page = (object) [
            'title' => ''
        ];

        $activeMenu = 'setting';

        return view('setting.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        $query = SettingModel::select(['id', 'label', 'value', 'created_at']);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)
                    ->locale('id')
                    ->translatedFormat('d F Y - H:i');
            })
            ->addColumn('aksi', function ($row) {
                return '<button onclick="modalAction(\'' . url('/setting/' . $row->id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></button>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function create_ajax()
    {
        return view('setting.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        $rules = [
            'label' => 'required|string|max:255|unique:m_setting,label',
            'value' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msgField' => $validator->errors()
            ]);
        }

        try {
            SettingModel::create([
                'label' => $request->label,
                'value' => $request->value
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data setting berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data setting.'
            ]);
        }
    }

    public function edit_ajax($id)
    {
        $setting = SettingModel::findOrFail($id);
        return view('setting.edit_ajax', compact('setting'));
    }


    public function update_ajax(Request $request, $id)
    {
        $rules = [
            'label' => 'required|string|max:255',
            'value' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msgField' => $validator->errors()
            ]);
        }

        try {
            $setting = SettingModel::findOrFail($id);
            $setting->update([
                'label' => $request->label,
                'value' => $request->value
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data setting berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data setting'
            ]);
        }
    }


    public function confirm_ajax(string $id)
    {
        $setting = SettingModel::findOrFail($id);
        return view('setting.confirm_ajax', compact('setting'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax()) {
            $setting = SettingModel::find($id);
            if ($setting) {
                $setting->delete();

                return response()->json(['status' => true, 'message' => 'Data setting berhasil dihapus']);
            } else {
                return response()->json(['status' => false, 'message' => 'Data setting tidak ditemukan']);
            }
        }

        return redirect('/');
    }
}
