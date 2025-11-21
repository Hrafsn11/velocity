@extends('layouts.app')

@section('title', 'Global Timeline - Roadmap')

@push('styles')
    <style>
        /* Menghilangkan border event agar lebih bersih */
        .fc-event {
            border: 0 !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Warna tombol hari ini */
        .fc-button-primary {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
        }

        /* Fix tinggi agar tidak kosong */
        #calendar {
            min-height: 600px;
        }

        /* Fix tinggi chart */
        #projectTimelineChart {
            min-height: 450px;
        }
    </style>
@endpush

@section('content')

    <div class="row mb-4">
        <div class="col-md-6">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Project Management /</span> Global Timeline</h4>
            <p class="text-muted mb-0 mt-1">Track roadmap progress across all active workspaces</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> New Project Plan
            </button>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

            <div class="d-flex align-items-center gap-3 w-100 w-md-auto flex-wrap">
                <div class="input-group input-group-merge" style="max-width: 250px; min-width: 200px;">
                    <span class="input-group-text bg-body"><i class="ti ti-calendar"></i></span>
                    <input type="text" class="form-control" placeholder="Filter Period" id="flatpickr-range" />
                </div>

                <select class="form-select" style="max-width: 180px; min-width: 150px;">
                    <option selected>All Status</option>
                    <option value="1">In Progress</option>
                    <option value="2">Upcoming</option>
                    <option value="3">Completed</option>
                </select>
            </div>

            <ul class="nav nav-pills" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active d-flex align-items-center" role="tab"
                        data-bs-toggle="tab" data-bs-target="#navs-timeline" aria-controls="navs-timeline"
                        aria-selected="true">
                        <i class="ti ti-chart-bar me-1"></i> <span class="d-none d-sm-inline">Roadmap</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link d-flex align-items-center" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-calendar" aria-controls="navs-calendar" aria-selected="false">
                        <i class="ti ti-calendar me-1"></i> <span class="d-none d-sm-inline">Calendar</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content p-0 bg-transparent shadow-none">

        <div class="tab-pane fade show active" id="navs-timeline" role="tabpanel">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Project Roadmap (Gantt View)</h5>

                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="timelineMenu" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="ti ti-dots-vertical text-muted"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="timelineMenu">
                            <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-download me-1"></i> Download
                                Report</a>
                            <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-refresh me-1"></i> Refresh
                                Data</a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-2">
                    <div id="projectTimelineChart"></div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="navs-calendar" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- DATA DARI CONTROLLER ---
            const projects = @json($projects);
            // Ambil warna primary dari CSS Variable agar sinkron dengan tema
            const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--bs-primary')
            .trim() || '#7367f0';

            // ==========================================
            // 1. APEXCHARTS (GANTT VIEW)
            // ==========================================
            const chartEl = document.querySelector("#projectTimelineChart");
            if (chartEl) {
                const chartSeries = [{
                    data: projects.map(p => {
                        return {
                            x: p.name,
                            y: [
                                new Date(p.start).getTime(),
                                new Date(p.end).getTime()
                            ],
                            fillColor: p.color
                        }
                    })
                }];

                const chartOptions = {
                    series: chartSeries,
                    chart: {
                        height: 450,
                        type: 'rangeBar',
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Public Sans' // Font bawaan Vuexy
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '50%',
                            borderRadius: 5,
                            rangeBarGroupRows: true
                        }
                    },
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            datetimeFormatter: {
                                year: 'yyyy',
                                month: 'MMM \'yy',
                                day: 'dd MMM'
                            }
                        }
                    },
                    grid: {
                        strokeDashArray: 6,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        },
                        yaxis: {
                            lines: {
                                show: false
                            }
                        },
                        padding: {
                            top: 0,
                            right: 10,
                            bottom: 0,
                            left: 10
                        }
                    }
                };

                const chart = new ApexCharts(chartEl, chartOptions);
                chart.render();
            }

            // ==========================================
            // 2. FULLCALENDAR (CALENDAR VIEW)
            // ==========================================
            const calendarEl = document.getElementById('calendar');
            // Tombol Tab untuk trigger render ulang saat diklik
            const calendarTabBtn = document.querySelector('button[data-bs-target="#navs-calendar"]');

            if (calendarEl) {
                // Format data projects agar sesuai format FullCalendar
                const calendarEvents = projects.map(p => {
                    return {
                        title: p.name,
                        start: p.start,
                        end: p.end,
                        backgroundColor: p.color, // Warna background event
                        borderColor: p.color, // Warna border
                        textColor: '#fff', // Warna teks putih agar kontras
                        allDay: true
                    };
                });

                let calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,listWeek'
                    },
                    events: calendarEvents,
                    height: 'auto', // Agar tinggi menyesuaikan konten
                    contentHeight: 600,
                    themeSystem: 'standard'
                });

                // KUNCI PERBAIKAN TAMPILAN:
                // Kalender seringkali tidak muncul jika di-render di dalam tab yang hidden (display:none).
                // Kita harus memanggil calendar.render() SAAT tab tersebut aktif.
                if (calendarTabBtn) {
                    calendarTabBtn.addEventListener('shown.bs.tab', function(event) {
                        calendar.render(); // Render ulang saat tab dibuka
                        calendar.updateSize(); // Pastikan ukurannya pas
                    });
                }

                // Render awal (jaga-jaga)
                calendar.render();
            }

            // ==========================================
            // 3. FLATPICKR
            // ==========================================
            const flatpickrEl = document.querySelector("#flatpickr-range");
            if (flatpickrEl) {
                flatpickr(flatpickrEl, {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    defaultDate: [new Date().fp_incr(-30), new Date().fp_incr(30)]
                });
            }
        });
    </script>
@endpush
