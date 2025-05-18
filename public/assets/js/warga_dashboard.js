document.addEventListener("DOMContentLoaded", () => {
    initializeWargaDashboard();
});

let ticketStatsChart = null;
let ticketDistributionChart = null;
let ticketStats = { pending: 0, assigned: 0, completed: 0 };

function initializeWargaDashboard() {
    setupTimeRangeListeners();
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
            timeRangeItems.forEach((i) => i.classList.remove("active"));
            this.classList.add("active");
            if (window.ticketStatsDatePicker) {
                window.ticketStatsDatePicker.clear();
            }
            fetchTicketStats(timeRange);
        });
    });

    const ticketStatsDatePickerElement = document.getElementById(
        "ticket-stats-date-picker"
    );
    if (ticketStatsDatePickerElement) {
        const ticketStatsDatePicker = flatpickr("#ticket-stats-date-picker", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: [new Date(), new Date()],
            maxDate: new Date(),
            onClose: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const startDate = selectedDates[0]
                        .toISOString()
                        .split("T")[0];
                    const endDate = selectedDates[1]
                        .toISOString()
                        .split("T")[0];
                    timeRangeItems.forEach((i) => i.classList.remove("active"));
                    document
                        .getElementById("ticket-stats-custom-range")
                        ?.classList.add("active");
                    fetchTicketStats(null, startDate, endDate);
                } else {
                    console.warn(
                        "Please select a valid date range for Ticket Stats."
                    );
                }
            },
        });
        window.ticketStatsDatePicker = ticketStatsDatePicker;

        document
            .getElementById("ticket-stats-custom-range")
            ?.addEventListener("click", function () {
                if (window.ticketStatsDatePicker) {
                    window.ticketStatsDatePicker.open();
                }
            });
    }

    setupFilterListeners();
}

function fetchTicketStats(
    timeRange = "week",
    startDate = null,
    endDate = null
) {
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
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            const totalPending = data.created.pending.data.reduce((a, b) => a + b, 0);
            const totalAssigned = data.created.assigned.data.reduce((a, b) => a + b, 0);
            const totalCompleted = data.created.completed.data.reduce((a, b) => a + b, 0);
            ticketStats = {
                pending: totalPending,
                assigned: totalAssigned,
                completed: totalCompleted
            };
            updateStatisticCards();
            setupTicketStatsChart(data.created);
            setupTicketDistributionChart();
        })
        .catch((error) => {
            console.error("Error fetching ticket stats:", error);
            ticketStats = { pending: 0, assigned: 0, completed: 0 };
            updateStatisticCards();
            setupTicketStatsChart();
            setupTicketDistributionChart();
        });
}

function fetchTickets() {
    fetch("/api/warga/tickets", {
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
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
    const total =
        (ticketStats.pending || 0) +
        (ticketStats.assigned || 0) +
        (ticketStats.completed || 0);
    document.getElementById("total-tickets").textContent = total;
    document.getElementById("pending-tickets").textContent =
        ticketStats.pending || 0;
    document.getElementById("assigned-tickets").textContent =
        ticketStats.assigned || 0;
    document.getElementById("completed-tickets").textContent =
        ticketStats.completed || 0;
}

function setupTicketStatsChart(data) {
    const chartContainer = document.getElementById("ticketStatsChart");
    if (!chartContainer) return;

    chartContainer.style.height = "300px";
    const ctx = chartContainer.getContext("2d");

    if (ticketStatsChart instanceof Chart) {
        ticketStatsChart.destroy();
    }

    ticketStatsChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: data.pending.labels,
            datasets: [
                {
                    label: "Pending",
                    data: data.pending.data,
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
                    data: data.assigned.data,
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
                    data: data.completed.data,
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
                    grid: {
                        display: true,
                        drawBorder: false,
                        color: "rgba(200, 200, 200, 0.15)",
                    },
                    ticks: { stepSize: 1 },
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 10,
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

    const hasData = total > 0;
    const completedPercentElement =
        document.getElementById("completed-percent");
    const pendingPercentElement = document.getElementById("pending-percent");
    const assignedPercentElement = document.getElementById("assigned-percent");

    if (!hasData) {
        chartContainer.parentElement.innerHTML =
            '<p class="text-center text-muted">Tidak ada data distribusi aduan.</p>';
        if (completedPercentElement) completedPercentElement.textContent = "0%";
        if (pendingPercentElement) pendingPercentElement.textContent = "0%";
        if (assignedPercentElement) assignedPercentElement.textContent = "0%";
        return;
    }

    if (ticketDistributionChart instanceof Chart) {
        ticketDistributionChart.destroy();
    }

    ticketDistributionChart = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["Pending", "Ditugaskan", "Selesai"],
            datasets: [
                {
                    data: data,
                    backgroundColor: ["#F9C74F", "#4361EE", "#1CB178"],
                    borderWidth: 0,
                    cutout: "60%",
                },
            ],
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
                            let total = context.dataset.data.reduce(
                                (a, b) => a + b,
                                0
                            );
                            let percent =
                                total > 0
                                    ? ((value / total) * 100).toFixed(1)
                                    : "0.0";
                            return `${label}: ${value} (${percent}%)`;
                        },
                    },
                },
            },
        },
    });

    if (completedPercentElement)
        completedPercentElement.textContent = `${percentages.completed}%`;
    if (pendingPercentElement)
        pendingPercentElement.textContent = `${percentages.pending}%`;
    if (assignedPercentElement)
        assignedPercentElement.textContent = `${percentages.assigned}%`;
}

