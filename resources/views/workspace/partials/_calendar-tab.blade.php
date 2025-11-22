<div class="app-calendar-wrapper">
    
    <div class="app-calendar-sidebar" id="app-calendar-sidebar">
        <div class="border-bottom p-3 my-sm-0 mb-3">
            <div class="d-grid">
                <button class="btn btn-primary btn-toggle-sidebar" data-bs-toggle="offcanvas" data-bs-target="#addEventSidebar" aria-controls="addEventSidebar">
                    <i class="ti ti-plus me-1"></i>
                    <span class="align-middle">Add Event</span>
                </button>
            </div>
        </div>
        <div class="p-3">
            <div class="inline-calendar"></div>
            <hr class="container-m-nx my-4" />
            <div class="mb-3"><small class="text-small text-muted text-uppercase align-middle">Filter</small></div>
            <div class="form-check mb-2">
                <input class="form-check-input select-all" type="checkbox" id="selectAll" data-value="all" checked />
                <label class="form-check-label" for="selectAll">View All</label>
            </div>
            <div class="app-calendar-events-filter">
                 <div class="form-check form-check-danger mb-2">
                    <input class="form-check-input input-filter" type="checkbox" id="select-personal" data-value="Personal" checked />
                    <label class="form-check-label" for="select-personal">Personal</label>
                </div>
                <div class="form-check form-check-primary mb-2">
                    <input class="form-check-input input-filter" type="checkbox" id="select-business" data-value="Business" checked />
                    <label class="form-check-label" for="select-business">Business</label>
                </div>
                <div class="form-check form-check-warning mb-2">
                    <input class="form-check-input input-filter" type="checkbox" id="select-family" data-value="Family" checked />
                    <label class="form-check-label" for="select-family">Family</label>
                </div>
                <div class="form-check form-check-success mb-2">
                    <input class="form-check-input input-filter" type="checkbox" id="select-holiday" data-value="Holiday" checked />
                    <label class="form-check-label" for="select-holiday">Holiday</label>
                </div>
                 </div>
        </div>
    </div>

    <div class="app-calendar-content">
        <div class="card shadow-none border-0 h-100">
            <div class="card-body pb-0">
                <div class="d-lg-none mb-3">
                     <button type="button" class="btn btn-label-secondary btn-toggle-calendar-sidebar">
                        <i class="ti ti-menu-2 me-1"></i> Sidebar
                    </button>
                </div>
                
                <div id="calendar"></div>
            </div>
        </div>
        <div class="app-overlay"></div>
    </div>

</div>