document.addEventListener("DOMContentLoaded", () => {
    initializeWargaDashboard();
});

let ticketStatsChart = null;
let ticketDistributionChart = null;
let ticketStats = { pending: 0, assigned: 0, completed: 0 };

function initializeWargaDashboard() {
    setupTimeRangeListeners();
    fetchStaticStats();
    fetchTicketStats();
    fetchTickets();
}

function setupTimeRangeListeners() {
    const timeRangeItems = document.querySelectorAll(
        "#ticket-stats-time-range .dropdown-item[data-time-range]"
    );
    timeRangeItems.forEach((item) => {
        item.addEventListener("click", function () {
            const timeRange = this.getAttribute("data-time-range");
            if (window.ticketStatsDatePicker) {
                window.ticketStatsDatePicker.clear();
            }
            fetchTicketStats(timeRange);
        });
    });

    const ticketStatsDatePickerElement = document.getElementById("ticket-stats-date-picker");
    if (ticketStatsDatePickerElement) {
        const ticketStatsDatePicker = flatpickr(ticketStatsDatePickerElement, {
            mode: "range",
            dateFormat: "Y-m-d",
            maxDate: new Date(),
            onClose: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const startDate = selectedDates[0].toISOString().split("T")[0];
                    const endDate = selectedDates[1].toISOString().split("T")[0];
                    timeRangeItems.forEach((i) => i.classList.remove("active"));
                    document.getElementById("ticket-stats-custom-range")?.classList.add("active");
                    fetchTicketStats(null, startDate, endDate);
                } else {
                    console.warn("Please select a valid date range for Ticket Stats.");
                }
            },
        });
        window.ticketStatsDatePicker = ticketStatsDatePicker;

        document.getElementById("ticket-stats-custom-range")?.addEventListener("click", function () {
            window.ticketStatsDatePicker.open();
        });
    }

    setupFilterListeners();
}

function fetchStaticStats() {
    fetch("/api/warga/static-stats", {
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        },
    })
        .then((response) => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then((data) => {
            ticketStats = {
                pending: data.pending || 0,
                assigned: data.assigned || 0,
                completed: data.completed || 0,
            };
            updateStatisticCards();
            setupTicketDistributionChart();
        })
        .catch((error) => {
            console.error("Error fetching static stats:", error);
            ticketStats = { pending: 0, assigned: 0, completed: 0 };
            updateStatisticCards();
            setupTicketDistributionChart();
        });
}

function fetchTicketStats(timeRange = "month", startDate = null, endDate = null) {
    let url = "/api/warga/ticket-stats";
    const params = new URLSearchParams();
    if (timeRange) params.append("time_range", timeRange);
    if (startDate && endDate) {
        params.append("start_date", startDate);
        params.append("end_date", endDate);
    }
    if (params.toString()) url += `?${params.toString()}`;

    fetch(url, {
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        },
    })
        .then((response) => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then((data) => {
            setupTicketStatsChart(data.created || { pending: { labels: [], data: [] }, assigned: { labels: [], data: [] }, completed: { labels: [], data: [] } });
        })
        .catch((error) => {
            console.error("Error fetching ticket stats:", error);
            setupTicketStatsChart({ pending: { labels: [], data: [] }, assigned: { labels: [], data: [] }, completed: { labels: [], data: [] } });
        });
}

function fetchTickets() {
    fetch("/api/warga/tickets", {
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        },
    })
        .then((response) => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then((data) => {
            const tickets = data.data || [];
            renderTickets(tickets);
        })
        .catch((error) => {
            console.error("Error fetching tickets:", error);
            renderTickets([]);
        });
}

function updateStatisticCards() {
    const total = ticketStats.pending + ticketStats.assigned + ticketStats.completed;
    document.getElementById("total-tickets").textContent = total || 0;
    document.getElementById("pending-tickets").textContent = ticketStats.pending || 0;
    document.getElementById("assigned-tickets").textContent = ticketStats.assigned || 0;
    document.getElementById("completed-tickets").textContent = ticketStats.completed || 0;
}