function setupFilterListeners() {
    document.querySelectorAll(".dropdown-item[data-status]").forEach((item) => {
        item.addEventListener("click", function () {
            const filter = this.getAttribute("data-status");
            const rows = document.querySelectorAll("tr[data-status-row]");

            rows.forEach((row) => {
                if (
                    filter === "semua" ||
                    row.getAttribute("data-status-row") === filter
                ) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    });
}

function renderTickets(tickets) {
    const tableBody = document.getElementById("ticket-list-table");
    if (!tableBody) return;

    if (!Array.isArray(tickets) || tickets.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <div class="alert alert-info m-0">
                        Anda belum memiliki tiket. Silakan klik "Buat Aduan" untuk membuat tiket baru.
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    const modalTemplate = document.getElementById(
        "ticketDetailModalTemplate"
    ).innerHTML;
    const chatModalTemplate =
        document.getElementById("chatModalTemplate").innerHTML;

    tableBody.innerHTML = tickets
        .map((ticket, index) => {
            const status =
                ticket.status === 0
                    ? "belum"
                    : ticket.status === 1
                    ? "direspon"
                    : "selesai";
            const statusBadge =
                ticket.status === 0
                    ? '<span class="badge bg-warning">Pending</span>'
                    : ticket.status === 1
                    ? '<span class="badge bg-info">Direspon</span>'
                    : '<span class="badge bg-success">Selesai</span>';

            const modalId = `detailModal-${ticket.id}`;
            const modalHtml = modalTemplate.replace(
                /ID_PLACEHOLDER/g,
                ticket.id
            );
            const chatModalId = `chatModal-${ticket.id}`;
            const chatModalHtml = chatModalTemplate.replace(
                /ID_PLACEHOLDER/g,
                ticket.id
            );
            document.body.insertAdjacentHTML("beforeend", modalHtml);
            document.body.insertAdjacentHTML("beforeend", chatModalHtml);

            return `
            <tr class="text-nowrap" data-status-row="${status}">
                <td>${index + 1}</td>
                <td>${ticket.ticket_code || "N/A"}</td>
                <td>${ticket.svc_name || "N/A"}</td>
                <td>${ticket.title || "No Title"}</td>
                <td>${ticket.description || "No Description"}</td>
                <td class="text-center">${statusBadge}</td>
                <td class="text-center">
                    <button type="button"
                        class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#detailModal-${ticket.id}"
                        title="Detail"
                        onclick="showTicketDetail(${ticket.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#${chatModalId}" title="Pesan">
                        <i class="fas fa-comments"></i>
                    </button>
                </td>
            </tr>
        `;
        })
        .join("");
}

function showTicketDetail(ticketId) {
    fetch(`/api/warga/tickets/${ticketId}`, {
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            const modalId = `detailModal-${ticketId}`;
            const modal = document.getElementById(modalId);
            if (!modal) {
                console.error(`Modal ${modalId} not found`);
                return;
            }

            document.getElementById(
                `modal-ticket-code-${ticketId}`
            ).textContent = data.ticket_code || "N/A";
            document.getElementById(`modal-title-${ticketId}`).textContent =
                data.title || "No Title";
            document.getElementById(
                `modal-description-${ticketId}`
            ).textContent = data.description || "No Description";
            document.getElementById(`modal-svc-name-${ticketId}`).textContent =
                data.svc_name || "N/A";
            document.getElementById(`modal-unit-name-${ticketId}`).textContent =
                data.unit_name || "N/A";
            document.getElementById(
                `modal-created-at-${ticketId}`
            ).textContent = data.created_at
                ? new Date(data.created_at).toLocaleString("id-ID", {
                      timeZone: "Asia/Jakarta",
                  })
                : "N/A";

            const statusElement = document.getElementById(
                `modal-status-${ticketId}`
            );
            if (data.status === 0) {
                statusElement.textContent = "Pending";
                statusElement.className =
                    "fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-warning-light text-warning";
            } else if (data.status === 1) {
                statusElement.textContent = "Ditugaskan";
                statusElement.className =
                    "fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-info-light text-info";
            } else {
                statusElement.textContent = "Selesai";
                statusElement.className =
                    "fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success";
            }

            const locationElement = document.getElementById(
                `modal-location-${ticketId}`
            );
            if (data.latitude && data.longitude) {
                locationElement.innerHTML = `
                <div class="d-flex gap-3">
                    <p class="mb-0"><strong>Latitude:</strong> ${data.latitude}</p>
                    <p class="mb-0"><strong>Longitude:</strong> ${data.longitude}</p>
                </div>
                <div id="map-${ticketId}" style="height: 200px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
            `;
                initializeMap(ticketId, data.latitude, data.longitude);
            } else {
                locationElement.innerHTML =
                    '<p class="text-muted">Lokasi tidak tersedia.</p>';
            }

            document.getElementById(
                `modal-original-unit-${ticketId}`
            ).textContent = "Tidak ditentukan";

            new bootstrap.Modal(modal).show();
        })
        .catch((error) => {
            console.error("Error fetching ticket detail:", error);
            alert("Gagal memuat detail tiket. Pastikan tiket valid.");
        });
}

function initializeMap(ticketId, latitude, longitude) {
    const mapElement = document.getElementById(`map-${ticketId}`);
    if (mapElement && !mapElement._map) {
        const map = L.map(mapElement).setView([latitude, longitude], 13);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 19,
            attribution:
                '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);
        L.marker([latitude, longitude])
            .addTo(map)
            .bindPopup("Lokasi Aduan")
            .openPopup();
    }
}

function generateLabels(timeRange, startDate, endDate) {
    const labels = [];
    const now = new Date();
    let start;

    if (startDate && endDate) {
        start = new Date(startDate);
        const end = new Date(endDate);
        while (start <= end) {
            labels.push(
                start.toLocaleDateString("id-ID", {
                    day: "numeric",
                    month: "short",
                })
            );
            start.setDate(start.getDate() + 1);
        }
        return labels;
    }

    switch (timeRange) {
        case "day":
            for (let i = 23; i >= 0; i--) {
                const date = new Date(now);
                date.setHours(now.getHours() - i);
                labels.push(
                    date.toLocaleTimeString("id-ID", {
                        hour: "2-digit",
                        minute: "2-digit",
                    })
                );
            }
            break;
        case "week":
            for (let i = 6; i >= 0; i--) {
                const date = new Date(now);
                date.setDate(now.getDate() - i);
                labels.push(
                    date.toLocaleDateString("id-ID", {
                        day: "numeric",
                        month: "short",
                    })
                );
            }
            break;
        case "month":
            for (let i = 29; i >= 0; i--) {
                const date = new Date(now);
                date.setDate(now.getDate() - i);
                labels.push(
                    date.toLocaleDateString("id-ID", {
                        day: "numeric",
                        month: "short",
                    })
                );
            }
            break;
        case "year":
            for (let i = 11; i >= 0; i--) {
                const date = new Date(now);
                date.setMonth(now.getMonth() - i);
                labels.push(
                    date.toLocaleDateString("id-ID", {
                        month: "short",
                        year: "numeric",
                    })
                );
            }
            break;
        case "10year":
            for (let i = 9; i >= 0; i--) {
                const date = new Date(now);
                date.setFullYear(now.getFullYear() - i);
                labels.push(
                    date.toLocaleDateString("id-ID", { year: "numeric" })
                );
            }
            break;
        default:
            for (let i = 6; i >= 0; i--) {
                const date = new Date(now);
                date.setDate(now.getDate() - i);
                labels.push(
                    date.toLocaleDateString("id-ID", {
                        day: "numeric",
                        month: "short",
                    })
                );
            }
    }
    return labels;
}