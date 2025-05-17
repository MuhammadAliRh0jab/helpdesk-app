/* Configure Axios with CSRF token and credentials */
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
axios.defaults.withCredentials = true;

// Initial data from backend (fallback if undefined)
const initialTicketStats = window.initialTicketStats || { resolved: 0, created: 0, assigned: 0 };

// DOM Elements
const resolvedCountElement = document.getElementById('resolved-tickets');
const createdCountElement = document.getElementById('created-tickets');
const assignedCountElement = document.getElementById('assigned-tickets');
const pendingPercentElement = document.getElementById('pending-percent');
const assignedPercentElement = document.getElementById('assigned-percent');
const completedPercentElement = document.getElementById('completed-percent');
const handlerPendingPercentElement = document.getElementById('handler-pending-percent');
const handlerCompletedPercentElement = document.getElementById('handler-completed-percent');

// Chart Instances
let ticketStatsChart = null;
let ticketDistributionChart = null;
let handlerTicketDistributionChart = null;
let resolutionByServiceChart = null;
let assignmentCompletionChart = null;

// Initialize Dashboard
document.addEventListener('DOMContentLoaded', () => {
    updateInitialDisplays();
    setupTicketStatsChart(initialTicketStats);
    setupTicketDistributionChart();
    loadRecentTickets();

    // Setup tab event listener
    const dashboardTabs = document.getElementById('dashboardTabs');
    if (dashboardTabs) {
        dashboardTabs.addEventListener('shown.bs.tab', function (event) {
            const activeTab = event.target.getAttribute('href').substring(1);
            if (activeTab === 'creator') {
                setupTicketStatsChart(initialTicketStats);
                setupTicketDistributionChart();
                loadRecentTickets();
            } else if (activeTab === 'handler') {
                setupHandlerTicketDistributionChart();
                setupResolutionByServiceChart();
                setupAssignmentCompletionChart();
                loadTicketList();
            }
        });
    }

    // Setup time range event listeners for both charts
    setupTimeRangeListeners();
});

/* Update Initial Displays */
function updateInitialDisplays() {
    try {
        if (resolvedCountElement) resolvedCountElement.textContent = initialTicketStats.resolved || 0;
        if (createdCountElement) createdCountElement.textContent = initialTicketStats.created || 0;
        if (assignedCountElement) assignedCountElement.textContent = initialTicketStats.assigned || 0;
    } catch (err) {
        console.error('Error updating initial displays:', err.message);
    }
}