function setupTicketStatsChart(data) {
    const chartContainer = document.getElementById("ticketStatsChart");
    if (!chartContainer) return;

    chartContainer.style.height = "200px";
    const ctx = chartContainer.getContext("2d");

    if (ticketStatsChart) ticketStatsChart.destroy();

    ticketStatsChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: data.pending.labels || [],
            datasets: [
                {
                    label: "Pending",
                    data: data.pending.data || [],
                    backgroundColor: "rgba(249, 199, 79, 0.1)",
                    borderColor: "#F9C74F",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: "#F9C74F",
                },
                {
                    label: "Ditugaskan",
                    data: data.assigned.data || [],
                    backgroundColor: "rgba(67, 97, 238, 0.1)",
                    borderColor: "#4361EE",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: "#4361EE",
                },
                {
                    label: "Selesai",
                    data: data.completed.data || [],
                    backgroundColor: "rgba(28, 177, 120, 0.1)",
                    borderColor: "#1CB178",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: "#1CB178",
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: "top", align: "end" },
                tooltip: {
                    mode: "index",
                    intersect: false,
                    callbacks: {
                        label: function (context) {
                            const label = context.dataset.label || "";
                            const value = context.parsed.y;
                            return `${label}: ${value}`;
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: "rgba(200, 200, 200, 0.15)" },
                    ticks: { stepSize: 1 },
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 7,
                        maxRotation: 45,
                        minRotation: 0,
                    },
                },
            },
        },
    });
}

function setupTicketDistributionChart() {
    const chartContainer = document.getElementById("ticketDistributionChart");
    if (!chartContainer) return;

    const ctx = chartContainer.getContext("2d");
    const data = [
        ticketStats.pending || 0,
        ticketStats.assigned || 0,
        ticketStats.completed || 0,
    ];
    const total = data.reduce((a, b) => a + b, 0);
    const percentages = {
        pending: total > 0 ? ((data[0] / total) * 100).toFixed(1) : 0,
        assigned: total > 0 ? ((data[1] / total) * 100).toFixed(1) : 0,
        completed: total > 0 ? ((data[2] / total) * 100).toFixed(1) : 0,
    };

    if (!total) {
        chartContainer.parentElement.innerHTML = '<p class="text-center text-muted">Tidak ada data distribusi aduan.</p>';
        document.getElementById("completed-percent").textContent = "0%";
        document.getElementById("pending-percent").textContent = "0%";
        document.getElementById("assigned-percent").textContent = "0%";
        return;
    }

    if (ticketDistributionChart) ticketDistributionChart.destroy();

    ticketDistributionChart = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["Pending", "Ditugaskan", "Selesai"],
            datasets: [{
                data: data,
                backgroundColor: ["#F9C74F", "#4361EE", "#1CB178"],
                borderWidth: 0,
                cutout: "60%",
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: "bottom",
                    labels: {
                        boxWidth: 14,
                        boxHeight: 14,
                        padding: 16,
                        usePointStyle: true,
                    },
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let label = context.label || "";
                            let value = context.raw || 0;
                            let percent = total > 0 ? ((value / total) * 100).toFixed(1) : "0.0";
                            return `${label}: ${value} (${percent}%)`;
                        },
                    },
                },
            },
        },
    });

    document.getElementById("completed-percent").textContent = `${percentages.completed}%`;
    document.getElementById("pending-percent").textContent = `${percentages.pending}%`;
    document.getElementById("assigned-percent").textContent = `${percentages.assigned}%`;
}

function setupFilterListeners() {
    document.querySelectorAll(".dropdown-item[data-status]").forEach((item) => {
        item.addEventListener("click", function () {
            const filter = this.getAttribute("data-status");
            const rows = document.querySelectorAll("tr[data-status-row]");
            rows.forEach((row) => {
                row.style.display = filter === "semua" || row.getAttribute("data-status-row") === filter ? "" : "none";
            });
            renumberVisibleRows();
        });
    });
}

function renumberVisibleRows() {
    const rows = document.querySelectorAll("tr[data-status-row]");
    const visibleRows = Array.from(rows).filter(row => row.style.display !== "none");
    visibleRows.forEach((row, index) => {
        row.querySelector("td:first-child").textContent = index + 1;
    });
}

