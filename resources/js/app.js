import './bootstrap';
import Chart from 'chart.js/auto';
import attendanceChart from './components/attendanceChart.js';
import attendanceCalendar from './components/attendanceCalendar.js';

// Make Chart available globally for Alpine.js
window.Chart = Chart;

// Chart.js global configuration
Chart.defaults.font.family = 'ui-sans-serif, system-ui, sans-serif';
Chart.defaults.color = '#64748b';

// Register Alpine.js components after Alpine loads
document.addEventListener('alpine:init', () => {
    Alpine.data('attendanceChart', attendanceChart);
    Alpine.data('attendanceCalendar', attendanceCalendar);
});