/* Setup Time Range Listeners */
function setupTimeRangeListeners() {
    // Ticket Stats Chart (Creator Tab)
    document.querySelectorAll('#ticket-stats-time-range .dropdown-item[data-time-range]')?.forEach(item => {
        item.addEventListener('click', function() {
            const timeRange = this.getAttribute('data-time-range');
            console.log('Ticket Stats Time Range Selected:', timeRange); // Debugging
            document.querySelectorAll('#ticket-stats-time-range .dropdown-item[data-time-range]').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            if (window.ticketStatsDatePicker) {
                window.ticketStatsDatePicker.clear();
            }
            setupTicketStatsChart(initialTicketStats);
        });
    });

    // Assignment Completion Chart (Handler Tab)
    document.querySelectorAll('#assignment-completion-time-range .dropdown-item[data-time-range]')?.forEach(item => {
        item.addEventListener('click', function() {
            const timeRange = this.getAttribute('data-time-range');
            console.log('Assignment Completion Time Range Selected:', timeRange); // Debugging
            document.querySelectorAll('#assignment-completion-time-range .dropdown-item[data-time-range]').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            if (window.assignmentCompletionDatePicker) {
                window.assignmentCompletionDatePicker.clear();
            }
            setupAssignmentCompletionChart();
        });
    });

    // Flatpickr for Ticket Stats Chart
    const ticketStatsDatePickerElement = document.getElementById('ticket-stats-date-picker');
    if (ticketStatsDatePickerElement) {
        const ticketStatsDatePicker = flatpickr('#ticket-stats-date-picker', {
            mode: 'range',
            dateFormat: 'Y-m-d',
            defaultDate: [new Date(), new Date()],
            maxDate: new Date(),
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const startDate = selectedDates[0].toISOString().split('T')[0];
                    const endDate = selectedDates[1].toISOString().split('T')[0];
                    console.log('Ticket Stats Custom Range Selected:', { startDate, endDate }); // Debugging
                    document.querySelectorAll('#ticket-stats-time-range .dropdown-item[data-time-range]').forEach(i => i.classList.remove('active'));
                    document.getElementById('ticket-stats-custom-range')?.classList.add('active');
                    setupTicketStatsChartWithCustomRange(initialTicketStats, startDate, endDate);
                } else {
                    console.warn('Please select a valid date range for Ticket Stats.');
                }
            }
        });
        window.ticketStatsDatePicker = ticketStatsDatePicker;

        document.getElementById('ticket-stats-custom-range')?.addEventListener('click', function() {
            if (window.ticketStatsDatePicker) {
                window.ticketStatsDatePicker.open();
            }
        });
    }

    // Flatpickr for Assignment Completion Chart
    const assignmentCompletionDatePickerElement = document.getElementById('assignment-completion-date-picker');
    if (assignmentCompletionDatePickerElement) {
        const assignmentCompletionDatePicker = flatpickr('#assignment-completion-date-picker', {
            mode: 'range',
            dateFormat: 'Y-m-d',
            defaultDate: [new Date(), new Date()],
            maxDate: new Date(),
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const startDate = selectedDates[0].toISOString().split('T')[0];
                    const endDate = selectedDates[1].toISOString().split('T')[0];
                    console.log('Assignment Completion Custom Range Selected:', { startDate, endDate }); // Debugging
                    document.querySelectorAll('#assignment-completion-time-range .dropdown-item[data-time-range]').forEach(i => i.classList.remove('active'));
                    document.getElementById('assignment-completion-custom-range')?.classList.add('active');
                    setupAssignmentCompletionChartWithCustomRange(startDate, endDate);
                } else {
                    console.warn('Please select a valid date range for Assignment Completion.');
                }
            }
        });
        window.assignmentCompletionDatePicker = assignmentCompletionDatePicker;

        document.getElementById('assignment-completion-custom-range')?.addEventListener('click', function() {
            if (window.assignmentCompletionDatePicker) {
                window.assignmentCompletionDatePicker.open();
            }
        });
    }
}

/* 1. Ticket Stats Chart (Line Chart with Fill) - Creator Tab */
function setupTicketStatsChart(ticketStats) {
    setupTicketStatsChartWithCustomRange(ticketStats, null, null);
}

