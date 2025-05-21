document.addEventListener("DOMContentLoaded", () => {
    initializeWargaDashboard();
});

let ticketStatsChart = null;
let ticketDistributionChart = null;
let ticketStats = { pending: 0, assigned: 0, completed: 0 };

// Store modal instances to manage them properly
const modalInstances = new Map();

function initializeWargaDashboard() {
    setupTimeRangeListeners();
    fetchStaticStats();
    fetchTicketStats();
    fetchTickets();
    setupModalEventListeners();
}

function setupTimeRangeListeners() {
    const timeRangeItems = document.querySelectorAll("#ticket-stats-time-range .dropdown-item[data-time-range]");
    timeRangeItems.forEach((item) => {
        item.addEventListener("click", function () {
            const timeRange = this.getAttribute("data-time-range");
            timeRangeItems.forEach((i) => i.classList.remove("active"));
            this.classList.add("active");
            document.getElementById("ticket-stats-time-label").textContent = this.textContent;
            if (window.ticketStatsDatePicker) {
                window.ticketStatsDatePicker.clear();
            }
            fetchTicketStats(timeRange);
        });
    });

    const ticketStatsDatePickerElement = document.getElementById("ticket-stats-date-picker");
    if (ticketStatsDatePickerElement) {
        const ticketStatsDatePicker = flatpickr("#ticket-stats-date-picker", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: [new Date(), new Date()],
            maxDate: new Date(),
            onClose: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const startDate = selectedDates[0].toISOString().split("T")[0];
                    const endDate = selectedDates[1].toISOString().split("T")[0];
                    timeRangeItems.forEach((i) => i.classList.remove("active"));
                    document.getElementById("ticket-stats-custom-range")?.classList.add("active");
                    document.getElementById("ticket-stats-time-label").textContent = "Kustom";
                    fetchTicketStats(null, startDate, endDate);
                } else {
                    console.warn("Please select a valid date range for Ticket Stats.");
                }
            },
        });
        window.ticketStatsDatePicker = ticketStatsDatePicker;

        document.getElementById("ticket-stats-custom-range")?.addEventListener("click", function () {
            if (window.ticketStatsDatePicker) {
                window.ticketStatsDatePicker.open();
            }
        });
    }

    setupFilterListeners();
    setupFileInputListeners();
}

function setupFileInputListeners() {
    document.addEventListener("click", function (e) {
        if (e.target.closest("[id^='custom-button-']")) {
            const button = e.target.closest("[id^='custom-button-']");
            const ticketId = button.id.split("-")[2];
            const fileInput = document.getElementById(`images-${ticketId}`);
            fileInput.click();
        }
    });

    document.addEventListener("change", function (e) {
        if (e.target.matches("[id^='images-']")) {
            const ticketId = e.target.id.split("-")[1];
            const fileNameSpan = document.getElementById(`file-name-${ticketId}`);
            const files = e.target.files;
            if (files.length > 0) {
                fileNameSpan.textContent = files.length > 1 ? `${files.length} files selected` : files[0].name;
            } else {
                fileNameSpan.textContent = "Tidak ada file dipilih";
            }
        }
    });
}

