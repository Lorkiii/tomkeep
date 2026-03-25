/**
 * Alpine.js component for Attendance Chart
 * Reusable Chart.js line chart for attendance reports
 */

export default function attendanceChart() {
    return {
        chartInstance: null,

        init() {
            const ctx = this.$refs.canvas.getContext('2d');
            // @js() outputs JS arrays directly, not JSON strings
            const labels = this.$el.dataset.labels ? JSON.parse(this.$el.dataset.labels) : [];
            const data = this.$el.dataset.values ? JSON.parse(this.$el.dataset.values) : [];

            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Worked Hours',
                        data: data,
                        borderColor: '#1e4fa3',
                        backgroundColor: 'rgba(30, 79, 163, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#1e4fa3',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: (context) => context.parsed.y.toFixed(2) + ' hours'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { color: '#64748b' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b', maxRotation: 45 }
                        }
                    },
                    interaction: { intersect: false, mode: 'index' }
                }
            });

            // Listen for Livewire chart data updates
            this.$wire.on('chartDataUpdated', (payload) => {
                if (payload && payload.labels && payload.data) {
                    this.updateChart(payload.labels, payload.data);
                }
            });
        },

        updateChart(labels, data) {
            if (!this.chartInstance) return;
            this.chartInstance.data.labels = labels;
            this.chartInstance.data.datasets[0].data = data;
            this.chartInstance.update('none');
        },

        destroy() {
            if (this.chartInstance) {
                this.chartInstance.destroy();
                this.chartInstance = null;
            }
        }
    };
}
