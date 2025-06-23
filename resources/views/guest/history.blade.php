<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel"
    aria-hidden="true">
    @if ($auth?->user_id)
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModalLabel">Riwayat Belanja</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body " >
                    <div id="history-body">
                      </div>

                      <nav aria-label="History pagination" class="mt-3">
                        <ul id="history-pagination" class="pagination justify-content-center mb-0">
                        </ul>
                      </nav>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Tutup</button>

                </div>
            </div>
        </div>
    @endif
</div>

<script>
    formatTanggalIndo = (tanggal) => {
        const bulanIndo = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];

        const date = new Date(tanggal);
        const hari = date.getDate();
        const bulan = bulanIndo[date.getMonth()];
        const tahun = date.getFullYear();

        return `${hari} ${bulan} ${tahun}`;
    }

    onShowHistory = (page = 1) => {
        const auth = '{{ $auth }}';
        if (!auth) {
            return window.location.href = "{{ url('/login') }}";
        }

        $.ajax({
            url: "{{ url('public/history') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                page
            },
            success: function(resp) {
                let html = '';
                if (resp.data.length) {
                    resp.data.forEach(penjualan => {
                        let badge = '';
                        switch (penjualan.status) {
                            case 'validate_payment':
                                badge =
                                    `<span class="badge badge-warning">Menunggu Pembayaran</span>`;
                                break;
                            case 'paid_off':
                                badge =
                                    `<span class="badge badge-primary">Lunas, Sedang Proses</span>`;
                                break;
                            case 'completed':
                                badge = `<span class="badge badge-success">Selesai</span>`;
                                break;
                            case 'rejected':
                                badge = `<span class="badge badge-danger">Dibatalkan</span>`;
                                break;
                        }

                        html += `
                                <div class="card mb-3">
                                    <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                        <h5 class="card-title mb-1">Kode: ${penjualan.penjualan_kode}</h5>
                                        <p class="text-muted mb-1" style="font-size:.9rem">
                                            Tanggal: ${penjualan.penjualan_tanggal ? formatTanggalIndo(penjualan.penjualan_tanggal) : '-'}
                                        </p>
                                        <p class="text-muted mb-1" style="font-size:.9rem">
                                            Total Item: ${penjualan.detail.length} barang
                                        </p>
                                        ${badge}
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary"
                                                data-toggle="collapse"
                                                data-target="#detail-${penjualan.penjualan_id}">
                                        Lihat Detail
                                        </button>
                                    </div>
                                    <div class="collapse mt-3" id="detail-${penjualan.penjualan_id}">
                                        <ul class="list-group list-group-flush">
                                `;
                        penjualan.detail.forEach(item => {
                            html += `
                                <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                    <strong>${item.barang?.barang_nama}</strong>
                                    <div class="text-muted" style="font-size:.85rem">
                                        Jumlah: ${item.jumlah} × ${formatRupiah(item.harga)}
                                    </div>
                                    </div>
                                    <div>${formatRupiah(item.harga * item.jumlah)}</div>
                                </div>
                                </li>`;
                        });
                        html += `
                                </ul>
                            </div>
                            </div>
                        </div>`;
                    });
                } else {
                    html = `<div class="text-center text-muted">Belum ada riwayat belanja.</div>`;
                }

                $('#history-body').html(html);
                $('#historyModal').modal('show');

                // 2) Render pagination
                let pg = '';
                // Prev
                pg += `<li class="page-item ${resp.current_page===1?'disabled':''}">
               <a class="page-link" href="#" data-page="${resp.current_page-1}">&laquo;</a>
             </li>`;
                // Pages
                for (let p = 1; p <= resp.last_page; p++) {
                    pg += `<li class="page-item ${p===resp.current_page?'active':''}">
                 <a class="page-link" href="#" data-page="${p}">${p}</a>
               </li>`;
                }
                // Next
                pg += `<li class="page-item ${resp.current_page===resp.last_page?'disabled':''}">
               <a class="page-link" href="#" data-page="${resp.current_page+1}">&raquo;</a>
             </li>`;

                $('#history-pagination').html(pg);

                // 3) Bind click
                $('#history-pagination .page-link').off('click').on('click', function(e) {
                    e.preventDefault();
                    const p = +$(this).data('page');
                    if (p >= 1 && p <= resp.last_page) {
                        onShowHistory(p);
                    }
                });
            }
        });
    };
</script>
