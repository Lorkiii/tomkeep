/**
 * Alpine.js component for Attendance Calendar
 * Features: display calendar, click day for details, navigate months
 */

export default function attendanceCalendar() {
    return {
        // State
        selectedDay: null,
        showDayModal: false,
        dayNames: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],

        calendarDays: [],
        fromDate: '',
        toDate: '',
        currentMonth: '',
        totalHoursValue: 0,

        init() {
            this.fromDate = this.$el.dataset.fromDate || '';
            this.toDate = this.$el.dataset.toDate || '';
            this.currentMonth = this.$el.dataset.currentMonth || '';
            this.totalHoursValue = parseFloat(this.$el.dataset.totalHours || 0);

            if (this.$refs.calendarDaysJson && this.$refs.calendarDaysJson.textContent) {
                const raw = this.$refs.calendarDaysJson.textContent.trim();
                try {
                    this.calendarDays = JSON.parse(raw) || [];
                } catch (e) {
                    console.error('[attendanceCalendar] Failed to parse calendarDays JSON', e);
                    console.error('[attendanceCalendar] JSON preview:', raw.slice(0, 200));
                    this.calendarDays = [];
                }
            } else {
                console.warn('[attendanceCalendar] Missing calendarDaysJson ref or empty content');
            }

            console.debug('[attendanceCalendar] init', {
                fromDate: this.fromDate,
                toDate: this.toDate,
                currentMonth: this.currentMonth,
                totalHoursValue: this.totalHoursValue,
                calendarDaysCount: Array.isArray(this.calendarDays) ? this.calendarDays.length : null,
            });

            // Listen for calendar data updates from Livewire
            this.$wire.on('calendarDataUpdated', (payload) => {
                if (payload && payload.calendarDays) {
                    this.calendarDays = payload.calendarDays;
                    this.fromDate = payload.fromDate;
                    this.toDate = payload.toDate;
                    this.currentMonth = payload.currentMonth;
                    this.totalHoursValue = parseFloat(payload.totalHours || 0);
                }
            });
        },

        // Get total hours formatted
        get totalHours() {
            const hours = this.totalHoursValue;
            return hours > 0 ? hours.toFixed(1) + ' total hours' : 'No hours recorded';
        },

        // Navigate to previous month
        previousMonth() {
            this.$wire.navigateMonth('previous');
        },

        // Navigate to next month
        nextMonth() {
            this.$wire.navigateMonth('next');
        },

        // Open day detail modal
        openDayModal(day) {
            if (day.hasRecords) {
                this.selectedDay = day;
                this.showDayModal = true;
            }
        },

        // Close day detail modal
        closeDayModal() {
            this.showDayModal = false;
            this.selectedDay = null;
        },

        // Format date for display
        formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        },

        // Get day of week (0-6) for grid positioning
        getDayOfWeek(dateStr) {
            return new Date(dateStr).getDay();
        },

        // Check if day is today
        isToday(dateStr) {
            return new Date(dateStr).toDateString() === new Date().toDateString();
        },

        // Generate calendar grid with empty cells for alignment
        get calendarGrid() {
            const days = this.calendarDays;
            if (!days || days.length === 0) return [];

            // Get first day of the range
            const firstDate = new Date(days[0].date);
            const startDayOfWeek = firstDate.getDay();

            // Build grid with empty cells at start
            const grid = [];

            // Add empty cells for days before the first date
            for (let i = 0; i < startDayOfWeek; i++) {
                grid.push({ isEmpty: true, date: null });
            }

            // Add actual days
            days.forEach(day => {
                grid.push({
                    ...day,
                    isEmpty: false,
                    isToday: this.isToday(day.date)
                });
            });

            return grid;
        }
    };
}
