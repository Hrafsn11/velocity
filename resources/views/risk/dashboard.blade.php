@extends('layouts.app')

@section('title', 'Risk Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('libs/apex-charts/apex-charts.css') }}">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Risk Dashboard</h4>
        <p class="text-muted mb-0">Visual overview of project risks and issues</p>
    </div>

    <!-- Overview Stats -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text mb-1">Total Risks</p>
                            <div class="d-flex align-items-end mb-1">
                                <h4 class="card-title mb-0 me-2">28</h4>
                                <small class="text-success">(+3)</small>
                            </div>
                            <small class="d-block">This month</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-danger rounded-circle p-2">
                                <i class="ti ti-alert-triangle ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text mb-1">Critical Risks</p>
                            <div class="d-flex align-items-end mb-1">
                                <h4 class="card-title mb-0 me-2 text-danger">8</h4>
                                <small class="text-warning">(+1)</small>
                            </div>
                            <small class="d-block">Immediate action needed</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-danger rounded-circle p-2">
                                <i class="ti ti-flame ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text mb-1">Open Issues</p>
                            <div class="d-flex align-items-end mb-1">
                                <h4 class="card-title mb-0 me-2 text-warning">15</h4>
                                <small class="text-danger">(+5)</small>
                            </div>
                            <small class="d-block">Pending resolution</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-warning rounded-circle p-2">
                                <i class="ti ti-bug ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text mb-1">Avg. Risk Score</p>
                            <div class="d-flex align-items-end mb-1">
                                <h4 class="card-title mb-0 me-2">11.4</h4>
                                <small class="text-success">(-0.8)</small>
                            </div>
                            <small class="d-block">Trending down</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-info rounded-circle p-2">
                                <i class="ti ti-chart-line ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row g-4 mb-4">
        <!-- Risk Distribution by Urgency -->
        <div class="col-lg-6 col-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Risk Distribution by Urgency</h5>
                        <p class="card-subtitle mb-0">Current risk urgency levels</p>
                    </div>
                </div>
                <div class="card-body">
                    <div id="riskUrgencyChart"></div>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-danger me-2" style="width: 12px; height: 12px;"></div>
                                <span>Critical (Risk Score 15-25)</span>
                            </div>
                            <span class="fw-semibold">8 risks</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-warning me-2" style="width: 12px; height: 12px;"></div>
                                <span>High (Risk Score 10-14)</span>
                            </div>
                            <span class="fw-semibold">12 risks</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-info me-2" style="width: 12px; height: 12px;"></div>
                                <span>Medium (Risk Score 6-9)</span>
                            </div>
                            <span class="fw-semibold">5 risks</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-success me-2" style="width: 12px; height: 12px;"></div>
                                <span>Low (Risk Score 1-5)</span>
                            </div>
                            <span class="fw-semibold">3 risks</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk by Category -->
        <div class="col-lg-6 col-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Risk by Category</h5>
                        <p class="card-subtitle mb-0">Distribution across categories</p>
                    </div>
                </div>
                <div class="card-body">
                    <div id="riskCategoryChart"></div>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-primary me-2" style="width: 12px; height: 12px;"></div>
                                <span>Technical</span>
                            </div>
                            <span class="fw-semibold">10 risks</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-warning me-2" style="width: 12px; height: 12px;"></div>
                                <span>SDM</span>
                            </div>
                            <span class="fw-semibold">7 risks</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-success me-2" style="width: 12px; height: 12px;"></div>
                                <span>Financial</span>
                            </div>
                            <span class="fw-semibold">6 risks</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-info me-2" style="width: 12px; height: 12px;"></div>
                                <span>Timeline</span>
                            </div>
                            <span class="fw-semibold">5 risks</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-4 mb-4">
        <!-- Risk Trend -->
        <div class="col-lg-8 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Risk Trend Over Time</h5>
                        <p class="card-subtitle mb-0">Monthly risk count by urgency level</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-text-secondary rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Last 6 Months
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="javascript:void(0);">Last 3 Months</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);">Last 6 Months</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);">Last Year</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div id="riskTrendChart"></div>
                </div>
            </div>
        </div>

        <!-- Issue Status -->
        <div class="col-lg-4 col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-1">Issue Status</h5>
                    <p class="card-subtitle mb-0">Current issue breakdown</p>
                </div>
                <div class="card-body">
                    <div id="issueStatusChart"></div>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-circle-filled text-warning me-2"></i>
                                <span>Open</span>
                            </div>
                            <span class="fw-semibold">15</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-circle-filled text-info me-2"></i>
                                <span>In Progress</span>
                            </div>
                            <span class="fw-semibold">18</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-circle-filled text-success me-2"></i>
                                <span>Resolved</span>
                            </div>
                            <span class="fw-semibold">9</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Risks Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Top 5 Critical Risks</h5>
                    <a href="{{ route('risk.index') }}" class="btn btn-sm btn-primary">View All Risks</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Risk Description</th>
                                <th>Category</th>
                                <th>Risk Score</th>
                                <th>Urgency</th>
                                <th>Affected Module</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="fw-medium">#R001</span></td>
                                <td>
                                    <span class="fw-medium d-block">Database server overload pada peak hours</span>
                                    <small class="text-muted">Cause: Kurang optimasi query dan indexing</small>
                                </td>
                                <td><span class="badge bg-label-primary">Technical</span></td>
                                <td><span class="badge bg-danger rounded-pill">20</span></td>
                                <td><span class="badge bg-danger">Critical</span></td>
                                <td>Module Performance</td>
                            </tr>
                            <tr>
                                <td><span class="fw-medium">#R002</span></td>
                                <td>
                                    <span class="fw-medium d-block">Key developer resign mendadak</span>
                                    <small class="text-muted">Cause: Work-life balance issues</small>
                                </td>
                                <td><span class="badge bg-label-warning">SDM</span></td>
                                <td><span class="badge bg-warning rounded-pill">12</span></td>
                                <td><span class="badge bg-warning">High</span></td>
                                <td>All Development Tasks</td>
                            </tr>
                            <tr>
                                <td><span class="fw-medium">#R003</span></td>
                                <td>
                                    <span class="fw-medium d-block">Budget overrun karena scope creep</span>
                                    <small class="text-muted">Cause: Permintaan fitur tambahan tanpa approval</small>
                                </td>
                                <td><span class="badge bg-label-success">Financial</span></td>
                                <td><span class="badge bg-warning rounded-pill">12</span></td>
                                <td><span class="badge bg-warning">High</span></td>
                                <td>Budget Planning</td>
                            </tr>
                            <tr>
                                <td><span class="fw-medium">#R005</span></td>
                                <td>
                                    <span class="fw-medium d-block">Security vulnerability pada authentication module</span>
                                    <small class="text-muted">Cause: Belum implementasi 2FA dan rate limiting</small>
                                </td>
                                <td><span class="badge bg-label-primary">Technical</span></td>
                                <td><span class="badge bg-warning rounded-pill">10</span></td>
                                <td><span class="badge bg-warning">High</span></td>
                                <td>Authentication Module</td>
                            </tr>
                            <tr>
                                <td><span class="fw-medium">#R004</span></td>
                                <td>
                                    <span class="fw-medium d-block">Delay integrasi third-party API</span>
                                    <small class="text-muted">Cause: Dokumentasi API tidak lengkap dari vendor</small>
                                </td>
                                <td><span class="badge bg-label-info">Timeline</span></td>
                                <td><span class="badge bg-info rounded-pill">9</span></td>
                                <td><span class="badge bg-info">Medium</span></td>
                                <td>Integration Module</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('libs/apex-charts/apexcharts.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Risk Urgency Donut Chart
            const riskUrgencyOptions = {
                series: [8, 12, 5, 3],
                chart: {
                    type: 'donut',
                    height: 280
                },
                labels: ['Critical', 'High', 'Medium', 'Low'],
                colors: ['#ff4c51', '#ff9f43', '#00cfe8', '#28c76f'],
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return Math.round(val) + '%';
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Risks',
                                    fontSize: '1.2rem',
                                    formatter: function(w) {
                                        return '28';
                                    }
                                }
                            }
                        }
                    }
                }
            };
            const riskUrgencyChart = new ApexCharts(document.querySelector("#riskUrgencyChart"), riskUrgencyOptions);
            riskUrgencyChart.render();

            // Risk Category Donut Chart
            const riskCategoryOptions = {
                series: [10, 7, 6, 5],
                chart: {
                    type: 'donut',
                    height: 280
                },
                labels: ['Technical', 'SDM', 'Financial', 'Timeline'],
                colors: ['#7367f0', '#ff9f43', '#28c76f', '#00cfe8'],
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return Math.round(val) + '%';
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Risks',
                                    fontSize: '1.2rem',
                                    formatter: function(w) {
                                        return '28';
                                    }
                                }
                            }
                        }
                    }
                }
            };
            const riskCategoryChart = new ApexCharts(document.querySelector("#riskCategoryChart"), riskCategoryOptions);
            riskCategoryChart.render();

            // Risk Trend Line Chart
            const riskTrendOptions = {
                series: [
                    {
                        name: 'Critical',
                        data: [5, 6, 7, 8, 7, 8]
                    },
                    {
                        name: 'High',
                        data: [8, 10, 11, 12, 11, 12]
                    },
                    {
                        name: 'Medium',
                        data: [3, 4, 5, 5, 4, 5]
                    },
                    {
                        name: 'Low',
                        data: [2, 2, 3, 3, 2, 3]
                    }
                ],
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#ff4c51', '#ff9f43', '#00cfe8', '#28c76f'],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },
                yaxis: {
                    title: {
                        text: 'Number of Risks'
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left'
                },
                grid: {
                    borderColor: '#e7e7e7',
                    strokeDashArray: 5
                }
            };
            const riskTrendChart = new ApexCharts(document.querySelector("#riskTrendChart"), riskTrendOptions);
            riskTrendChart.render();

            // Issue Status Donut Chart
            const issueStatusOptions = {
                series: [15, 18, 9],
                chart: {
                    type: 'donut',
                    height: 250
                },
                labels: ['Open', 'In Progress', 'Resolved'],
                colors: ['#ff9f43', '#00cfe8', '#28c76f'],
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        return opts.w.config.series[opts.seriesIndex];
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Issues',
                                    fontSize: '1.2rem',
                                    formatter: function(w) {
                                        return '42';
                                    }
                                }
                            }
                        }
                    }
                }
            };
            const issueStatusChart = new ApexCharts(document.querySelector("#issueStatusChart"), issueStatusOptions);
            issueStatusChart.render();
        });
    </script>
@endpush