function renderTickets(tickets) {
    const tableBody = document.getElementById("ticket-list-table").querySelector("tbody");
    if (!tableBody) return;

    if (!tickets.length) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center">
                    <div class="alert alert-info m-0">
                        Anda belum memiliki tiket. Silakan klik "Buat Aduan" untuk membuat tiket baru.
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    const modalTemplate = document.getElementById("ticketDetailModalTemplate").innerHTML;
    const chatModalTemplate = document.getElementById("chatModalTemplate").innerHTML;

    tableBody.innerHTML = tickets.map((ticket, index) => {
        const status = ticket.status === 0 ? "belum" : ticket.status === 1 ? "direspon" : "selesai";
        const statusBadge = ticket.status === 0
            ? '<span class="badge bg-warning">Pending</span>'
            : ticket.status === 1
            ? '<span class="badge bg-info">Direspon</span>'
            : '<span class="badge bg-success">Selesai</span>';

        const modalHtml = modalTemplate.replace(/ID_PLACEHOLDER/g, ticket.id);
        const chatModalHtml = chatModalTemplate.replace(/ID_PLACEHOLDER/g, ticket.id);
        document.body.insertAdjacentHTML("beforeend", modalHtml);
        document.body.insertAdjacentHTML("beforeend", chatModalHtml);

        return `
            <tr class="text-nowrap" data-status-row="${status}">
                <td>${index + 1}</td>
                <td>${ticket.ticket_code || "N/A"}</td>
                <td>${ticket.title || "No Title"}</td>
                <td>${ticket.svc_name || "N/A"}</td>
                <td>${statusBadge}</td>
            </tr>
        `;
    }).join("");

    renumberVisibleRows();
}

function showTicketDetail(ticketId) {
    fetch(`/api/warga/tickets/${ticketId}`, {
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        },
    })
        .then((response) => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then((data) => {
            const modal = document.getElementById(`detailModal-${ticketId}`);
            if (!modal) return;

            document.getElementById(`modal-ticket-code-${ticketId}`).textContent = data.ticket_code || "N/A";
            document.getElementById(`modal-title-${ticketId}`).textContent = data.title || "No Title";
            document.getElementById(`modal-description-${ticketId}`).textContent = data.description || "No Description";
            document.getElementById(`modal-svc-name-${ticketId}`).textContent = data.svc_name || "N/A";
            document.getElementById(`modal-unit-name-${ticketId}`).textContent = data.unit_name || "N/A";
            document.getElementById(`modal-created-at-${ticketId}`).textContent = data.created_at
                ? new Date(data.created_at).toLocaleString("id-ID", { timeZone: "Asia/Jakarta" })
                : "N/A";

            const statusElement = document.getElementById(`modal-status-${ticketId}`);
            statusElement.textContent = data.status === 0 ? "Pending" : data.status === 1 ? "Ditugaskan" : "Selesai";
            statusElement.className = `fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill ${
                data.status === 0 ? "bg-warning-light text-warning" : data.status === 1 ? "bg-info-light text-info" : "bg-success-light text-success"
            }`;

            const locationElement = document.getElementById(`modal-location-${ticketId}`);
            locationElement.innerHTML = data.latitude && data.longitude
                ? `
                    <div class="d-flex gap-3">
                        <p class="mb-0"><strong>Latitude:</strong> ${data.latitude}</p>
                        <p class="mb-0"><strong>Longitude:</strong> ${data.longitude}</p>
                    </div>
                    <div id="map-${ticketId}" style="height: 200px; border-radius: 8px;"></div>
                `
                : '<p class="text-muted">Lokasi tidak tersedia.</p>';

            document.getElementById(`modal-original-unit-${ticketId}`).textContent = "Tidak ditentukan";

            new bootstrap.Modal(modal).show();

            if (data.latitude && data.longitude) initializeMap(ticketId, data.latitude, data.longitude);
        })
        .catch((error) => {
            console.error("Error fetching ticket detail:", error);
            alert("Gagal memuat detail tiket.");
        });
}

function initializeMap(ticketId, latitude, longitude) {
    const mapElement = document.getElementById(`map-${ticketId}`);
    if (mapElement && !mapElement._map) {
        const map = L.map(mapElement).setView([latitude, longitude], 13);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 19,
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        }).addTo(map);
        L.marker([latitude, longitude]).addTo(map).bindPopup("Lokasi Aduan").openPopup();
        mapElement._map = map;
    }
}