function setupTicketStatsChartWithCustomRange(ticketStats, startDate, endDate) {
    const chartContainer = document.getElementById('ticketStatsChart');
    if (!chartContainer) {
        console.error('Canvas element for ticketStatsChart not found.');
        return;
    }

    chartContainer.style.height = '300px';
    const ctx = chartContainer.getContext('2d');

    const params = {};
    if (startDate && endDate) {
        params.custom_start = startDate;
        params.custom_end = endDate;
    } else {
        const timeRange = document.querySelector('#ticket-stats-time-range .dropdown-item.active[data-time-range]')?.getAttribute('data-time-range') || 'week';
        console.log('Fetching Ticket Stats with time_range:', timeRange); // Debugging
        params.time_range = timeRange;
    }

    axios.get('/api/pegawai-ticket-stats', { params })
        .then(response => {
            const { resolved = 0, created = 0, assigned = 0 } = response.data.totalStats || {};
            const createdPending = response.data.created?.pending?.data || [];
            const createdCompleted = response.data.created?.completed?.data || [];
            const createdLabels = response.data.created?.pending?.labels || [];

            if (ticketStatsChart instanceof Chart) {
                ticketStatsChart.destroy();
            }

            ticketStatsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: createdLabels,
                    datasets: [
                        {
                            label: 'Tiket Dibuat',
                            data: createdPending,
                            backgroundColor: 'rgba(67, 97, 238, 0.1)',
                            borderColor: '#4361EE',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#4361EE'
                        },
                        {
                            label: 'Tiket Selesai',
                            data: createdCompleted,
                            backgroundColor: 'rgba(28, 177, 120, 0.1)',
                            borderColor: '#1CB178',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#1CB178'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'end' },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.parsed.y;
                                    return `${label}: ${value}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { display: true, drawBorder: false, color: 'rgba(200, 200, 200, 0.15)' },
                            ticks: { stepSize: 5 }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { autoSkip: true, maxTicksLimit: 10, maxRotation: 45, minRotation: 0 }
                        }
                    }
                }
            });

            if (resolvedCountElement) resolvedCountElement.textContent = resolved;
            if (createdCountElement) createdCountElement.textContent = created;
            if (assignedCountElement) assignedCountElement.textContent = assigned;
        })
        .catch(error => {
            console.error('Error loading ticket stats:', error.response?.data || error.message);
        });
}

/* 2. Ticket Distribution Chart (Doughnut Chart) - Creator Tab */
function setupTicketDistributionChart() {
    const chartContainer = document.getElementById('ticketDistributionChart');
    if (!chartContainer) {
        console.error('Canvas element for ticketDistributionChart not found.');
        return;
    }

    const ctx = chartContainer.getContext('2d');

    axios.get('/api/pegawai-ticket-distribution-created')
        .then(response => {
            console.log('Ticket Distribution Response:', response.data);
            const { labels, data, percentages } = response.data || { labels: [], data: [], percentages: {} };

            const hasData = data && data.length && data.some(val => val > 0);
            if (!hasData) {
                console.warn('No ticket distribution data available.');
                chartContainer.parentElement.innerHTML = '<p class="text-center text-muted">Tidak ada data distribusi tiket.</p>';
                if (completedPercentElement) completedPercentElement.textContent = '0%';
                if (pendingPercentElement) pendingPercentElement.textContent = '0%';
                if (assignedPercentElement) assignedPercentElement.textContent = '0%';
                return;
            }

            if (ticketDistributionChart instanceof Chart) {
                ticketDistributionChart.destroy();
            }

            ticketDistributionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#1CB178', '#F9C74F', '#4361EE'],
                        borderWidth: 0,
                        cutout: '60%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                boxWidth: 14,
                                boxHeight: 14,
                                padding: 16,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percent = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                                    return `${label}: ${value} (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });

            if (completedPercentElement) completedPercentElement.textContent = `${(percentages.completed || 0).toFixed(1)}%`;
            if (pendingPercentElement) pendingPercentElement.textContent = `${(percentages.pending || 0).toFixed(1)}%`;
            if (assignedPercentElement) assignedPercentElement.textContent = `${(percentages.assigned || 0).toFixed(1)}%`;
        })
        .catch(error => {
            console.error('Error loading ticket distribution:', error.response?.data || error.message);
        });
}

/* 3. Handler Ticket Distribution Chart (Pie Chart) - Handler Tab */
function setupHandlerTicketDistributionChart() {
    const chartContainer = document.getElementById('handlerTicketDistributionChart');
    if (!chartContainer) {
        console.error('Canvas element for handlerTicketDistributionChart not found.');
        return;
    }

    const ctx = chartContainer.getContext('2d');

    axios.get('/api/pegawai-ticket-distribution')
        .then(response => {
            console.log('Handler Ticket Distribution Response:', response.data);
            const { labels, data, percentages } = response.data || { labels: ['Belum Selesai', 'Selesai'], data: [0, 0], percentages: { pending: 0, completed: 0 } };
            console.log('Processed Data:', { labels, data, percentages });

            const hasData = data && data.length && data.some(val => val > 0);
            if (!hasData) {
                console.warn('No handler ticket distribution data available.');
                chartContainer.parentElement.innerHTML = '<p class="text-center text-muted">Tidak ada data distribusi tiket.</p>';
                if (handlerCompletedPercentElement) handlerCompletedPercentElement.textContent = '0%';
                if (handlerPendingPercentElement) handlerPendingPercentElement.textContent = '0%';
                return;
            }

            if (handlerTicketDistributionChart instanceof Chart) {
                handlerTicketDistributionChart.destroy();
            }

            handlerTicketDistributionChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#F9C74F', '#1CB178'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percent = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                                    return `${label}: ${value} (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });

            if (handlerCompletedPercentElement) handlerCompletedPercentElement.textContent = `${(percentages.completed || 0).toFixed(1)}%`;
            if (handlerPendingPercentElement) handlerPendingPercentElement.textContent = `${(percentages.pending || 0).toFixed(1)}%`;
        })
        .catch(error => {
            console.error('Error loading handler ticket distribution:', error.response?.data || error.message);
        });
}

/* 4. Resolution Time by Service Chart (Bar Chart) - Handler Tab */
function setupResolutionByServiceChart() {
    const chartContainer = document.getElementById('resolutionByServiceChart');
    if (!chartContainer) {
        console.error('Canvas element for resolutionByServiceChart not found.');
        return;
    }

    const ctx = chartContainer.getContext('2d');

    axios.get('/api/pegawai-resolution-time-by-service')
        .then(response => {
            console.log('Raw Resolution Time by Service Response:', response.data);
            const { labels = [], data = [] } = response.data || {};
            const sanitizedData = data.map(item => {
                const value = Number(item);
                return isNaN(value) ? 0 : value;
            });
            console.log('Sanitized Data:', { labels, sanitizedData });

            const testAIndex = labels.indexOf('Test A');
            if (testAIndex !== -1) {
                console.log('Test A Data:', sanitizedData[testAIndex]);
            } else {
                console.warn('Test A not found in labels:', labels);
            }

            const hasData = sanitizedData && sanitizedData.length && sanitizedData.some(val => val >= 0);
            if (!hasData) {
                console.warn('No resolution time by service data available.');
                chartContainer.parentElement.innerHTML = '<p class="text-center text-muted">Tidak ada data waktu penyelesaian per service.</p>';
                return;
            }

            if (resolutionByServiceChart instanceof Chart) {
                resolutionByServiceChart.destroy();
            }

            resolutionByServiceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Rata-rata Waktu (Hari)',
                        data: sanitizedData,
                        backgroundColor: '#4361EE', // Match btn-alt-primary color
                        borderWidth: 0,
                        borderRadius: 5, // Rounded corners for bars
                        barThickness: 30 // Fixed bar width
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 14,
                                    family: "'Roboto', sans-serif",
                                    weight: '600'
                                },
                                color: '#343a40', // Match block-title color
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14,
                                family: "'Roboto', sans-serif",
                                weight: '600'
                            },
                            bodyFont: {
                                size: 12,
                                family: "'Roboto', sans-serif"
                            },
                            padding: 10,
                            cornerRadius: 5,
                            callbacks: {
                                label: function(context) {
                                    return `${context.raw.toFixed(2)} Hari`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            title: {
                                display: true,
                                text: 'Hari',
                                font: {
                                    size: 16,
                                    family: "'Roboto', sans-serif",
                                    weight: '600'
                                },
                                color: '#343a40', // Match block-title color
                                padding: 10
                            },
                            ticks: { 
                                precision: 2,
                                callback: function(value) {
                                    return value.toFixed(2);
                                },
                                font: {
                                    size: 12,
                                    family: "'Roboto', sans-serif"
                                },
                                color: '#6c757d', // Match text-muted color
                            },
                            min: 0,
                            suggestedMax: Math.max(...sanitizedData) + 1 || 5, // Ensure small values are visible
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Layanan',
                                font: {
                                    size: 16,
                                    family: "'Roboto', sans-serif",
                                    weight: '600'
                                },
                                color: '#343a40', // Match block-title color
                                padding: 10
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    family: "'Roboto', sans-serif"
                                },
                                color: '#6c757d', // Match text-muted color
                                maxRotation: 45,
                                minRotation: 45
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading resolution time by service:', error.response ? error.response.data : error.message);
            chartContainer.parentElement.innerHTML = '<p class="text-center text-muted">Gagal memuat data. Silakan coba lagi nanti.</p>';
        });
}

