document.addEventListener('DOMContentLoaded', function () {
    // Configure Axios with CSRF token and credentials for Sanctum
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    axios.defaults.withCredentials = true;

    // Current operator unit_id
    const currentOperatorUnitId = window.currentOperatorUnitId || null;
    let selectedUnitId = currentOperatorUnitId;

    // Initial data from backend (fallback if undefined)
    const initialTicketStats = window.initialTicketStats || { completed: 0, pending: 0, assigned: 0 };

    // Calculate total and percentages with fallback
    const total = (initialTicketStats.completed || 0) + (initialTicketStats.pending || 0) + (initialTicketStats.assigned || 0);
    const percentageCompleted = total > 0 ? ((initialTicketStats.completed / total) * 100).toFixed(1) : 0;
    const percentagePending = total > 0 ? ((initialTicketStats.pending / total) * 100).toFixed(1) : 0;
    const percentageAssigned = total > 0 ? ((initialTicketStats.assigned / total) * 100).toFixed(1) : 0;

    // Update percentage displays with stricter error handling
    try {
        const pendingPercentElement = document.getElementById('pending-percent');
        const assignedPercentElement = document.getElementById('assigned-percent');
        const completedPercentElement = document.getElementById('completed-percent');

        if (pendingPercentElement) {
            pendingPercentElement.textContent = `${percentagePending}%`;
        } else {
            console.warn('Element with ID "pending-percent" not found.');
        }

        if (assignedPercentElement) {
            assignedPercentElement.textContent = `${percentageAssigned}%`;
        } else {
            console.warn('Element with ID "assigned-percent" not found.');
        }

        if (completedPercentElement) {
            completedPercentElement.textContent = `${percentageCompleted}%`;
        } else {
            console.warn('Element with ID "completed-percent" not found.');
        }
    } catch (err) {
        console.error('Error updating percentage displays:', err.message);
    }

    // Setup initial charts, map, and data
    setupCharts(initialTicketStats);
    setupTicketMap();
    loadRecentTickets();
    loadUnitData();
    setupPerServiceChart();

    // Function to set up all charts
    function setupCharts(ticketStats) {
        setupTicketDistributionChart(ticketStats);
        setupTicketPerformanceChart();
        setupTicketCategoryChart();
        setupResolutionTimeChart();
    }

    // 1. Ticket Distribution Chart
    function setupTicketDistributionChart(ticketStats) {
        const ctx = document.getElementById('ticketDistributionChart')?.getContext('2d');
        if (!ctx) {
            console.error('Canvas element for ticketDistributionChart not found.');
            return;
        }
        if (window.ticketDistChart) {
            window.ticketDistChart.destroy();
        }
        window.ticketDistChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Selesai', 'Pending', 'Ditugaskan'],
                datasets: [{
                    label: 'Jumlah Tiket',
                    data: [ticketStats.completed || 0, ticketStats.pending || 0, ticketStats.assigned || 0],
                    backgroundColor: ['#1CB178', '#F9C74F', '#4361EE'],
                    borderWidth: 0,
                    cutout: '75%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 20 } },
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
    }

    // 2. Ticket Performance Chart
    function setupTicketPerformanceChart() {
    const ctx = document.getElementById('ticketPerformanceChart')?.getContext('2d');
    if (!ctx) {
        console.error('Canvas element for ticketPerformanceChart not found.');
        return;
    }
    
    const timeRange = document.querySelector('.dropdown-item.active[data-time-range]')?.getAttribute('data-time-range') || 'week';
    
    axios.get('/api/ticket-performance', { params: { unit_id: selectedUnitId, time_range: timeRange } })
        .then(response => {
            const { labels, created, completed } = response.data || { labels: [], created: [], completed: [] };
            
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
                                    const value = context.raw;
                                    return `${label}: ${value} tiket`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { display: true, drawBorder: false, color: 'rgba(200, 200, 200, 0.15)' }, 
                            ticks: { 
                                stepSize: 1,
                                callback: function(value) {
                                    return Math.round(value);
                                }
                            } 
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading ticket performance:', error.response?.data || error.message);
        });
}

    // 3. Ticket Category Chart (Pemerintah vs Publik)
    function setupTicketCategoryChart() {
        const ctx = document.getElementById('ticketCategoryChart')?.getContext('2d');
        if (!ctx) {
            console.error('Canvas element for ticketCategoryChart not found.');
            return;
        }
        axios.get('/api/ticket-categories', { params: { unit_id: selectedUnitId } })
            .then(response => {
                const categoryData = response.data.reduce((acc, item) => {
                    acc[item.category_id === 1 ? 'pemerintah' : 'publik'] = (acc[item.category_id === 1 ? 'pemerintah' : 'publik'] || 0) + (item.count || 0);
                    return acc;
                }, { pemerintah: 0, publik: 0 });

                if (window.categoryChart) {
                    window.categoryChart.destroy();
                }
                window.categoryChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Pemerintah', 'Publik'],
                        datasets: [{
                            data: [categoryData.pemerintah, categoryData.publik],
                            backgroundColor: ['#4361EE', '#F9C74F'],
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
                                        return `${label}: ${percentage}%`;
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
            
            // Round the resolution days to whole numbers
            const roundedResolutionDays = avgResolutionDays.map(value => Math.round(value));

            if (window.resolutionChart) {
                window.resolutionChart.destroy();
            }

            window.resolutionChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: services,
                    datasets: [{
                        label: 'Rata-rata Waktu Penyelesaian (Hari)',
                        data: roundedResolutionDays,
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
                                    return `${Math.round(context.raw)} Hari`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Hari' },
                            ticks: {
                                // Format y-axis ticks as integers
                                callback: function(value) {
                                    return Math.round(value);
                                }
                            }
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


    // 5. Per-Service Ticket Chart (Slidable)
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

        // Check if perServiceChart is a valid Chart instance before destroying
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

    // 6. Geographic Map for Tickets
    function setupTicketMap() {
        if (typeof L === 'undefined') {
            console.error('Leaflet library is not loaded.');
            return;
        }
        const map = L.map('ticketMap').setView([-8.0951, 112.1607], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        axios.get('/api/ticket-locations', { params: { unit_id: selectedUnitId } })
            .then(response => {
                const ticketLocations = response.data || [];
                if (ticketLocations.length > 0) {
                    ticketLocations.forEach(location => {
                        L.marker([location.lat || -8.0951, location.lng || 112.1607])
                            .addTo(map)
                            .bindPopup(`<b>${location.title || 'No Title'}</b><br>${location.description || 'No Description'}`);
                    });
                    const bounds = L.latLngBounds(ticketLocations.map(loc => [loc.lat || -8.0951, loc.lng || 112.1607]));
                    map.fitBounds(bounds, { padding: [50, 50] });
                } else {
                    console.warn('No ticket locations available.');
                }
            })
            .catch(error => {
                console.error('Error loading ticket locations:', error.response?.data || error.message);
            });
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
                    tableBody.innerHTML = '<tr><td colspan="3" class="text-center">Tidak ada tiket terbaru.</td></tr>';
                    return;
                }
                tickets.forEach(ticket => {
                    const statusBadge = getStatusBadge(ticket.status || 'unknown');
                    const row = `
                        <tr>
                            <td>${ticket.code || 'N/A'}</td>
                            <td>${ticket.title || 'No Title'}</td>
                            <td class="text-center">${statusBadge}</td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
            })
            .catch(error => {
                console.error('Error loading recent tickets:', error.response?.data || error.message);
                tableBody.innerHTML = '<tr><td colspan="3" class="text-center">Gagal memuat tiket.</td></tr>';
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
                    const isActive = unit.id == currentOperatorUnitId ? 'active' : ''; // Use == for loose comparison
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
                        refreshDashboard();
                    });
                });
            })
            .catch(error => {
                console.error('Error loading units:', error.response?.data || error.message);
            });
    }

    // 9. Refresh Dashboard Data
    function refreshDashboard() {
        const timeRange = document.querySelector('.dropdown-item.active[data-time-range]')?.getAttribute('data-time-range') || 'week';
        axios.get('/api/ticket-stats', {
            params: { unit_id: selectedUnitId, time_range: timeRange }
        })
            .then(response => {
                const ticketStats = response.data || { completed: 0, pending: 0, assigned: 0 };
                const totalTicketsElement = document.getElementById('total-tickets');
                const pendingTicketsElement = document.getElementById('pending-tickets');
                const assignedTicketsElement = document.getElementById('assigned-tickets');
                const completedTicketsElement = document.getElementById('completed-tickets');
                const pendingPercentElement = document.getElementById('pending-percent');
                const assignedPercentElement = document.getElementById('assigned-percent');
                const completedPercentElement = document.getElementById('completed-percent');

                if (totalTicketsElement) totalTicketsElement.textContent = ticketStats.completed + ticketStats.pending + ticketStats.assigned;
                if (pendingTicketsElement) pendingTicketsElement.textContent = ticketStats.pending;
                if (assignedTicketsElement) assignedTicketsElement.textContent = ticketStats.assigned;
                if (completedTicketsElement) completedTicketsElement.textContent = ticketStats.completed;

                const total = ticketStats.completed + ticketStats.pending + ticketStats.assigned;
                if (pendingPercentElement) {
                    pendingPercentElement.textContent = total > 0 ? ((ticketStats.pending / total) * 100).toFixed(1) + '%' : '0%';
                }
                if (assignedPercentElement) {
                    assignedPercentElement.textContent = total > 0 ? ((ticketStats.assigned / total) * 100).toFixed(1) + '%' : '0%';
                }
                if (completedPercentElement) {
                    completedPercentElement.textContent = total > 0 ? ((ticketStats.completed / total) * 100).toFixed(1) + '%' : '0%';
                }

                setupCharts(ticketStats);
                loadRecentTickets();
                setupTicketMap();
                setupPerServiceChart();
            })
            .catch(error => {
                console.error('Error refreshing dashboard:', error.response?.data || error.message);
            });
    }

    // Event listeners for time range selection
    document.querySelectorAll('.dropdown-item[data-time-range]')?.forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.dropdown-item[data-time-range]').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            refreshDashboard();
        });
    });

    // Event listener for refresh buttons
    document.querySelectorAll('.btn-block-option[data-action="state_toggle"]')?.forEach(button => {
        button.addEventListener('click', () => refreshDashboard());
    });
});