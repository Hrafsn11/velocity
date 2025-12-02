<div class="offcanvas offcanvas-end event-sidebar" tabindex="-1" id="addEventSidebar" aria-labelledby="addEventSidebarLabel">d
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="addEventSidebarLabel">Add Event</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form class="event-form pt-0" id="eventForm" onsubmit="return false">
            <div class="mb-3">
                <label class="form-label" for="eventTitle">Title</label>
                <input type="text" class="form-control" id="eventTitle" name="eventTitle"
                    placeholder="Event Title" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="eventLabel">Label</label>
                <select class="select2 select-event-label form-select" id="eventLabel" name="eventLabel">
                    <option data-label="primary" value="Business" selected>Business</option>
                    <option data-label="danger" value="Personal">Personal</option>
                    <option data-label="warning" value="Family">Family</option>
                    <option data-label="success" value="Holiday">Holiday</option>
                    <option data-label="info" value="ETC">ETC</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="eventStartDate">Start Date</label>
                <input type="text" class="form-control" id="eventStartDate" name="eventStartDate"
                    placeholder="Start Date" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="eventEndDate">End Date</label>
                <input type="text" class="form-control" id="eventEndDate" name="eventEndDate"
                    placeholder="End Date" />
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input allDay-switch" id="allDaySwitch" />
                    <label class="form-check-label" for="allDaySwitch">All Day</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="eventURL">Event URL</label>
                <input type="url" class="form-control" id="eventURL" name="eventURL"
                    placeholder="https://www.google.com" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="eventLocation">Location</label>
                <input type="text" class="form-control" id="eventLocation" name="eventLocation"
                    placeholder="Enter Location" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="eventDescription">Description</label>
                <textarea class="form-control" name="eventDescription" id="eventDescription"></textarea>
            </div>
            <div class="mb-3 d-flex justify-content-sm-between justify-content-start my-4">
                <div>
                    <button type="submit" class="btn btn-primary btn-add-event me-sm-3 me-1">Add</button>
                    <button type="reset" class="btn btn-label-secondary btn-cancel me-sm-0 me-1"
                        data-bs-dismiss="offcanvas">Cancel</button>
                </div>
                <button class="btn btn-label-danger btn-delete-event d-none">Delete</button>
            </div>
        </form>
    </div>
</div>