function setupFilterListeners() {
    document.querySelectorAll(".dropdown-item[data-status]").forEach((item) => {
        item.addEventListener("click", function () {
            const filter = this.getAttribute("data-status");
            const rows = document.querySelectorAll("tr[data-status-row]");
            rows.forEach((row) => {
                if (filter === "semua" || row.getAttribute("data-status-row") === filter) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    });
}

function setupModalEventListeners() {
    // Event delegation for detail buttons
    document.addEventListener("click", function (e) {
        const detailButton = e.target.closest(".btn-primary[data-bs-toggle='modal'][title='Detail']");
        if (detailButton) {
            const ticketId = detailButton.getAttribute("data-ticket-id");
            showTicketDetail(ticketId);
        }
    });

    // Clean up backdrop and body class on modal close
    document.addEventListener("hidden.bs.modal", function (e) {
        const modalId = e.target.id;
        // Dispose of the modal instance
        if (modalInstances.has(modalId)) {
            modalInstances.get(modalId).dispose();
            modalInstances.delete(modalId);
        }

        // Fallback cleanup
        const backdrop = document.querySelector(".modal-backdrop");
        if (backdrop) {
            backdrop.remove();
        }
        document.body.classList.remove("modal-open");
        document.body.style.overflow = "";
        document.body.style.paddingRight = "";
    });
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

function updateStatisticCards() {
    const total = (ticketStats.pending || 0) + (ticketStats.assigned || 0) + (ticketStats.completed || 0);
    document.getElementById("total-tickets").textContent = total;
    document.getElementById("pending-tickets").textContent = ticketStats.pending || 0;
    document.getElementById("assigned-tickets").textContent = ticketStats.assigned || 0;
    document.getElementById("completed-tickets").textContent = ticketStats.completed || 0;

    const totalTickets = total || 1; // Avoid division by zero
    document.getElementById("pending-percent").textContent = `${((ticketStats.pending / totalTickets) * 100).toFixed(1)}%`;
    document.getElementById("assigned-percent").textContent = `${((ticketStats.assigned / totalTickets) * 100).toFixed(1)}%`;
    document.getElementById("completed-percent").textContent = `${((ticketStats.completed / totalTickets) * 100).toFixed(1)}%`;
}

function fetchTicketStats(timeRange = "week", startDate = null, endDate = null) {
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
            setupTicketStatsChart(data.created);
        })
        .catch((error) => {
            console.error("Error fetching ticket stats:", error);
            setupTicketStatsChart({ pending: { labels: [], data: [] }, assigned: { labels: [], data: [] }, completed: { labels: [], data: [] } });
        });
}

function fetchTickets(page = 1, perPage = 10) {
    fetch(`/api/warga/tickets?page=${page}&per_page=${perPage}`, {
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
            console.log(data); // Debug API response
            renderTickets(data.data || [], data.current_page, data.last_page, data.per_page, data.total);
        })
        .catch((error) => {
            console.error("Error fetching tickets:", error, error.message);
            renderTickets([], 1, 1, 10, 0);
        });
}

function setupTicketStatsChart(data) {
    const chartContainer = document.getElementById("ticketStatsChart");
    if (!chartContainer) return;

    chartContainer.style.height = "300px";
    const ctx = chartContainer.getContext("2d");
    if (ticketStatsChart) {
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
                legend: {
                    position: "top",
                    align: "end",
                },
                tooltip: {
                    mode: "index",
                    intersect: false,
                    callbacks: {
                        label: function (context) {
                            return `${context.dataset.label}: ${context.parsed.y}`;
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
                    ticks: {
                        stepSize: 1,
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
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
    const data = [ticketStats.pending || 0, ticketStats.assigned || 0, ticketStats.completed || 0];
    const total = data.reduce((a, b) => a + b, 0);

    if (total === 0) {
        chartContainer.parentElement.innerHTML = '<p class="text-center text-muted">Tidak ada data distribusi aduan.</p>';
        document.getElementById("completed-percent").textContent = "0%";
        document.getElementById("pending-percent").textContent = "0%";
        document.getElementById("assigned-percent").textContent = "0%";
        return;
    }

    if (ticketDistributionChart) {
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
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percent = total > 0 ? ((value / total) * 100).toFixed(1) : "0.0";
                            return `${label}: ${value} (${percent}%)`;
                        },
                    },
                },
            },
        },
    });

    document.getElementById("completed-percent").textContent = `${((data[2] / total) * 100).toFixed(1)}%`;
    document.getElementById("pending-percent").textContent = `${((data[0] / total) * 100).toFixed(1)}%`;
    document.getElementById("assigned-percent").textContent = `${((data[1] / total) * 100).toFixed(1)}%`;
}

function renderTickets(tickets, currentPage, lastPage, perPage, total) {
    const tableBody = document.getElementById("ticket-list-table");
    const paginationLinks = document.getElementById("paginationLinks");
    if (!tableBody) return;

    if (!Array.isArray(tickets) || tickets.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center">
                    <div class="alert alert-info m-0">
                        Anda belum memiliki tiket. Silakan klik "Buat Aduan" untuk membuat tiket baru.
                    </div>
                </td>
            </tr>
        `;
        paginationLinks.innerHTML = "";
        return;
    }

    // Create modals only if they don't already exist
    const modalTemplate = document.getElementById("ticketDetailModalTemplate").innerHTML;
    const chatModalTemplate = document.getElementById("chatModalTemplate").innerHTML;
    tickets.forEach((ticket) => {
        const modalId = `detailModal-${ticket.id}`;
        const chatModalId = `chatModal-${ticket.id}`;
        if (!document.getElementById(modalId)) {
            const modalHtml = modalTemplate.replace(/ID_PLACEHOLDER/g, ticket.id);
            document.body.insertAdjacentHTML("beforeend", modalHtml);
        }
        if (!document.getElementById(chatModalId)) {
            const chatModalHtml = chatModalTemplate.replace(/ID_PLACEHOLDER/g, ticket.id);
            document.body.insertAdjacentHTML("beforeend", chatModalHtml);
        }
    });

    tableBody.innerHTML = tickets.map((ticket, index) => {
        const status = ticket.status === 0 ? "belum" : ticket.status === 1 ? "direspon" : "selesai";
        const statusBadge = ticket.status === 0
            ? '<span class="badge bg-warning">Pending</span>'
            : ticket.status === 1
            ? '<span class="badge bg-info">Direspon</span>'
            : '<span class="badge bg-success">Selesai</span>';

        const pengaduDisplay = ticket.is_guest
            ? `${ticket.pengadu} <span class="fs-xs fw-semibold d-inline-block py-1 px-2 rounded-pill bg-secondary-light text-secondary ms-1">Guest</span>`
            : ticket.pengadu;

        const modalId = `detailModal-${ticket.id}`;
        const chatModalId = `chatModal-${ticket.id}`;

        return `
            <tr class="text-nowrap" data-status-row="${status}">
                <td>${((currentPage - 1) * perPage) + index + 1}</td>
                <td>${ticket.ticket_code || "N/A"}</td>
                <td>${ticket.svc_name || "N/A"}</td>
                <td>${ticket.title || "No Title"}</td>
                <td>${ticket.description || "No Description"}</td>
                <td>${pengaduDisplay}</td>
                <td class="text-center">${statusBadge}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#${modalId}" data-ticket-id="${ticket.id}" title="Detail">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#${chatModalId}" title="Pesan">
                        <i class="fas fa-comments"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join("");

    paginationLinks.innerHTML = generatePagination(currentPage, lastPage, perPage);
    attachPaginationListeners();
}

function generatePagination(currentPage, lastPage, perPage) {
    if (lastPage <= 1) return "";

    let html = '<nav aria-label="Page navigation"><ul class="pagination">';
    html += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" data-page="${currentPage - 1}" aria-label="Previous">
                <span aria-hidden="true">«</span>
            </a>
        </li>
    `;

    const maxPagesToShow = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
    let endPage = Math.min(lastPage, startPage + maxPagesToShow - 1);

    if (endPage - startPage + 1 < maxPagesToShow) {
        startPage = Math.max(1, endPage - maxPagesToShow + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" data-page="${i}">${i}</a>
            </li>
        `;
    }

    html += `
        <li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" data-page="${currentPage + 1}" aria-label="Next">
                <span aria-hidden="true">»</span>
            </a>
        </li>
    `;
    html += '</ul></nav>';
    return html;
}

function attachPaginationListeners() {
    document.querySelectorAll("#paginationLinks .page-link").forEach((link) => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const page = parseInt(this.getAttribute("data-page"));
            if (page && !this.parentElement.classList.contains("disabled")) {
                fetchTickets(page);
            }
        });
    });
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
            const modalId = `detailModal-${ticketId}`;
            const modal = document.getElementById(modalId);
            if (!modal) {
                console.error(`Modal ${modalId} not found`);
                return;
            }

            document.getElementById(`modal-ticket-code-${ticketId}`).textContent = data.ticket_code || "N/A";
            document.getElementById(`modal-title-${ticketId}`).textContent = data.title || "No Title";
            document.getElementById(`modal-description-${ticketId}`).textContent = data.description || "No Description";
            document.getElementById(`modal-svc-name-${ticketId}`).textContent = data.svc_name || "N/A";
            document.getElementById(`modal-unit-name-${ticketId}`).textContent = data.unit_name || "N/A";
            document.getElementById(`modal-created-at-${ticketId}`).textContent = data.created_at
                ? new Date(data.created_at).toLocaleString("id-ID", { timeZone: "Asia/Jakarta" })
                : "N/A";

            const statusElement = document.getElementById(`modal-status-${ticketId}`);
            if (data.status === 0) {
                statusElement.textContent = "Pending";
                statusElement.className = "fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-warning-light text-warning";
            } else if (data.status === 1) {
                statusElement.textContent = "Ditugaskan";
                statusElement.className = "fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-info-light text-info";
            } else {
                statusElement.textContent = "Selesai";
                statusElement.className = "fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success";
            }

            const locationElement = document.getElementById(`modal-location-${ticketId}`);
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
                locationElement.innerHTML = '<p class="text-muted">Lokasi tidak tersedia.</p>';
            }

            const modalInstance = new bootstrap.Modal(modal);
            modalInstances.set(modalId, modalInstance);
            modalInstance.show();
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
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);
        L.marker([latitude, longitude]).addTo(map).bindPopup("Lokasi Aduan").openPopup();
        mapElement._map = map;
    }
}