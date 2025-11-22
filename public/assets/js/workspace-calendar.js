/**
 * Workspace Project Detail - Calendar Module
 * Handles FullCalendar initialization and interactions
 * @version 1.0.0
 */

(function() {
    'use strict';

    /**
     * Calendar Configuration
     */
    const CalendarConfig = {
        colors: {
            Business: 'primary',
            Holiday: 'success',
            Personal: 'danger',
            Family: 'warning',
            ETC: 'info'
        },
        
        selectors: {
            calendar: '#calendar',
            calendarTab: 'button[data-bs-target="#tab-calendar"]',
            calendarPane: '#tab-calendar',
            sidebar: '.app-calendar-sidebar',
            sidebarToggle: '.btn-toggle-calendar-sidebar',
            addEventSidebar: '#addEventSidebar',
            overlay: '.app-overlay'
        }
    };

    /**
     * Calendar Manager Class
     */
    class WorkspaceCalendar {
        constructor() {
            this.calendar = null;
            this.elements = {};
            this.events = [];
            this.isRendered = false;
            
            this.init();
        }

        /**
         * Initialize calendar and elements
         */
        init() {
            this.cacheElements();
            this.loadEvents();
            this.initCalendar();
            this.attachEventListeners();
            this.handleInitialRender();
        }

        /**
         * Cache DOM elements
         */
        cacheElements() {
            const { selectors } = CalendarConfig;
            
            this.elements = {
                calendar: document.querySelector(selectors.calendar),
                calendarTab: document.querySelector(selectors.calendarTab),
                calendarPane: document.querySelector(selectors.calendarPane),
                sidebar: document.querySelector(selectors.sidebar),
                sidebarToggle: document.querySelector(selectors.sidebarToggle),
                addEventSidebar: document.querySelector(selectors.addEventSidebar),
                overlay: document.querySelector(selectors.overlay)
            };
        }

        /**
         * Load events (placeholder - replace with AJAX call)
         */
        loadEvents() {
            const currentDate = new Date();
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            this.events = [
                {
                    id: 1,
                    url: '',
                    title: 'Design Review',
                    start: new Date(year, month, 1),
                    end: new Date(year, month, 2),
                    allDay: true,
                    extendedProps: { calendar: 'Business' }
                },
                {
                    id: 2,
                    url: '',
                    title: 'Client Meeting',
                    start: new Date(year, month, 10),
                    end: new Date(year, month, 10),
                    allDay: true,
                    extendedProps: { calendar: 'Personal' }
                }
            ];
        }

        /**
         * Initialize FullCalendar
         */
        initCalendar() {
            if (!this.elements.calendar) return;

            const self = this;
            const { colors } = CalendarConfig;

            this.calendar = new FullCalendar.Calendar(this.elements.calendar, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                events: this.events.map(e => ({
                    ...e,
                    classNames: ['bg-label-' + colors[e.extendedProps.calendar]]
                })),
                editable: true,
                dragScroll: true,
                dayMaxEvents: 2,
                eventResizableFromStart: true,
                windowResize() {
                    self.calendar.render();
                },
                eventClick(info) {
                    self.handleEventClick(info);
                },
                dateClick(info) {
                    self.handleDateClick(info);
                }
            });
        }

        /**
         * Attach event listeners
         */
        attachEventListeners() {
            // Tab shown event - render calendar when tab becomes visible
            if (this.elements.calendarTab) {
                this.elements.calendarTab.addEventListener('shown.bs.tab', () => {
                    // Use setTimeout to ensure DOM is fully ready and visible
                    setTimeout(() => {
                        this.renderCalendar();
                    }, 50);
                });
            }

            // Mobile sidebar toggle
            if (this.elements.sidebarToggle && this.elements.sidebar) {
                this.elements.sidebarToggle.addEventListener('click', () => {
                    this.elements.sidebar.classList.toggle('show');
                });
            }
        }

        /**
         * Handle initial render if calendar tab is active
         */
        handleInitialRender() {
            const isCalendarActive = this.elements.calendarPane && 
                (this.elements.calendarPane.classList.contains('show') || 
                 this.elements.calendarPane.classList.contains('active'));

            if (isCalendarActive) {
                // Delay render to ensure all styles are loaded
                setTimeout(() => {
                    this.renderCalendar();
                }, 100);
            }
        }

        /**
         * Render calendar with size update
         */
        renderCalendar() {
            if (!this.calendar) return;
            
            // Only render once, then just update size
            if (!this.isRendered) {
                this.calendar.render();
                this.isRendered = true;
            }
            
            // Always update size when tab is shown
            this.calendar.updateSize();
        }

        /**
         * Handle event click
         */
        handleEventClick(info) {
            if (!this.elements.addEventSidebar) return;

            const bsOffcanvas = new bootstrap.Offcanvas(this.elements.addEventSidebar);
            bsOffcanvas.show();

            // Populate form (example)
            const eventTitle = document.getElementById('eventTitle');
            if (eventTitle) {
                eventTitle.value = info.event.title;
            }
        }

        /**
         * Handle date click
         */
        handleDateClick(info) {
            if (!this.elements.addEventSidebar) return;

            const bsOffcanvas = new bootstrap.Offcanvas(this.elements.addEventSidebar);
            bsOffcanvas.show();

            const eventStartDate = document.getElementById('eventStartDate');
            const eventEndDate = document.getElementById('eventEndDate');

            if (eventStartDate) eventStartDate.value = info.dateStr;
            if (eventEndDate) eventEndDate.value = info.dateStr;
        }

        /**
         * Public API: Add event
         */
        addEvent(eventData) {
            if (this.calendar) {
                this.calendar.addEvent(eventData);
            }
        }

        /**
         * Public API: Remove event
         */
        removeEvent(eventId) {
            if (this.calendar) {
                const event = this.calendar.getEventById(eventId);
                if (event) {
                    event.remove();
                }
            }
        }

        /**
         * Public API: Refresh events from server
         */
        async refreshEvents(url) {
            try {
                const response = await fetch(url);
                const events = await response.json();
                
                if (this.calendar) {
                    this.calendar.removeAllEvents();
                    events.forEach(event => this.calendar.addEvent(event));
                }
            } catch (error) {
                console.error('Error refreshing calendar events:', error);
            }
        }
    }

    /**
     * Initialize on DOM ready
     */
    document.addEventListener('DOMContentLoaded', function() {
        window.workspaceCalendar = new WorkspaceCalendar();
    });

})();
