<form action="{{ url('/penjualan/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pembeli</label>
                    <input type="text" name="pembeli" class="form-control" required>
                    <small id="error-pembeli" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Tanggal Penjualan</label>
                    <input type="text" class="form-control" disabled value="{{ date('Y-m-d H:i:s') }}">
                </div>
                <hr>
                <h5>Detail Barang</h5>
                <table class="table table-bordered" id="detail-barang">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right font-weight-bold">Total</td>
                            <td><input type="text" class="form-control" id="total-harga" readonly></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-sm btn-info" id="tambah-barang">Tambah Barang</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
<script>
    const barangs = @json($barangs);

    function hitungTotal() {
        let total = 0;
        $('#detail-barang tbody tr').each(function() {
            const subtotal = parseFloat($(this).find('.subtotal').val()) || 0;
            total += subtotal;
        });
        $('#total-harga').val(total);
    }

    function tambahBaris() {
        const index = $('#detail-barang tbody tr').length;

        let barangOptions = '<option value="">-- Pilih --</option>';
        barangs.forEach(barang => {
            barangOptions += `<option value="${barang.barang_id}" data-harga="${barang.harga_jual}"  data-stok="${barang.stok}">${barang.barang_nama}</option>`;
        });

        const row = `
            <tr>
                <td>
                    <select name="barang_id[]" class="form-control barang-select">
                        ${barangOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="harga[]" class="form-control harga" readonly>
                </td>
                <td>
                    <input type="number" name="jumlah[]" class="form-control jumlah" min="1" value="1">
                </td>
                <td>
                    <input type="number" class="form-control subtotal" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger hapus-baris">Hapus</button>
                </td>
            </tr>`;

        $('#detail-barang tbody').append(row);
    }

    $(document).ready(function() {
        // function button add
        $('#tambah-barang').click(tambahBaris);

        // onchange barang dipilih, akan mengisi harga
        $('#detail-barang').on('change', '.barang-select', function() {
            const harga = $(this).find(':selected').data('harga') || 0;
            const row = $(this).closest('tr');
            row.find('.harga').val(harga);
            row.find('.jumlah').trigger('input');
        });

        // menghitung sub total
        $('#detail-barang').on('input', '.jumlah', function() {
            const row = $(this).closest('tr');
            const stok = row.find('.barang-select').find(':selected').data('stok') || 0;
            const jumlah = parseInt(row.find('.jumlah').val()) || 0;

            if (jumlah > stok) {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Jumlah barang melebihi stok' });
                row.find('.jumlah').val(stok);
            }

            const harga = parseFloat(row.find('.harga').val()) || 0;
            const subtotal = harga * jumlah;
            row.find('.subtotal').val(subtotal);
            hitungTotal();
        });

        // delete row
        $('#detail-barang').on('click', '.hapus-baris', function() {
            $(this).closest('tr').remove();
            hitungTotal();
        });

        $("#form-tambah").validate({
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message });
                            tablePenjualan.ajax.reload();
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: response.message });
                        }
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