/* 5. Assignment and Completion Time Series Chart - Handler Tab */
function setupAssignmentCompletionChart() {
    setupAssignmentCompletionChartWithCustomRange(null, null);
}

function setupAssignmentCompletionChartWithCustomRange(startDate, endDate) {
    const chartContainer = document.getElementById('assignmentCompletionChart');
    if (!chartContainer) {
        console.error('Canvas element for assignmentCompletionChart not found.');
        return;
    }

    const ctx = chartContainer.getContext('2d');

    const params = {};
    if (startDate && endDate) {
        params.custom_start = startDate;
        params.custom_end = endDate;
    } else {
        const timeRange = document.querySelector('#assignment-completion-time-range .dropdown-item.active[data-time-range]')?.getAttribute('data-time-range') || 'week';
        console.log('Fetching Assignment Completion with time_range:', timeRange); // Debugging
        params.time_range = timeRange;
    }

    axios.get('/api/pegawai-assignment-completion', { params })
        .then(response => {
            console.log('Assignment Completion Response:', response.data);
            const { labels = [], assignedData = [], completedData = [] } = response.data || {};
            console.log('Processed Data:', { labels, assignedData, completedData });

            const hasData = (assignedData.length > 0 || completedData.length > 0) && labels.length > 0;
            if (!hasData) {
                console.warn('No assignment or completion data available.');
                chartContainer.parentElement.innerHTML = '<p class="text-center text-muted">Tidak ada data penugasan atau penyelesaian.</p>';
                return;
            }

            if (assignmentCompletionChart instanceof Chart) {
                assignmentCompletionChart.destroy();
            }

            assignmentCompletionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Ditugaskan',
                            data: assignedData,
                            borderColor: '#4361EE',
                            backgroundColor: 'rgba(67, 97, 238, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4
                        },
                        {
                            label: 'Selesai',
                            data: completedData,
                            borderColor: '#1CB178',
                            backgroundColor: 'rgba(28, 177, 120, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Jumlah' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading assignment completion:', error.response?.data || error.message);
        });
}

