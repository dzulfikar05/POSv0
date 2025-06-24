@empty($penjualan)
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Kesalahan</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                Data yang anda cari tidak ditemukan
            </div>
            <a href="{{ url('/penjualan') }}" class="btn btn-warning">Kembali</a>
        </div>
    </div>
</div>
@else
<form action="{{ url('/penjualan/' . $penjualan->penjualan_id . '/update_ajax') }}" method="POST" id="form-edit">
    @csrf @method('PUT')
    <div id="modal-master" class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Data Pesanan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Kode Pesanan</label>
                    <input value="{{ $penjualan->penjualan_kode }}" type="text" name="penjualan_kode" class="form-control" required>
                    <small id="error-penjualan_kode" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Tanggal</label>
                    <input value="{{ $penjualan->penjualan_tanggal }}" type="text" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Pembeli</label>
                    <select name="customer_id" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        @foreach ($customers as $row)
                        <option value="{{ $row->user_id }}" {{ $row->user_id == $penjualan->customer_id ? 'selected' : '' }}>
                            {{ $row->nama }} - {{ $row->wa }}
                        </option>
                        @endforeach
                    </select>
                    <small id="error-customer_id" class="error-text text-danger"></small>
                </div>

                <hr>
                <h5>Detail Barang</h5>
                <table class="table table-bordered" id="tabel-barang">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th><button type="button" class="btn btn-sm btn-success" id="tambah-baris"><i class="fa fa-plus"></i></button></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penjualan->detail as $d)
                        <tr>
                            <td>
                                <select name="barang_id[]" class="form-control select-barang" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($barangs as $b)
                                    <option value="{{ $b->barang_id }}" data-harga="{{ $b->harga }}" {{ $b->barang_id == $d->barang_id ? 'selected' : '' }}>
                                        {{ $b->barang_nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="harga[]" class="form-control harga" value="{{ number_format($d->harga, 0, ',', '.') }}" readonly></td>
                            <td><input type="number" name="jumlah[]" class="form-control jumlah" value="{{ $d->jumlah }}" required></td>
                            <td><input type="text" class="form-control subtotal" readonly></td>
                            <td><button type="button" class="btn btn-sm btn-danger hapus-baris"><i class="fa fa-trash"></i></button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="form-group">
                    <label>Total Harga</label>
                    <input type="text" id="total-harga" class="form-control" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
@endempty

<script>
    function formatRibuan(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function parseRibuan(nilai) {
        return parseFloat(nilai.replace(/\./g, "")) || 0;
    }

    function hitungSubtotal(tr) {
        const harga = parseRibuan($(tr).find('.harga').val());
        const jumlah = parseInt($(tr).find('.jumlah').val()) || 0;
        const subtotal = harga * jumlah;
        $(tr).find('.subtotal').val(formatRibuan(subtotal));
        return subtotal;
    }

    function updateTotal() {
        let total = 0;
        $('#tabel-barang tbody tr').each(function() {
            total += hitungSubtotal(this);
        });
        $('#total-harga').val(formatRibuan(total));
    }

    function initSelect2() {
        $('.select-barang').select2({
            placeholder: "-- Pilih --",
            width: '100%',
            dropdownParent: $('.modal-content')
        });
    }

    $(document).ready(function() {
        updateTotal();
        initSelect2();
    });

    $(document).on('change input', '.jumlah', function() {
        updateTotal();
    });

    $(document).on('change', '.select-barang', function() {
        const harga = $(this).find(':selected').data('harga') || 0;
        const formattedHarga = formatRibuan(harga);
        const row = $(this).closest('tr');
        row.find('.harga').val(formattedHarga);
        row.find('.jumlah').trigger('input');
    });

    $(document).on('click', '#tambah-baris', function() {
        const barisBaru = `
        <tr>
            <td>
                <select name="barang_id[]" class="form-control select-barang" required>
                    <option value="">-- Pilih --</option>
                    @foreach ($barangs as $b)
                        <option value="{{ $b->barang_id }}" data-harga="{{ $b->harga }}">{{ $b->barang_nama }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="text" name="harga[]" class="form-control harga" readonly></td>
            <td><input type="number" name="jumlah[]" class="form-control jumlah" value="1" required></td>
            <td><input type="text" class="form-control subtotal" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger hapus-baris"><i class="fa fa-trash"></i></button></td>
        </tr>`;
        $('#tabel-barang tbody').append(barisBaru);
        initSelect2(); // inisialisasi ulang untuk select baru
    });

    $(document).on('click', '.hapus-baris', function() {
        $(this).closest('tr').remove();
        updateTotal();
    });
</script>
