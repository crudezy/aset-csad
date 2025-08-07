@extends('layouts.app')

@section('title', 'Dashboard')

@push('style')
    {{-- CSS Libraries can be added here if needed --}}
@endpush

@section('content')
    <div class="section-header">
        <h1>Dashboard</h1>
    </div>

    {{-- KARTU INDIKATOR (KPI) --}}
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-box"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Aset</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalAset }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Aset Digunakan</h4>
                    </div>
                    <div class="card-body">
                        {{ $asetDigunakan }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Aset Rusak</h4>
                    </div>
                    <div class="card-body">
                        {{ $asetRusak }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Dalam Perbaikan</h4>
                    </div>
                    <div class="card-body">
                        {{ $asetDalamPerbaikan }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRAFIK VISUAL --}}
    <div class="row">
        <div class="col-lg-6 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Komposisi Status Aset</h4>
                </div>
                <div class="card-body">
                    <canvas id="statusAsetChart" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Jumlah Aset per Kategori</h4>
                </div>
                <div class="card-body">
                    <canvas id="asetPerKategoriChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- AKTIVITAS TERBARU --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Aktivitas Terbaru</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-tools text-danger"></i> Servis Terbaru</h6>
                            <ul class="list-unstyled list-unstyled-border">
                                @forelse ($servisTerbaru as $servis)
                                <li class="media">
                                    <div class="media-body">
                                        <div class="text-primary float-right text-small">{{ $servis->created_at->diffForHumans() }}</div>
                                        <div class="media-title">{{ $servis->aset->merk }} {{ $servis->aset->type }}</div>
                                        <span class="text-small text-muted">{{ Str::limit($servis->deskripsi_kerusakan, 40) }}</span>
                                    </div>
                                </li>
                                @empty
                                <li class="text-center text-muted p-3">Tidak ada aktivitas servis.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-handshake text-success"></i> Pemakaian Terbaru</h6>
                            <ul class="list-unstyled list-unstyled-border">
                                @forelse ($pemakaianTerbaru as $pemakaian)
                                <li class="media">
                                    <div class="media-body">
                                        <div class="text-primary float-right text-small">{{ $pemakaian->created_at->diffForHumans() }}</div>
                                        <div class="media-title">{{ $pemakaian->aset->merk }}</div>
                                        <span class="text-small text-muted">Diserahkan kepada {{ $pemakaian->pegawai->nama }}</span>
                                    </div>
                                </li>
                                @empty
                                <li class="text-center text-muted p-3">Tidak ada aktivitas pemakaian.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // =================================================================
            // KODE FINAL UNTUK PIE CHART (STATUS ASET)
            // =================================================================
            var statusLabels = {!! json_encode($statusLabels) !!};
            var statusCounts = {!! json_encode($statusCounts) !!};
            var statusAsetCtx = document.getElementById("statusAsetChart").getContext('2d');
            new Chart(statusAsetCtx, {
                type: 'pie',
                data: {
                    datasets: [{
                        data: statusCounts,
                        backgroundColor: ['#6777ef', '#63ed7a', '#fc544b', '#ffa426', '#343a40', '#868e96'],
                        label: 'Jumlah Aset'
                    }],
                    labels: statusLabels,
                },
                options: {
                    responsive: true,
                    legend: {
                        position: 'bottom',
                        labels: {
                            filter: function(legendItem, chartData) {
                                return chartData.datasets[0].data[legendItem.index] > 0;
                            }
                        }
                    },
                    tooltips: {
                        callbacks: {
                            filter: function(tooltipItem, data) {
                                return data.datasets[0].data[tooltipItem.index] > 0;
                            },
                            label: function(tooltipItem, data) {
                                var label = data.labels[tooltipItem.index] || '';
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                return ' ' + label + ': ' + value;
                            }
                        }
                    }
                }
            });

            // Data untuk Bar Chart (Aset per Kategori)
            var kategoriLabels = {!! json_encode($kategoriLabels) !!};
            var kategoriCounts = {!! json_encode($kategoriCounts) !!};
            var asetPerKategoriCtx = document.getElementById("asetPerKategoriChart").getContext('2d');
            new Chart(asetPerKategoriCtx, {
                type: 'bar',
                data: {
                    labels: kategoriLabels,
                    datasets: [{
                        label: 'Jumlah Aset',
                        data: kategoriCounts,
                        backgroundColor: '#6777ef',
                    }]
                },
                options: {
                    responsive: true,
                    legend: { display: false },
                    scales: {
                        yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }]
                    }
                }
            });
        });
    </script>
@endpush