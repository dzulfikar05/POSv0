<form action="{{ url('/stok/ajax') }}" method="POST" id="form-tambah">
    @csrf

    <div id="modal-master" class="modal-dialog modal-xl" role="document">
        <div class="modal-content modal-xl">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Stok</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="stok_tanggal" id="stok_tanggal" class="form-control" required>
                    <small class="error-text form-text text-danger" id="error-stok_tanggal"></small>
                </div>
                <div id="stok-wrapper">
                    <div class="stok-item mb-3">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Item</label>
                                <input type="text" name="item[]" id="item" class="form-control" required>
                                <small class="error-text form-text text-danger" id="error-barang_id"></small>
                            </div>

                            <div class="form-group col-md-1">
                                <label>Jumlah</label>
                                <input type="number" name="stok_jumlah[]" id="stok_jumlah" class="form-control" required>
                                <small class="error-text form-text text-danger" id="error-stok_jumlah"></small>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Harga Total</label>
                                <input type="number" name="harga_total[]" id="harga_total" class="form-control" required>
                                <small class="error-text form-text text-danger" id="error-harga_total"></small>
                            </div>

                            <div class="form-group col-md-2">
                                <label>Supplier</label>
                                <select name="supplier_id[]" id="supplier_id" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach($supplier as $row)
                                        <option value="{{ $row->supplier_id }}">{{ $row->supplier_nama }}</option>
                                    @endforeach
                                </select>
                                <small class="error-text form-text text-danger" id="error-supplier_id"></small>
                            </div>

                            <div class="form-group col-md-3">
                                <label>Keterangan</label>
                                <textarea name="keterangan[]" id="keterangan" class="form-control"></textarea>
                                <small class="error-text form-text text-danger" id="error-keterangan"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-row" class="btn btn-success">Tambah Baris</button>
            </div>

            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function () {
    $('#add-row').click(function () {
        let clone = $('.stok-item').first().clone();
        clone.find('input, select, textarea').val('');
        clone.find('.error-text').text('');
        $('#stok-wrapper').append(clone);
    });

    $("#form-tambah").submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: this.action,
            type: this.method,
            data: $(this).serialize(),
            success: function (response) {
                if (response.status) {
                    $('#myModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    });
                    tableStok.ajax.reload();
                } else {
                    $('.error-text').text('');
                    $.each(response.msgField, function (i, groupErrors) {
                        $.each(groupErrors, function (field, messages) {
                            $('.stok-item').eq(i).find(`#error-${field}`).text(messages[0]);
                        });
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: response.message
                    });
                }
            }
        });
    });
});
</script>
