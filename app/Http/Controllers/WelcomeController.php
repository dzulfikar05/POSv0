<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\PenjualanDetailModel;
use App\Models\PenjualanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    public function index()
    {
        $penjualan = null;
        if (Auth::user()) {
            $penjualan = PenjualanModel::with('detail.barang')->select('penjualan_id')->where('customer_id', Auth::user()->user_id)->where('status', 'ordered')->first();
        }

        return view("guest.index", [
            'auth' => Auth::user(),
            'title' => 'POS v0',
            'kategori' => KategoriModel::all(),
            'cart' => $penjualan?->detail ? json_encode($penjualan->detail) : '[]'
        ]);
    }

    public function banner()
    {
        $itemTerlaris = PenjualanDetailModel::with(['barang', 'penjualan'])
            ->whereRelation('barang', 'image', '!=', null)
            ->get()
            ->groupBy('barang_id')
            ->map(function ($group) {
                return [
                    'barang_id' => $group->first()->barang->barang_id ?? '-',
                    'barang_nama' => $group->first()->barang->barang_nama ?? '-',
                    'image' => $group->first()->barang->image ?? '-',
                    'total' => $group->sum('jumlah')
                ];
            })
            ->sortByDesc('total')
            ->take(3)
            ->values();

        return json_encode($itemTerlaris);
    }

    public function product(Request $request)
    {

        $query = BarangModel::query();
        if ($request->filled('category_id')) {
            $query->where('kategori_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->whereRaw('LOWER(barang_nama) LIKE ?', ["%{$search}%"]);
        }
        $barang = $query->paginate(9);

        return response()->json([
            'data' => $barang->items(),
            'current_page' => $barang->currentPage(),
            'last_page' => $barang->lastPage(),
        ]);
    }

    public function add_cart(Request $request)
    {
        try {
            DB::beginTransaction();
            $transaction = null;
            $checkExitingTra = PenjualanModel::where('customer_id', Auth::user()->user_id)->where('status', 'ordered')->first();
            if ($checkExitingTra != null) {
                $transaction = $checkExitingTra;
            } else {
                $transaction = PenjualanModel::create([
                    'user_id' => Auth::user()->user_id,
                    'customer_id' => Auth::user()->user_id,
                    'penjualan_kode' => 'PJ' . now()->format('YmdHis'),
                    'penjualan_tanggal' => date('Y-m-d H:i:s'),
                    'status' => 'ordered'
                ]);
            }

            PenjualanDetailModel::create([
                'penjualan_id' => $transaction->penjualan_id,
                'barang_id' => $request->barang_id,
                'harga' => $request->harga,
                'jumlah' => $request->jumlah,
            ]);

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Data berhasil ditambahkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update_cart(Request $request)
    {
        try {
            DB::beginTransaction();
            $params = [];
            $penjualan_id = $request->all()['cart'][0]['penjualan_id'];
            foreach ($request->all()['cart'] as $key => $value) {
                $params[] = [
                    'penjualan_id' => $value['penjualan_id'],
                    'barang_id' => $value['barang_id'],
                    'harga' => $value['harga'],
                    'jumlah' => $value['jumlah'],
                ];
            }

            PenjualanDetailModel::where('penjualan_id', $penjualan_id)->delete();
            PenjualanDetailModel::insert($params);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function checkout(Request $request)
    {
        try {
            DB::beginTransaction();
            $transaction = PenjualanModel::where('customer_id', Auth::user()->user_id)->where('status', 'ordered')->first();

            $transaction->update([
                'status' => 'validate_payment',
            ]);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Berhasil melakukan checkout']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function history(Request $request)
    {
        $perPage = 5;
        $page    = $request->input('page', 1);

        $query = PenjualanModel::with('detail.barang')
            ->where('status', '!=', 'ordered')
            ->where('customer_id', Auth::user()->user_id)
            ->orderBy('penjualan_tanggal', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data'         => $query->items(),
            'current_page' => $query->currentPage(),
            'last_page'    => $query->lastPage(),
        ]);
    }
}
