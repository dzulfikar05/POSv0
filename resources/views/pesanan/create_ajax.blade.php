<form action="{{ url('/pesanan/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Pesanan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Customer</label>
                    <select name="customer_id" id="customer_id" class="form-control customer_id" required
                        style="width: 100%">
                        <option value="">-- Pilih --</option>
                        @foreach ($customers as $row)
                            <option value="{{ $row->user_id }}">{{ $row->nama }} - {{ $row->wa }}</option>
                        @endforeach
                    </select>
                    <small class="error-text form-text text-danger" id="error-customer_id"></small>
                </div>

                <div class="form-group">
                    <label>Tanggal Pesanan</label>
                    <input type="text" class="form-control" disabled
                        value="{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY HH:mm') }}">
                </div>

                <hr>
                <h5>Detail Produk</h5>
                <table class="table table-bordered" id="detail-barang">
                    <thead>
                        <tr>
                            <th>Produk</th>
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
                <button type="button" class="btn btn-sm btn-info" id="tambah-barang">Tambah Produk</button>
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

    function formatRibuan(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function hitungTotal() {
        let total = 0;
        $('#detail-barang tbody tr').each(function() {
            const subtotal = parseFloat($(this).find('.subtotal').val().replace(/\./g, '')) || 0;
            total += subtotal;
        });
        $('#total-harga').val(formatRibuan(total));
    }

    function tambahBaris() {
        let barangOptions = '<option value="">-- Pilih --</option>';
        barangs.forEach(barang => {
            barangOptions +=
                `<option value="${barang.barang_id}" data-harga="${barang.harga}">${barang.barang_nama}</option>`;
        });

        const row = `
            <tr>
                <td>
                    <select name="barang_id[]" class="form-control barang-select">
                        ${barangOptions}
                    </select>
                </td>
                <td>
                    <input type="text" name="harga[]" class="form-control harga" readonly>
                </td>
                <td>
                    <input type="number" name="jumlah[]" class="form-control jumlah" min="1" value="1">
                </td>
                <td>
                    <input type="text" class="form-control subtotal" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger hapus-baris">Hapus</button>
                </td>
            </tr>`;

        $('#detail-barang tbody').append(row);

        // Inisialisasi Select2 untuk dropdown yang baru
        $('#detail-barang tbody .barang-select').last().select2({
            dropdownParent: $('#form-tambah')
        });
    }

    $(document).ready(function() {
        // Inisialisasi awal select2 untuk customer
        $('.customer_id').select2({
            dropdownParent: $('#form-tambah')
        });

        // Tambah baris produk
        $('#tambah-barang').on('click', tambahBaris);

        // Ketika produk dipilih
        $('#detail-barang').on('change', '.barang-select', function() {
            const harga = $(this).find(':selected').data('harga') || 0;
            const row = $(this).closest('tr');
            row.find('.harga').val(formatRibuan(harga));
            row.find('.jumlah').trigger('input');
        });

        // Perubahan jumlah
        $('#detail-barang').on('input', '.jumlah', function() {
            const row = $(this).closest('tr');
            const jumlah = parseInt(row.find('.jumlah').val()) || 0;
            const harga = parseFloat((row.find('.harga').val() || '0').replace(/\./g, '')) || 0;
            const subtotal = jumlah * harga;
            row.find('.subtotal').val(formatRibuan(subtotal));
            hitungTotal();
        });

        // Hapus baris
        $('#detail-barang').on('click', '.hapus-baris', function() {
            $(this).closest('tr').remove();
            hitungTotal();
        });

        // Submit AJAX
        $('#form-tambah').validate({
            submitHandler: function(form) {
                // Hapus format titik ribuan sebelum submit
                $('#detail-barang .harga, #detail-barang .subtotal').each(function() {
                    $(this).val($(this).val().replace(/\./g, ''));
                });
                $('#total-harga').val($('#total-harga').val().replace(/\./g, ''));

                $.ajax({
                    url: form.action,
                    method: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            tablePesanan.ajax.reload();
                            setTimeout(() => {
                                tablePesanan.columns.adjust().draw();
                            }, 200);
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
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
