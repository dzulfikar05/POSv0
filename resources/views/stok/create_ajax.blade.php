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
                    <input type="date" name="stok_tanggal" class="form-control" required>
                    <small class="error-text text-danger error-stok_tanggal"></small>
                </div>

                <div id="stok-wrapper">
                    <div class="stok-item border p-3 rounded mb-2">
                        <div class="form-row align-items-end">
                            <div class="form-group col-lg-2 col-md-4 mb-2">
                                <label>Item</label>
                                <input type="text" name="item[]" class="form-control item" required>
                                <small class="error-text text-danger error-barang_id"></small>
                            </div>
                            <div class="form-group col-lg-1 col-md-2 mb-2">
                                <label>Jumlah</label>
                                <input type="number" name="stok_jumlah[]" class="form-control jumlah" required>
                                <small class="error-text text-danger error-stok_jumlah"></small>
                            </div>
                            <div class="form-group col-lg-2 col-md-3 mb-2">
                                <label>Harga</label>
                                <input type="text" name="harga_total[]" class="form-control harga-total" required>
                                <small class="error-text text-danger error-harga_total"></small>
                            </div>
                            <div class="form-group col-lg-3 col-md-3 mb-2">
                                <label>Supplier</label>
                                <select name="supplier_id[]" class="form-control supplier-select" required style="width: 100%">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($supplier as $row)
                                        <option value="{{ $row->supplier_id }}">{{ $row->supplier_nama }}</option>
                                    @endforeach
                                </select>
                                <small class="error-text text-danger error-supplier_id"></small>
                            </div>
                            <div class="form-group col-lg-3 col-md-5 mb-2">
                                <label>Keterangan</label>
                                <input type="text" name="keterangan[]" class="form-control keterangan">
                                <small class="error-text text-danger error-keterangan"></small>
                            </div>
                            <div class="form-group col-lg-1 col-md-2 text-right">
                                <button type="button" class="btn btn-danger btn-sm remove-row mt-4 w-100">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-row" class="btn btn-success mt-3">+ Tambah Baris</button>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        function initSelect2(container) {
            container.find('.supplier-select').select2({
                dropdownParent: $('#stok-wrapper')
            });
        }

        initSelect2($('.stok-item').first());

        // Format ribuan
        $(document).on('input', '.harga-total', function () {
            let val = $(this).val().replace(/\D/g, '');
            $(this).val(val.replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
        });

        // Tambah baris
        $('#add-row').click(function () {
            let item = $('.stok-item').first();
            item.find('.supplier-select').select2('destroy');

            let clone = item.clone();
            clone.find('input, textarea, select').val('');
            clone.find('.error-text').text('');
            $('#stok-wrapper').append(clone);

            initSelect2(item);
            initSelect2(clone);
        });

        // Hapus baris
        $(document).on('click', '.remove-row', function () {
            if ($('.stok-item').length > 1) {
                $(this).closest('.stok-item').remove();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Minimal 1 baris',
                    text: 'Harus ada setidaknya 1 baris stok.'
                });
            }
        });

        // Submit
        $('#form-tambah').submit(function (e) {
            e.preventDefault();

            $('.harga-total').each(function () {
                let raw = $(this).val().replace(/\./g, '');
                $(this).val(raw);
            });

            $.ajax({
                url: this.action,
                type: this.method,
                data: $(this).serialize(),
                success: function (res) {
                    if (res.status) {
                        $('#myModal').modal('hide');
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message });
                        tableStok.ajax.reload();
                    } else {
                        $('.error-text').text('');
                        $.each(res.msgField, function (i, groupErrors) {
                            $.each(groupErrors, function (field, messages) {
                                $('.stok-item').eq(i).find('.error-' + field).text(messages[0]);
                            });
                        });
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                    }
                }
            });
        });
    });
</script>