/* 6. Load Recent Tickets - Creator Tab */
function loadRecentTickets() {
    const tableBody = document.getElementById('recent-tickets-table');
    if (!tableBody) {
        console.error('Table element for recent-tickets-table not found.');
        return;
    }

    axios.get('/api/pegawai-recent-tickets')
        .then(response => {
            console.log('Recent Tickets Response:', response.data);
            const tickets = response.data || [];
            tableBody.innerHTML = '';

            if (tickets.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada tiket terbaru.</td></tr>';
                return;
            }

            tickets.forEach(ticket => {
                const statusBadge = getStatusBadge(ticket.status || 'unknown');
                const row = `
                    <tr>
                        <td class="text-ellipsis">${ticket.code || 'N/A'}</td>
                        <td class="text-ellipsis">${ticket.title || 'No Title'}</td>
                        <td class="text-ellipsis">${ticket.created_at ? new Date(ticket.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : 'Belum Ada'}</td>
                        <td class="text-ellipsis">${ticket.unit_name || 'N/A'}</td>
                        <td class="text-center">${statusBadge}</td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
        })
        .catch(error => {
            console.error('Error loading recent tickets:', error.response?.data || error.message);
        });
}

/* 7. Load Ticket List (To Be Completed and Completed) - Handler Tab */
function loadTicketList() {
    const tableBody = document.getElementById('ticket-list-table');
    if (!tableBody) {
        console.error('Table element for ticket-list-table not found.');
        return;
    }

    axios.get('/api/pegawai-ticket-list')
        .then(response => {
            console.log('Ticket List Response:', response.data);
            const tickets = response.data || [];
            tableBody.innerHTML = '';

            if (tickets.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada tiket.</td></tr>';
                return;
            }

            tickets.forEach(ticket => {
                const statusBadge = getStatusBadge(ticket.status);
                const completedDate = ticket.status == 2 ? (ticket.updated_at ? new Date(ticket.updated_at).toLocaleDateString('id-ID') : 'Belum Selesai') : 'Belum Selesai';
                const row = `
                    <tr>
                        <td class="text-ellipsis">${ticket.code || 'N/A'}</td>
                        <td class="text-ellipsis">${ticket.title || 'No Title'}</td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-ellipsis">${ticket.created_at ? new Date(ticket.created_at).toLocaleDateString('id-ID') : 'N/A'}</td>
                        <td class="text-ellipsis">${completedDate}</td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
        })
        .catch(error => {
            console.error('Error loading ticket list:', error.response?.data || error.message);
        });
}

/* Helper Function to Get Status Badge */
function getStatusBadge(status) {
    const statusStr = String(status || '').toLowerCase();
    switch (statusStr) {
        case 'completed':
        case '2':
            return '<span class="badge bg-success">Selesai</span>';
        case 'pending':
        case '0':
            return '<span class="badge bg-warning">Pending</span>';
        case 'assigned':
        case '1':
            return '<span class="badge bg-info">Ditugaskan</span>';
        default:
            return '<span class="badge bg-warning">Pending</span>';
    }
}