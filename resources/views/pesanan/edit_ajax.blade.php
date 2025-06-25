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
                    Data yang anda cari tidak ditemukan.
                </div>
                <a href="{{ url('/penjualan') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/pesanan/' . $penjualan->penjualan_id . '/update_ajax') }}" method="POST" id="form-edit">
        @csrf @method('PUT')
        <div id="modal-master" class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Data Pesanan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">

                    {{-- Data Utama --}}
                    <div class="form-group">
                        <label>Kode Pesanan</label>
                        <input type="text" value="{{ $penjualan->penjualan_kode }}" class="form-control" disabled>
                        <input type="hidden" name="penjualan_kode" value="{{ $penjualan->penjualan_kode }}">
                        <small id="error-penjualan_kode" class="error-text text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="text" value="{{ $penjualan->penjualan_tanggal }}" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Pembeli</label>
                        <select class="form-control" disabled>
                            <option value="">-- Pilih --</option>
                            @foreach ($customers as $row)
                                <option value="{{ $row->user_id }}"
                                    {{ $row->user_id == $penjualan->customer_id ? 'selected' : '' }}>
                                    {{ $row->nama }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customer_id" value="{{ $penjualan->customer_id }}">
                        <small id="error-customer_id" class="error-text text-danger"></small>
                    </div>
                    <a href="https://wa.me/ {{ preg_replace('/[^0-9]/', '', $penjualan->customer->wa ?? '6281234567890') }} "
                        target="_blank" class="btn btn-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-whatsapp" viewBox="0 0 16 16">
                            <path
                                d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
                        </svg>
                        {{ $penjualan->customer->wa }}
                    </a>
                    <hr>
                    <h5>Detail Barang</h5>
                    <table class="table table-bordered" id="tabel-barang">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>
                                    <button type="button" class="btn btn-sm btn-success" id="tambah-baris">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualan->detail as $d)
                                <tr>
                                    <td>
                                        <select name="barang_id[]" class="form-control select-barang" required>
                                            <option value="">-- Pilih --</option>
                                            @foreach ($barangs as $b)
                                                <option value="{{ $b->barang_id }}" data-harga="{{ $b->harga }}"
                                                    {{ $b->barang_id == $d->barang_id ? 'selected' : '' }}>
                                                    {{ $b->barang_nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" name="harga[]" class="form-control harga"
                                            value="{{ number_format($d->harga, 0, ',', '.') }}" readonly></td>
                                    <td><input type="number" name="jumlah[]" class="form-control jumlah"
                                            value="{{ $d->jumlah }}" required></td>
                                    <td><input type="text" class="form-control subtotal" readonly></td>
                                    <td><button type="button" class="btn btn-sm btn-danger hapus-baris"><i
                                                class="fa fa-trash"></i></button></td>
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
                    <button type="button" onclick="onValidatePayment({{ $penjualan->penjualan_id }})" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Verifikasi Pembayaran</button>
                    <button type="button" onclick="onReject({{ $penjualan->penjualan_id }})" class="btn btn-danger btn-sm mr-3"><i class="fa fa-times"></i> Batalkan Pesanan</button>

                    <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
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

        // Handle Submit AJAX
        $('#form-edit').on('submit', function(e) {
            e.preventDefault();
            const form = this;

            // Hapus format ribuan dari semua harga
            $(form).find('input.harga').each(function() {
                const val = $(this).val();
                $(this).val(parseRibuan(val));
            });

            // Hapus format ribuan dari semua subtotal
            $(form).find('input.subtotal').each(function() {
                const val = $(this).val();
                $(this).val(parseRibuan(val));
            });

            // Hapus format ribuan dari total harga
            $('#total-harga').val(parseRibuan($('#total-harga').val()));

            // Kirim data via AJAX
            $.ajax({
                url: form.action,
                type: 'POST',
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
                    } else {
                        $('.error-text').text('');
                        if (response.msgField) {
                            $.each(response.msgField, function(key, val) {
                                $('#error-' + key).text(val[0]);
                            });
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan sistem.'
                    });
                }
            });
        });


    });

    $(document).on('change input', '.jumlah', updateTotal);

    $(document).on('change', '.select-barang', function() {
        const harga = $(this).find(':selected').data('harga') || 0;
        const row = $(this).closest('tr');
        row.find('.harga').val(formatRibuan(harga));
        row.find('.jumlah').trigger('input');
    });

    $(document).on('click', '#tambah-baris', function() {
        const newRow = `
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
        $('#tabel-barang tbody').append(newRow);
        initSelect2();
    });

    $(document).on('click', '.hapus-baris', function() {
        $(this).closest('tr').remove();
        updateTotal();
    });
</script>
