@extends('layouts.template')

@section('content')
    <div class="col-12 row">

        <div class="p-3 col-md-3">
            <div class="card ">
                <div class="card-header">
                    <span class="card-title text-left">Filter Data</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select id="filter_tahun" class="form-control">
                                <option value="">Semua Tahun</option>
                                @for ($year = date('Y'); $year >= 2020; $year--)
                                    <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                        {{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-8">
                            <select id="filter_bulan" class="form-control">
                                <option value="">Semua Bulan</option>
                                @php
                                    $bulanIndonesia = [
                                        1 => 'Januari',
                                        2 => 'Februari',
                                        3 => 'Maret',
                                        4 => 'April',
                                        5 => 'Mei',
                                        6 => 'Juni',
                                        7 => 'Juli',
                                        8 => 'Agustus',
                                        9 => 'September',
                                        10 => 'Oktober',
                                        11 => 'November',
                                        12 => 'Desember',
                                    ];
                                    $currentMonth = date('m');
                                @endphp
                                @foreach ($bulanIndonesia as $num => $nama)
                                    <option value="{{ sprintf('%02d', $num) }}"
                                        {{ $num == $currentMonth ? 'selected' : '' }}>
                                        {{ $nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="p-3 col-md-3">
            <div class="card ">
                <div class="card-header">
                    <span class="card-title text-left">Total Penjualan</span>
                </div>
                <div class="card-body">
                    <span id="penjualan" class="font-weight-bold h1 text-muted">

                    </span>
                </div>
            </div>
        </div>
        <div class="p-3 col-md-3">
            <div class="card ">
                <div class="card-header">
                    <span class="card-title text-left">Total Pembelanjaan</span>
                </div>
                <div class="card-body">
                    <span id="pembelanjaan" class="font-weight-bold h1 text-muted">

                    </span>
                </div>
            </div>
        </div>
        <div class="p-3 col-md-3">
            <div class="card ">
                <div class="card-header">
                    <span class="card-title text-left">Margin</span>
                </div>
                <div class="card-body">
                    <span id="margin" class="font-weight-bold h1 text-muted">

                    </span>
                </div>
            </div>
        </div>


    </div>
    <div class="col-12 row px-3 mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Grafik Penjualan per Bulan</span>
                </div>
                <div class="card-body">
                    <canvas id="chartPenjualan"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Item Terlaris</span>
                </div>
                <div class="card-body">
                    <canvas id="chartItemTerlaris"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let chartPenjualanInstance = null;
        let chartItemTerlarisInstance = null;

        $(() => {
            getCardData();
            getChartData();
        });

        $('#filter_tahun, #filter_bulan').on('change', function() {
            getCardData();
            getChartData();
        });

        formatRupiah = (angka) => {
            const absFormatted = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(Math.abs(angka));

            return angka < 0 ?
                `Rp -${absFormatted.replace('Rp', '').trim()}` :
                `Rp ${absFormatted.replace('Rp', '').trim()}`;
        }

        getCardData = () => {
            $.ajax({
                url: "{{ url('dashboard/getCardData') }}",
                type: "POST",
                dataType: "json",
                data: {
                    tahun: $('#filter_tahun').val(),
                    bulan: $('#filter_bulan').val(),
                },
                success: function(data) {
                    $.each(data, function(key, value) {
                        const formatted = formatRupiah(value);
                        const element = $(`#${key}`);

                        element.html(formatted);
                        element.removeClass('text-success text-danger text-muted');

                        if (value > 0) {
                            element.addClass('text-success');
                        } else if (value < 0) {
                            element.addClass('text-danger');
                        } else {
                            element.addClass('text-muted');
                        }
                    });

                }
            })
        }

        getChartData = () => {
            $.ajax({
                url: "{{ url('dashboard/getChartData') }}",
                type: "POST",
                dataType: "json",
                data: {
                    tahun: $('#filter_tahun').val(),
                    bulan: $('#filter_bulan').val(),
                },
                success: function(data) {
                    const ctxPenjualan = document.getElementById('chartPenjualan').getContext('2d');
                    const ctxItemTerlaris = document.getElementById('chartItemTerlaris').getContext('2d');

                    if (chartPenjualanInstance) {
                        chartPenjualanInstance.destroy();
                    }

                    chartPenjualanInstance = new Chart(ctxPenjualan, {
                        type: 'line',
                        data: {
                            labels: data.bulan.labels,
                            datasets: [{
                                label: 'Total Penjualan',
                                data: data.bulan.data,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    if (chartItemTerlarisInstance) {
                        chartItemTerlarisInstance.destroy();
                    }

                    chartItemTerlarisInstance = new Chart(ctxItemTerlaris, {
                        type: 'pie',
                        data: {
                            labels: data.item.labels,
                            datasets: [{
                                label: 'Item Terlaris',
                                data: data.item.data,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(153, 102, 255, 0.2)',
                                    'rgba(255, 159, 64, 0.2)',
                                    'rgba(200, 100, 100, 0.2)',
                                    'rgba(100, 200, 100, 0.2)',
                                    'rgba(100, 100, 200, 0.2)',
                                    'rgba(200, 200, 100, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)',
                                    'rgba(200, 100, 100, 1)',
                                    'rgba(100, 200, 100, 1)',
                                    'rgba(100, 100, 200, 1)',
                                    'rgba(200, 200, 100, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            return tooltipItem.label + ": " + formatRupiah(
                                                tooltipItem.raw);
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        }
    </script>
@endpush
