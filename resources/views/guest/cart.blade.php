<div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
    @if ($auth?->user_id)
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Keranjang Belanja</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body " id="cart-body">
                    <div class="card my-2">
                        <div class="card-body">
                            tes
                        </div>
                    </div>
                </div>
                <div class="modal-footer action-button">
                    @if (!empty($cart))
                        <button type="button" class="btn btn-warning" onclick="updateCart()">Simpan Data</button>
                        <button type="button" class="btn btn-primary" onclick="onCheckout()">Checkout</button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    onShowCart = () => {
        let auth = '{{ $auth }}';
        if (!auth) {
            window.location.href = "{{ url('/login') }}";
            return;
        }

        let html = ``;
        let total = 0;

        $('.cart-count').text(cart.length);

        if (cart.length === 0) {
            $('.action-button').hide();

            html = `
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                <p class="text-muted mb-0">Keranjang belanja Anda kosong</p>
            </div>
        `;
        } else {
            $('.action-button').show();

            $.each(cart, function(index, val) {
                let url = val.barang?.image ?
                    `/storage/uploads/product/${val.barang?.image}` :
                    'No_Image_Available.jpg';
                let subtotal = val.harga * val.jumlah;
                total += subtotal;

                html += `
<div class="card mb-3">
    <div class="card-body">
        <div class="row align-items-center text-center text-md-left">
            <!-- Gambar Produk -->
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <img src="${url}" alt="Product Image" class="img-fluid rounded" style="object-fit: cover; width: 80px; height: 80px;">
            </div>

            <!-- Nama & Harga -->
            <div class="col-12 col-md-4 mb-2 mb-md-0">
                <p class="mb-1 font-weight-bold history-content">${val.barang?.barang_nama ?? 'Nama Barang'}</p>
                <p class="text-muted history-content" style="font-size: 0.9rem;">Harga Satuan: ${formatRupiah(val.harga)}</p>
            </div>

            <!-- Input Jumlah -->
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <div class="input-group justify-content-center">
                    <button class="btn btn-outline-secondary btn-sm history-content" onclick="decreaseQtyCart(${index})">-</button>
                    <input type="text" class="form-control form-control-sm history-content text-center mx-1" value="${val.jumlah}" readonly style="max-width: 50px;">
                    <button class="btn btn-outline-secondary btn-sm history-content" onclick="increaseQtyCart(${index})">+</button>
                </div>
            </div>

            <!-- Subtotal & Hapus -->
            <div class="col-12 col-md-3">
                <div class="d-flex justify-content-center justify-content-md-between align-items-center">
                    <span class="me-2 history-content">${formatRupiah(subtotal)}</span>
                    <button class="btn btn-link text-danger btn-sm p-0 btn-delete-card" onclick="removeItemCart(${index})" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

            `;
            });

            html += `
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Total Harga</h5>
                    <h5 class="mb-0 text-primary font-weight-bold">${formatRupiah(total)}</h5>
                </div>
            </div>
        `;
        }

        $('#cart-body').html(html);
        $('#cartModal').modal('show');

        let countLength = $('.cart-count').text();
        if (countLength == 0 || countLength == "0") {
            $('.cart-count').addClass('d-none');
        }
    }

    decreaseQtyCart = (index) => {
        if (cart[index].jumlah > 1) {
            cart[index].jumlah--;
            onShowCart();
        }
    };

    increaseQtyCart = (index) => {
        cart[index].jumlah++;
        onShowCart();
    };

    removeItemCart = (index) => {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Setelah melakukan perubahan, jangan lupa klik tombol simpan data!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                cart.splice(index, 1);
                onShowCart();
            }
        })
    };


    updateCart = () => {
        $.ajax({
            url: "{{ url('public/update_cart') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                cart: cart
            },
            success: function(res) {
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message
                    }).then(function() {
                        location.reload();
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: res.message
                    });
                }
            }
        })
    }

    onCheckout = () => {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Setelah melakukan checkout, tunggu respon dari admin melalui Whatsapp!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Checkout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('public/checkout') }}",
                    type: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message
                            }).then(function() {
                                location.reload();
                            })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: res.message
                            });
                        }
                    }
                })
            }
        })
    }
</script>
