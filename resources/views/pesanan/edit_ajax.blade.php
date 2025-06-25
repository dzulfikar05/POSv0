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
    @php
    $isRejected = $penjualan->status === 'rejected';
@endphp

<form action="{{ url('/pesanan/' . $penjualan->penjualan_id . '/update_ajax') }}" method="POST" id="form-edit">
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
                    <input type="text" value="{{ $penjualan->penjualan_kode }}" class="form-control" disabled>
                    <input type="hidden" name="penjualan_kode" value="{{ $penjualan->penjualan_kode }}">
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
                            <option value="{{ $row->user_id }}" {{ $row->user_id == $penjualan->customer_id ? 'selected' : '' }}>
                                {{ $row->nama }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="customer_id" value="{{ $penjualan->customer_id }}">
                </div>

                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $penjualan->customer->wa ?? '6281234567890') }}" target="_blank" class="btn btn-success">
                    <i class="fab fa-whatsapp"></i> {{ $penjualan->customer->wa }}
                </a>

                <hr>
                <h5>Detail Produk</h5>
                <table class="table table-bordered" id="tabel-barang">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>
                                @if (!$isRejected)
                                <button type="button" class="btn btn-sm btn-success" id="tambah-baris">
                                    <i class="fa fa-plus"></i>
                                </button>
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penjualan->detail as $d)
                            <tr>
                                <td>
                                    <select name="barang_id[]" class="form-control select-barang" required {{ $isRejected ? 'disabled' : '' }}>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($barangs as $b)
                                            <option value="{{ $b->barang_id }}" data-harga="{{ $b->harga }}" {{ $b->barang_id == $d->barang_id ? 'selected' : '' }}>
                                                {{ $b->barang_nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="harga[]" class="form-control harga" value="{{ number_format($d->harga, 0, ',', '.') }}" readonly></td>
                                <td><input type="number" name="jumlah[]" class="form-control jumlah" value="{{ $d->jumlah }}" {{ $isRejected ? 'disabled' : '' }} required></td>
                                <td><input type="text" class="form-control subtotal" readonly></td>
                                <td>
                                    @if (!$isRejected)
                                    <button type="button" class="btn btn-sm btn-danger hapus-baris"><i class="fa fa-trash"></i></button>
                                    @endif
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
                @if (!$isRejected)
                    <button type="button" onclick="onValidatePayment({{ $penjualan->penjualan_id }})" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Verifikasi Pembayaran</button>
                    <button type="button" onclick="onReject({{ $penjualan->penjualan_id }})" class="btn btn-danger btn-sm mr-3"><i class="fa fa-times"></i> Batalkan Pesanan</button>
                @endif
                <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">Tutup</button>
                @if (!$isRejected)
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                @endif
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
                        setTimeout(() => {
                            tablePesanan.columns.adjust().draw();
                        }, 200);
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
<script>
    const isRejected = @json($isRejected);
    if (isRejected) {
        $('#form-edit input, #form-edit select, #form-edit textarea, #form-edit button').prop('disabled', true);
        $('#form-edit button[data-dismiss="modal"]').prop('disabled', false); // biar tetap bisa tutup modal
    }
</script>
