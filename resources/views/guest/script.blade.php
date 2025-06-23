<script>
    let currentPage = 1;
    const itemsPerPage = 9;
    const cart = {!! $cart !!};

    $(() => {
        getBannerData();
        getProductData(currentPage);
        if(cart.length > 0) {
            $('.cart-count').text(cart.length);
        }
    });

    $('#categoryFilter').on('change', () => getProductData(1));

    $('#search-input').on('keyup', function() {
        setTimeout(() => {
            getProductData(1);
        }, 300);
    });
    getBannerData = () => {
        $.ajax({
            url: "{{ url('guest/banner') }}",
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                let html = ``;
                for (let i = 0; i < data.length; i++) {
                    html += `
                        <div class="carousel-item ${ i == 0 ? 'active' : ''}">
                            <div class="container py-5">
                                <div class="row align-items-center">
                                    <div class="col-md-12 text-center position-relative">
                                        <img src="{{ asset('storage/uploads/product/${data[i].image}') }}" alt="" class="img-fluid" style="width: 100% ; height: 500px; object-fit: cover">
                                        <div class="text-overlay p-3 text-white">
                                            <h3 class="mb-1">${data[i].barang_nama}</h3>
                                            <p>${data[i]?.deskripsi ?? ''}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                $('.carousel-inner').html(html);
            }
        })
    }

    formatRupiah = (angka) => {
        if (!angka) return 'Rp 0';
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    getProductData = (page = 1) => {
        const categoryId = $('#categoryFilter').val();
        const searchTerm = $('#search-input').val().trim();

        $.ajax({
            url: "{{ url('guest/product') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                page: page,
                category_id: categoryId,
                search: searchTerm
            },
            beforeSend: function() {
                $('#loading').show();
                $('.product-list').hide();
                $('#pagination').hide();
            },
            success: function(data) {
                let html = '';
                // Render products
                for (let i = 0; i < data.data.length; i++) {
                    if (data.data[i].image == null) {
                        html += `
                    <div class="col-md-4 mb-4">
                        <div class="card feature-card h-100">
                            <img src="/No_Image_Available.jpg" alt="Product Image" class="card-img-top" style="width: 100%; height: 175px; object-fit: cover">
                            <div class="card-body">
                                <h5 class="card-title">${data.data[i].barang_nama}</h5>

                                <p class="card-text" style="font-weight: bold;">${formatRupiah(data.data[i]?.harga) ?? ""}</p>

                                <input type="hidden" id="harga-${data.data[i].barang_id}" value="${data.data[i].harga}">

                                <p class="card-text">${data.data[i]?.deskripsi ?? ""}</p>


                                 <div class="d-flex align-items-center justify-content-between mt-3">
                                    <div class="input-group" style="width: 100px;">
                                        <button class="btn btn-outline-primary btn-sm" type="button" onclick="decreaseQty(${data.data[i].barang_id})">-</button>
                                        <input type="text" id="qty-${data.data[i].barang_id}" class="form-control text-center form-control-sm" value="1" readonly style="background: white;">
                                        <button class="btn btn-outline-primary btn-sm" type="button" onclick="increaseQty(${data.data[i].barang_id})">+</button>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addCart(${data.data[i].barang_id})">
                                        <i class="fas fa-plus"></i>
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                    } else {
                        html += `
                    <div class="col-md-4 mb-4">
                        <div class="card feature-card h-100">
                            <img src="{{ asset('storage/uploads/product/${data.data[i].image}') }}" alt="Product Image" class="card-img-top" style="width: 100%; height: 175px; object-fit: cover">
                            <div class="card-body">
                                <h5 class="card-title">${data.data[i].barang_nama}</h5>
                                <p class="card-text" style="font-weight: bold;">${formatRupiah(data.data[i]?.harga) ?? ""}</p>

                                <input type="hidden" id="harga-${data.data[i].barang_id}" value="${data.data[i].harga}">

                                <p class="card-text">${data.data[i]?.deskripsi ?? ""}</p>

                                <div class="d-flex align-items-center justify-content-between mt-3">
                                    <div class="input-group" style="width: 100px;">
                                        <button class="btn btn-outline-primary btn-sm" type="button" onclick="decreaseQty(${data.data[i].barang_id})">-</button>
                                        <input type="text" id="qty-${data.data[i].barang_id}" class="form-control text-center form-control-sm" value="1" readonly style="background: white;">
                                        <button class="btn btn-outline-primary btn-sm" type="button" onclick="increaseQty(${data.data[i].barang_id})">+</button>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addCart(${data.data[i].barang_id})">
                                        <i class="fas fa-plus"></i>
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                    }
                }
                $('.product-list').html(html);

                // Render pagination
                let paginationHtml = '';

                // Previous button
                paginationHtml += `<li class="page-item ${data.current_page === 1 ? 'disabled' : ''}">
                                   <a class="page-link" href="#" data-page="${data.current_page - 1}" aria-label="Previous">
                                       <span aria-hidden="true">&laquo;</span>
                                   </a>
                               </li>`;

                // Page number buttons
                for (let page = 1; page <= data.last_page; page++) {
                    paginationHtml += `<li class="page-item ${page === data.current_page ? 'active' : ''}">
                                    <a class="page-link" href="#" data-page="${page}">${page}</a>
                                   </li>`;
                }

                // Next button
                paginationHtml += `<li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}">
                                   <a class="page-link" href="#" data-page="${data.current_page + 1}" aria-label="Next">
                                       <span aria-hidden="true">&raquo;</span>
                                   </a>
                               </li>`;

                $('#pagination').html(paginationHtml);

                $('.page-link').on('click', function(e) {
                    e.preventDefault();
                    let page = $(this).data('page');
                    if (page >= 1 && page <= data.last_page) {
                        getProductData(page);
                    }
                });
            },
            complete: function() {
                $('#loading').hide();
                $('.product-list').show();
                $('#pagination').show();
            }
        });
    }

    increaseQty = (id) => {
        let qtyInput = document.getElementById(`qty-${id}`);
        let currentValue = parseInt(qtyInput.value) || 0;
        qtyInput.value = currentValue + 1;
    }

    decreaseQty = (id) => {
        let qtyInput = document.getElementById(`qty-${id}`);
        let currentValue = parseInt(qtyInput.value) || 1;
        if (currentValue > 1) {
            qtyInput.value = currentValue - 1;
        }
    }


    addCart = (id_barang) => {
        let auth = '{{ $auth }}';
        if(!auth){
            window.location.href = "{{ url('/login') }}";
            return;
        }

        $.ajax({
            url: "{{ url('public/add_cart') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                barang_id: id_barang,
                jumlah: $('#qty-' + id_barang).val(),
                harga: $('#harga-' + id_barang).val()
            },
            success: function(res) {
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message
                    }).then(function() {
                        location.reload();
                    });
                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: res.message
                    });
                }
            }
        });
    }
</script>
