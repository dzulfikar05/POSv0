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
    <form id="form-edit">
        <div id="modal-master" class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Data Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Penjualan</label>
                        <input value="{{ $penjualan->penjualan_kode }}" type="text" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input value="{{ $penjualan->penjualan_tanggal }}" type="text" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Pembeli</label>
                        <input type="text" class="form-control" readonly
                            value="{{ optional($penjualan->customer)->nama }} - {{ optional($penjualan->customer)->wa }}">
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualan->detail as $d)
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" readonly
                                            value="{{ optional($d->barang)->barang_nama }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control harga" readonly
                                            value="{{ number_format($d->harga, 0, ',', '.') }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control jumlah" readonly
                                            value="{{ $d->jumlah }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control subtotal" readonly>
                                    </td>
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
                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Tutup</button>
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
        return parseFloat(nilai.toString().replace(/\./g, '')) || 0;
    }

    function hitungSubtotal(tr) {
        let harga = parseRibuan($(tr).find('.harga').val());
        let jumlah = parseInt($(tr).find('.jumlah').val()) || 0;
        let subtotal = harga * jumlah;
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

    $(document).ready(function() {
        updateTotal();
    });
</script>
