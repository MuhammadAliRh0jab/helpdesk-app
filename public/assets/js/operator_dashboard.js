document.addEventListener('DOMContentLoaded', function () {
    // Configure Axios with CSRF token and credentials for Sanctum
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    axios.defaults.withCredentials = true;

    // Current operator unit_id
    const currentOperatorUnitId = window.currentOperatorUnitId || null;
    let selectedUnitId = currentOperatorUnitId;

    // Initial data from backend (fallback if undefined)
    const initialTicketStats = window.initialTicketStats || { completed: 0, pending: 0, assigned: 0 };

    // Function to update Statistics Cards
    function updateStatisticsCards(stats) {
        const total = (stats.completed || 0) + (stats.pending || 0) + (stats.assigned || 0);
        const totalTicketsElement = document.getElementById('total-tickets');
        const pendingTicketsElement = document.getElementById('pending-tickets');
        const assignedTicketsElement = document.getElementById('assigned-tickets');
        const completedTicketsElement = document.getElementById('completed-tickets');

        if (totalTicketsElement) totalTicketsElement.textContent = total;
        if (pendingTicketsElement) pendingTicketsElement.textContent = stats.pending || 0;
        if (assignedTicketsElement) assignedTicketsElement.textContent = stats.assigned || 0;
        if (completedTicketsElement) completedTicketsElement.textContent = stats.completed || 0;

        // Update percentage displays
        const percentageCompleted = total > 0 ? ((stats.completed / total) * 100).toFixed(1) : 0;
        const percentagePending = total > 0 ? ((stats.pending / total) * 100).toFixed(1) : 0;
        const percentageAssigned = total > 0 ? ((stats.assigned / total) * 100).toFixed(1) : 0;

        const pendingPercentElement = document.getElementById('pending-percent');
        const assignedPercentElement = document.getElementById('assigned-percent');
        const completedPercentElement = document.getElementById('completed-percent');

        if (pendingPercentElement) pendingPercentElement.textContent = `${percentagePending}%`;
        if (assignedPercentElement) assignedPercentElement.textContent = `${percentageAssigned}%`;
        if (completedPercentElement) completedPercentElement.textContent = `${percentageCompleted}%`;
    }

    // Initial update of Statistics Cards with initialTicketStats
    updateStatisticsCards(initialTicketStats);

    // Setup initial charts, map, and data
    setupTicketDistributionChart(initialTicketStats);
    setupCharts();
    setupServiceDistributionChart();
    loadRecentTickets();
    loadUnitData();
    setupPerServiceChart();

    // Function to set up all charts except Ticket Distribution
    function setupCharts() {
        setupTicketPerformanceChart();
        setupTicketCategoryChart();
        setupResolutionTimeChart();
    }

    // 1. Ticket Distribution Chart (Static, not updated by time range)
    function setupTicketDistributionChart(stats) {
        const ctx = document.getElementById('ticketDistributionChart')?.getContext('2d');
        if (!ctx) {
            console.error('Canvas element for ticketDistributionChart not found.');
            return;
        }

        axios.get('/api/ticket-stats', { params: { unit_id: selectedUnitId || null } })
            .then(response => {
                const { completed = 0, pending = 0, assigned = 0 } = response.data || {};
                const total = completed + pending + assigned;

                // Update Statistics Cards with fetched data
                updateStatisticsCards({ completed, pending, assigned });

                // Create percentages for chart tooltip only
                const getPercent = (val) => total > 0 ? ((val / total) * 100).toFixed(1) : '0.0';

                if (window.ticketDistChart) window.ticketDistChart.destroy();

                window.ticketDistChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Selesai', 'Pending', 'Ditugaskan'],
                        datasets: [{
                            label: 'Jumlah Tiket',
                            data: [completed, pending, assigned],
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
                                    label: function (context) {
                                        let label = context.label || '';
                                        let value = context.raw || 0;
                                        let percent = getPercent(value);
                                        return `${label}: ${value} (${percent}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading ticket stats:', error.response?.data || error.message);
                // Fallback to zeros if API call fails
                const completed = 0;
                const pending = 0;
                const assigned = 0;
                updateStatisticsCards({ completed, pending, assigned });

                if (window.ticketDistChart) window.ticketDistChart.destroy();

                window.ticketDistChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Selesai', 'Pending', 'Ditugaskan'],
                        datasets: [{
                            label: 'Jumlah Tiket',
                            data: [completed, pending, assigned],
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
                                    label: function (context) {
                                        let label = context.label || '';
                                        let value = context.raw || 0;
                                        let percent = '0.0';
                                        return `${label}: ${value} (${percent}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            });
    }

    // 2. Ticket Performance Chart
    function setupTicketPerformanceChart() {
        setupTicketPerformanceChartWithCustomRange(null, null);
    }

    function setupTicketPerformanceChartWithCustomRange(startDate, endDate) {
        const ctx = document.getElementById('ticketPerformanceChart')?.getContext('2d');
        if (!ctx) {
            console.error('Canvas element for ticketPerformanceChart not found.');
            return;
        }

        const params = { unit_id: selectedUnitId };
        if (startDate && endDate) {
            params.custom_start = startDate;
            params.custom_end = endDate;
        } else {
            const timeRange = document.querySelector('.dropdown-item.active[data-time-range]')?.getAttribute('data-time-range') || 'week';
            params.time_range = timeRange;
        }
        console.log('API call parameters:', params);

        axios.get('/api/ticket-performance', { params })
            .then(response => {
                console.log('API response:', response.data);
                const { labels, created, completed, pending, assigned } = response.data || { labels: [], created: [], completed: [], pending: 0, assigned: 0 };

                if (!Array.isArray(labels) || !Array.isArray(created) || !Array.isArray(completed)) {
                    console.error('Invalid data format:', { labels, created, completed });
                    return;
                }

                if (labels.length !== created.length || labels.length !== completed.length) {
                    console.error('Data length mismatch:', { labelsLength: labels.length, createdLength: created.length, completedLength: completed.length });
                    return;
                }

                if (labels.length === 0) {
                    console.warn('No data available for the selected time range.');
                    ctx.canvas.style.display = 'none';
                    const placeholder = document.createElement('div');
                    placeholder.innerText = 'Tidak ada data untuk ditampilkan.';
                    placeholder.style.textAlign = 'center';
                    placeholder.style.padding = '20px';
                    ctx.canvas.parentNode.appendChild(placeholder);
                    return;
                } else {
                    ctx.canvas.style.display = 'block';
                    const existingPlaceholder = ctx.canvas.parentNode.querySelector('div');
                    if (existingPlaceholder) {
                        existingPlaceholder.remove();
                    }
                }

                if (window.performanceChart) {
                    window.performanceChart.destroy();
                }

                window.performanceChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Tiket Dibuat',
                            data: created,
                            backgroundColor: 'rgba(67, 97, 238, 0.1)',
                            borderColor: '#4361EE',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#4361EE'
                        }, {
                            label: 'Tiket Selesai',
                            data: completed,
                            backgroundColor: 'rgba(28, 177, 120, 0.1)',
                            borderColor: '#1CB178',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#1CB178'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
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
                                ticks: {
                                    autoSkip: true,
                                    maxTicksLimit: 10,
                                    maxRotation: 45,
                                    minRotation: 0
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading ticket performance:', error.response?.data || error.message);
                ctx.canvas.style.display = 'none';
                const placeholder = document.createElement('div');
                placeholder.innerText = 'Terjadi kesalahan saat memuat data.';
                placeholder.style.textAlign = 'center';
                placeholder.style.padding = '20px';
                ctx.canvas.parentNode.appendChild(placeholder);
            });
    }

    // 3. Ticket Category Chart
    function setupTicketCategoryChart() {
        const ctx = document.getElementById('ticketCategoryChart')?.getContext('2d');
        if (!ctx) {
            console.error('Canvas element for ticketCategoryChart not found.');
            return;
        }
        axios.get('/api/ticket-categories', { params: { unit_id: selectedUnitId } })
            .then(response => {
                const { labels, counts } = response.data;
                if (window.categoryChart) {
                    window.categoryChart.destroy();
                }
                window.categoryChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels || ['Operator', 'Staff', 'Pengguna'],
                        datasets: [{
                            data: counts || [0, 0, 0],
                            backgroundColor: ['#4361EE', '#F9C74F', '#1CB178'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'right' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.raw || 0;
                                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading ticket categories:', error.response?.data || error.message);
            });
    }

    // 4. Resolution Time Chart
    function setupResolutionTimeChart() {
        const ctx = document.getElementById('resolutionTimeChart')?.getContext('2d');
        if (!ctx) {
            console.error('Canvas element for resolutionTimeChart not found.');
            return;
        }

        axios.get('/api/resolution-times', { params: { unit_id: selectedUnitId } })
            .then(response => {
                const { services, avgResolutionDays } = response.data || { services: [], avgResolutionDays: [] };

                if (window.resolutionChart) {
                    window.resolutionChart.destroy();
                }

                window.resolutionChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: services,
                        datasets: [{
                            label: 'Rata-rata Waktu (Hari)',
                            data: avgResolutionDays,
                            backgroundColor: '#4361EE',
                            borderRadius: 5,
                            maxBarThickness: 30
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
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
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Hari' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading resolution times:', error.response?.data || error.message);
            });
    }

    // 5. Service Distribution Chart
    function setupServiceDistributionChart() {
        const ctx = document.getElementById('serviceDistributionChart')?.getContext('2d');
        if (!ctx) {
            console.error('Canvas element for serviceDistributionChart not found.');
            return;
        }
        axios.get('/api/service-distribution', { params: { unit_id: selectedUnitId } })
            .then(response => {
                const { labels, counts } = response.data || { labels: [], counts: [] };
                if (window.serviceDistChart) {
                    window.serviceDistChart.destroy();
                }
                window.serviceDistChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Tiket',
                            data: counts,
                            backgroundColor: ['#4361EE', '#F9C74F', '#1CB178', '#FF6384', '#36A2EB'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.raw || 0;
                                        return `${label}: ${value}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { grid: { display: false }, ticks: { maxRotation: 0, minRotation: 0 } },
                            x: { beginAtZero: true, title: { display: true, text: 'Jumlah Tiket' } }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading service distribution:', error.response?.data || error.message);
            });
    }

    // 6. Per-Service Ticket Chart (Slidable)
    let currentServiceIndex = 0;
    let servicesData = [];

    function setupPerServiceChart() {
        const ctx = document.getElementById('perServiceChart')?.getContext('2d');
        if (!ctx) {
            console.error('Canvas element for perServiceChart not found.');
            return;
        }
        const prevButton = document.getElementById('prev-service');
        const nextButton = document.getElementById('next-service');
        const serviceNameElement = document.getElementById('current-service-name');

        function updateChart() {
            if (!servicesData.length) {
                if (serviceNameElement) serviceNameElement.textContent = 'Tidak ada layanan';
                return;
            }

            const service = servicesData[currentServiceIndex];
            if (serviceNameElement) serviceNameElement.textContent = service.name || 'Layanan Tidak Diketahui';
            const stats = service.stats || { completed: 0, pending: 0, assigned: 0 };

            if (window.perServiceChart && typeof window.perServiceChart.destroy === 'function') {
                window.perServiceChart.destroy();
            }
            window.perServiceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Selesai', 'Pending', 'Ditugaskan'],
                    datasets: [{
                        label: 'Jumlah Tiket',
                        data: [stats.completed || 0, stats.pending || 0, stats.assigned || 0],
                        backgroundColor: ['#1CB178', '#F9C74F', '#4361EE'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Jumlah Tiket' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            if (prevButton) prevButton.disabled = currentServiceIndex === 0;
            if (nextButton) nextButton.disabled = currentServiceIndex === servicesData.length - 1;
        }

        axios.get('/api/service-stats', { params: { unit_id: selectedUnitId } })
            .then(response => {
                servicesData = response.data || [];
                currentServiceIndex = 0;
                updateChart();
            })
            .catch(error => {
                console.error('Error loading service stats:', error.response?.data || error.message);
                if (serviceNameElement) serviceNameElement.textContent = 'Gagal memuat layanan';
            });

        if (prevButton) {
            prevButton.addEventListener('click', () => {
                if (currentServiceIndex > 0) {
                    currentServiceIndex--;
                    updateChart();
                }
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', () => {
                if (currentServiceIndex < servicesData.length - 1) {
                    currentServiceIndex++;
                    updateChart();
                }
            });
        }
    }

    // 7. Load Recent Tickets
    function loadRecentTickets() {
        const tableBody = document.getElementById('recent-tickets-table');
        if (!tableBody) {
            console.error('Table element for recent-tickets-table not found.');
            return;
        }
        axios.get('/api/recent-tickets', { params: { unit_id: selectedUnitId } })
            .then(response => {
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
                            <td class="text-ellipsis">${ticket.created_at ? new Date(ticket.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : 'N/A'}</td>
                            <td class="text-ellipsis">${ticket.unit_name || 'N/A'}</td>
                            <td class="text-center">${statusBadge}</td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
            })
            .catch(error => {
                console.error('Error loading recent tickets:', error.response?.data || error.message);
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center">Gagal memuat tiket.</td></tr>';
            });
    }

    // Helper function to get status badge
    function getStatusBadge(status) {
        switch ((status || '').toLowerCase()) {
            case 'completed': return '<span class="badge bg-success">Selesai</span>';
            case 'pending': return '<span class="badge bg-warning">Pending</span>';
            case 'assigned': return '<span class="badge bg-info">Ditugaskan</span>';
            default: return '<span class="badge bg-secondary">Unknown</span>';
        }
    }

    // 8. Load Unit Data for Filter Dropdown
    function loadUnitData() {
        const dropdownMenu = document.querySelector('.dropdown-menu[aria-labelledby="unit-filter"]');
        if (!dropdownMenu) {
            console.error('Dropdown menu for unit-filter not found.');
            return;
        }
        axios.get('/api/units')
            .then(response => {
                const units = response.data || [];
                dropdownMenu.querySelectorAll('.dropdown-item:not([data-unit-id="all"])').forEach(item => item.remove());
                units.forEach(unit => {
                    const isActive = unit.id == currentOperatorUnitId ? 'active' : '';
                    const unitItem = `
                        <a class="dropdown-item ${isActive}" href="javascript:void(0)" data-unit-id="${unit.id}">
                            ${unit.name || 'Unit Tidak Diketahui'}
                        </a>
                    `;
                    dropdownMenu.insertAdjacentHTML('beforeend', unitItem);
                });
                dropdownMenu.querySelectorAll('.dropdown-item').forEach(item => {
                    item.addEventListener('click', function() {
                        selectedUnitId = this.getAttribute('data-unit-id') === 'all' ? null : this.getAttribute('data-unit-id');
                        dropdownMenu.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active'));
                        this.classList.add('active');
                        const selectedUnitNameElement = document.getElementById('selected-unit-name');
                        if (selectedUnitNameElement) {
                            selectedUnitNameElement.textContent = selectedUnitId ? this.textContent : 'Semua Unit';
                        }

                        // Update Ticket Distribution Chart and Statistics Cards
                        axios.get('/api/ticket-stats', { params: { unit_id: selectedUnitId } })
                            .then(response => {
                                const ticketStats = response.data || { completed: 0, pending: 0, assigned: 0 };
                                setupTicketDistributionChart(ticketStats);
                            })
                            .catch(error => {
                                console.error('Error loading ticket stats for unit:', error.response?.data || error.message);
                                setupTicketDistributionChart({ completed: 0, pending: 0, assigned: 0 });
                            });

                        refreshDashboard();
                    });
                });
            })
            .catch(error => {
                console.error('Error loading units:', error.response?.data || error.message);
            });
    }

    // 9. Refresh Dashboard Data (Excluding Ticket Distribution and Statistics Cards)
    function refreshDashboard() {
        setupCharts();
        setupServiceDistributionChart();
        loadRecentTickets();
        setupPerServiceChart();
    }

    function refreshDashboardWithCustomRange(startDate, endDate) {
        setupCharts();
        setupServiceDistributionChart();
        loadRecentTickets();
        setupPerServiceChart();
        setupTicketPerformanceChartWithCustomRange(startDate, endDate);
    }

    // Event listeners for time range selection
    document.querySelectorAll('.dropdown-item[data-time-range]')?.forEach(item => {
        console.log('Dropdown item found:', item.getAttribute('data-time-range'));
        item.addEventListener('click', function() {
            console.log('Dropdown item clicked:', this.getAttribute('data-time-range'));
            document.querySelectorAll('.dropdown-item[data-time-range]').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            if (window.customDatePicker) {
                window.customDatePicker.clear();
            }
            refreshDashboard();
        });
    });

    // Initialize Flatpickr for custom range
    const customDatePickerElement = document.getElementById('custom-date-picker');
    if (!customDatePickerElement) {
        console.error('Element #custom-date-picker not found in DOM.');
    } else {
        console.log('Initializing Flatpickr on:', customDatePickerElement);
        const customDatePicker = flatpickr('#custom-date-picker', {
            mode: 'range',
            dateFormat: 'Y-m-d',
            defaultDate: [new Date(), new Date()],
            maxDate: new Date(),
            onClose: function(selectedDates, dateStr, instance) {
                console.log('Flatpickr onClose triggered:', selectedDates);
                if (selectedDates.length === 2) {
                    const startDate = selectedDates[0].toISOString().split('T')[0];
                    const endDate = selectedDates[1].toISOString().split('T')[0];
                    console.log('Custom range selected:', startDate, endDate);
                    document.querySelectorAll('.dropdown-item[data-time-range]').forEach(i => i.classList.remove('active'));
                    document.getElementById('custom-range')?.classList.add('414active');
                    refreshDashboardWithCustomRange(startDate, endDate);
                } else {
                    console.warn('Please select a valid date range.');
                }
            }
        });
        window.customDatePicker = customDatePicker;
    }

    document.getElementById('custom-range')?.addEventListener('click', function() {
        console.log('Custom range clicked');
        if (window.customDatePicker) {
            window.customDatePicker.open();
        } else {
            console.error('customDatePicker is not initialized.');
        }
    });

    // Event listener for refresh buttons
    document.querySelectorAll('.btn-block-option[data-action="state_toggle"]')?.forEach(button => {
        button.addEventListener('click', () => refreshDashboard());
    });
});