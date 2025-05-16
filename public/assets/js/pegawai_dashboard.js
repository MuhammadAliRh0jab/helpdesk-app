document.addEventListener('DOMContentLoaded', function () {
    // Configure Axios with CSRF token and credentials
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    axios.defaults.withCredentials = true;

    // Initial data from backend (fallback if undefined)
    const initialTicketStats = window.initialTicketStats || { resolved: 0, created: 0, assigned: 0 };
    const initialAvgResolutionTime = window.initialAvgResolutionTime || 0;

    // Elements
    const resolvedCountElement = document.getElementById('resolved-tickets');
    const createdCountElement = document.getElementById('created-tickets');
    const assignedCountElement = document.getElementById('assigned-tickets');
    const avgResolutionElement = document.getElementById('avg-resolution-time');
    const pendingPercentElement = document.getElementById('pending-percent');
    const assignedPercentElement = document.getElementById('assigned-percent');
    const completedPercentElement = document.getElementById('completed-percent');

    // Update initial displays
    try {
        if (resolvedCountElement) resolvedCountElement.textContent = initialTicketStats.resolved || 0;
        if (createdCountElement) createdCountElement.textContent = initialTicketStats.created || 0;
        if (assignedCountElement) assignedCountElement.textContent = initialTicketStats.assigned || 0;
        if (avgResolutionElement) avgResolutionElement.textContent = `${initialAvgResolutionTime.toFixed(2)} hari`;
    } catch (err) {
        console.error('Error updating initial displays:', err.message);
    }

    // Initialize chart variables
    window.ticketStatsChart = null;
    window.resolutionChart = null;
    window.ticketDistributionChart = null;
    window.serviceDistributionChart = null;

    // Setup charts and load data
    setupTicketStatsChart(initialTicketStats);
    setupResolutionTimeChart(initialAvgResolutionTime);
    setupTicketDistributionChart();
    setupServiceDistributionChart();
    loadRecentTickets();

    // 1. Ticket Stats Chart (Bar Chart)
    function setupTicketStatsChart(ticketStats) {
    const chartContainer = document.getElementById('ticketStatsChart');
    if (!chartContainer) {
        console.error('Canvas element for ticketStatsChart not found.');
        return;
    }

    chartContainer.style.height = '300px';
    const ctx = chartContainer.getContext('2d');

    axios.get('/api/pegawai-ticket-stats')
        .then(response => {
            const { resolved = 0, created = 0, assigned = 0 } = response.data || {};

            if (window.ticketStatsChart instanceof Chart) {
                window.ticketStatsChart.destroy();
            }

            window.ticketStatsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Tiket Diselesaikan', 'Tiket Diajukan', 'Tiket Ditugaskan'],
                    datasets: [{
                        label: 'Jumlah Tiket',
                        data: [resolved, created, assigned],
                        backgroundColor: ['#36A2EB', '#FF6384', '#4BC0C0'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Statistik Tiket Pegawai' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Jumlah' }
                        }
                    },
                    animation: false
                }
            });

            // Update counts
            if (resolvedCountElement) resolvedCountElement.textContent = resolved;
            if (createdCountElement) createdCountElement.textContent = created;
            if (assignedCountElement) assignedCountElement.textContent = assigned;
        })
        .catch(error => {
            console.error('Error loading ticket stats:', error.response?.data || error.message);
        });
}

    // 2. Resolution Time Chart (Bar Chart per Service)
    function setupResolutionTimeChart(avgResolutionTime) {
        const chartContainer = document.getElementById('resolutionTimeChart');
        if (!chartContainer) {
            console.error('Canvas element for resolutionTimeChart not found.');
            return;
        }

        chartContainer.style.height = '300px';
        const ctx = chartContainer.getContext('2d');

        axios.get('/api/pegawai-resolution-times')
            .then(response => {
                const { services = [], avgResolutionDays = [] } = response.data || {};

                const sanitizedAvgResolutionDays = avgResolutionDays.map(value => {
                    const num = Number(value);
                    return isNaN(num) || !isFinite(num) ? 0 : num;
                });

                const avg = sanitizedAvgResolutionDays.length
                    ? sanitizedAvgResolutionDays.reduce((a, b) => a + b, 0) / sanitizedAvgResolutionDays.length
                    : avgResolutionTime;

                if (window.resolutionChart instanceof Chart) {
                    window.resolutionChart.destroy();
                }

                window.resolutionChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: services.length ? services : ['Rata-rata'],
                        datasets: [{
                            label: 'Rata-rata Waktu (Hari)',
                            data: sanitizedAvgResolutionDays.length ? sanitizedAvgResolutionDays : [avgResolutionTime],
                            backgroundColor: '#4361EE',
                            borderRadius: 5,
                            maxBarThickness: 30
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        aspectRatio: 2,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.raw} Hari`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: 'Hari' } },
                            x: { 
                                grid: { display: false },
                                ticks: { autoSkip: true, maxRotation: 45, minRotation: 45 }
                            }
                        },
                        animation: false
                    }
                });

                if (avgResolutionElement) avgResolutionElement.textContent = `${avg.toFixed(2)} hari`;
            })
            .catch(error => {
                console.error('Error loading resolution times:', error.response?.data || error.message);
            });
    }

    // 3. Ticket Distribution Chart (Pie Chart)
    function setupTicketDistributionChart() {
    const chartContainer = document.getElementById('ticketDistributionChart');
    if (!chartContainer) {
        console.error('Canvas element for ticketDistributionChart not found.');
        return;
    }

    const ctx = chartContainer.getContext('2d');

    axios.get('/api/pegawai-ticket-distribution')
        .then(response => {
            const { labels, data, percentages } = response.data;

            if (window.ticketDistributionChart instanceof Chart) {
                window.ticketDistributionChart.destroy();
            }

            window.ticketDistributionChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#28A745', '#FFC107', '#17A2B8'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    animation: false
                }
            });

            // Update percentages
            if (completedPercentElement) completedPercentElement.textContent = `${percentages.completed.toFixed(1)}%`;
            if (pendingPercentElement) pendingPercentElement.textContent = `${percentages.pending.toFixed(1)}%`;
            if (assignedPercentElement) assignedPercentElement.textContent = `${percentages.assigned.toFixed(1)}%`;
        })
        .catch(error => {
            console.error('Error loading ticket distribution:', error.response?.data || error.message);
        });
}

    // 4. Service Distribution Chart (Horizontal Bar Chart)
    function setupServiceDistributionChart() {
        const chartContainer = document.getElementById('serviceDistributionChart');
        if (!chartContainer) {
            console.error('Canvas element for serviceDistributionChart not found.');
            return;
        }

        chartContainer.style.height = '300px';
        const ctx = chartContainer.getContext('2d');

        axios.get('/api/service-distribution') // Gunakan endpoint yang ada
            .then(response => {
                const { labels = [], counts = [] } = response.data || {};

                if (window.serviceDistributionChart instanceof Chart) {
                    window.serviceDistributionChart.destroy();
                }

                window.serviceDistributionChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Tiket',
                            data: counts,
                            backgroundColor: '#FF6384',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y', // Horizontal bar
                        responsive: true,
                        maintainAspectRatio: true,
                        aspectRatio: 2,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.raw} Tiket`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: { beginAtZero: true, title: { display: true, text: 'Jumlah Tiket' } },
                            y: { grid: { display: false } }
                        },
                        animation: false
                    }
                });
            })
            .catch(error => {
                console.error('Error loading service distribution:', error.response?.data || error.message);
            });
    }

    // 5. Load Recent Tickets
    function loadRecentTickets() {
        const tableBody = document.getElementById('recent-tickets-table');
        if (!tableBody) {
            console.error('Table element for recent-tickets-table not found.');
            return;
        }

        axios.get('/api/pegawai-recent-tickets')
            .then(response => {
                const tickets = response.data || [];
                tableBody.innerHTML = '';
                if (tickets.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="4" class="text-center">Tidak ada tiket terbaru.</td></tr>';
                    return;
                }
                tickets.forEach(ticket => {
                    const statusBadge = getStatusBadge(ticket.status || 'unknown');
                    const row = `
                        <tr>
                            <td class="text-ellipsis">${ticket.code || 'N/A'}</td>
                            <td class="text-ellipsis">${ticket.title || 'No Title'}</td>
                            <td class="text-ellipsis">${ticket.created_at ? new Date(ticket.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : 'N/A'}</td>
                            <td class="text-center">${statusBadge}</td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
            })
            .catch(error => {
                console.error('Error loading recent tickets:', error.response?.data || error.message);
                tableBody.innerHTML = '<tr><td colspan="4" class="text-center">Gagal memuat tiket.</td></tr>';
            });
    }

    // Helper function to get status badge
    function getStatusBadge(status) {
        const statusStr = String(status || '').toLowerCase();
        switch (statusStr) {
            case 'completed':
                return '<span class="badge bg-success">Selesai</span>';
            case 'pending':
                return '<span class="badge bg-warning">Pending</span>';
            case 'assigned':
                return '<span class="badge bg-info">Ditugaskan</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }
});