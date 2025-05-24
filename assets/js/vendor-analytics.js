/**
 * File path: assets/js/vendor-analytics.js
 *
 * This file contains the JavaScript functionality for the vendor analytics page,
 * including chart initialization and data visualization.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts if they exist on the page
    if (document.getElementById('quotesChart')) {
        initializeQuotesChart();
    }

    if (document.getElementById('distributionChart')) {
        initializeDistributionChart();
    }

    // Handle date range selector
    initializeDateRangeSelector();
});

/**
 * Initialize the quotes chart
 */
function initializeQuotesChart() {
    const ctx = document.getElementById('quotesChart').getContext('2d');

    // Get chart data from the page
    const labels = JSON.parse(document.getElementById('quotesChartLabels')?.dataset.labels || '[]');
    const data = JSON.parse(document.getElementById('quotesChartData')?.dataset.values || '[]');

    // Create the chart
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: document.getElementById('quotesChartTitle')?.textContent || 'Quote Requests',
                data: data,
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                tension: 0.4,
                pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            }
        }
    });
}

/**
 * Initialize the distribution chart
 */
function initializeDistributionChart() {
    const ctx = document.getElementById('distributionChart').getContext('2d');

    // Get chart data from the page
    const labels = JSON.parse(document.getElementById('distributionChartLabels')?.dataset.labels || '[]');
    const data = JSON.parse(document.getElementById('distributionChartData')?.dataset.values || '[]');

    // Create the chart
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(99, 102, 241, 0.7)',
                    'rgba(139, 92, 246, 0.7)',
                    'rgba(236, 72, 153, 0.7)',
                    'rgba(248, 113, 113, 0.7)',
                    'rgba(251, 191, 36, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(6, 182, 212, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${context.label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Initialize date range selector functionality
 */
function initializeDateRangeSelector() {
    const rangeSelect = document.getElementById('range');
    const customDateInputs = document.getElementById('customDateInputs');

    if (!rangeSelect || !customDateInputs) {
        return;
    }

    rangeSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateInputs.classList.remove('hidden');
        } else {
            customDateInputs.classList.add('hidden');

            // Auto-submit the form when a preset range is selected
            this.closest('form').submit();
        }
    });
}

/**
 * Export chart as image
 * @param {string} chartId - The ID of the chart canvas element
 * @param {string} fileName - The filename for the downloaded image
 */
function exportChart(chartId, fileName) {
    const canvas = document.getElementById(chartId);
    if (!canvas) return;

    // Convert canvas to data URL
    const dataUrl = canvas.toDataURL('image/png');

    // Create a download link
    const link = document.createElement('a');
    link.download = fileName || 'chart.png';
    link.href = dataUrl;
    link.click();
}

/**
 * Export data to CSV
 * @param {Array} data - The data to export
 * @param {string} fileName - The filename for the downloaded CSV
 */
function exportToCSV(data, fileName) {
    if (!data || !data.length) return;

    // Column headers
    const headers = Object.keys(data[0]);

    // Create CSV content
    let csvContent = headers.join(',') + '\n';

    // Add data rows
    csvContent += data.map(row => {
        return headers.map(header => {
            // Wrap values with commas in quotes
            const value = row[header];
            if (value === null || value === undefined) {
                return '';
            }
            const stringValue = String(value);
            return stringValue.includes(',') ? `"${stringValue}"` : stringValue;
        }).join(',');
    }).join('\n');

    // Create a Blob and download link
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', fileName || 'export.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Add event listeners for export buttons if they exist
document.querySelectorAll('[data-export-chart]').forEach(button => {
    button.addEventListener('click', function() {
        const chartId = this.getAttribute('data-export-chart');
        const fileName = this.getAttribute('data-export-filename') || 'chart.png';
        exportChart(chartId, fileName);
    });
});

document.querySelectorAll('[data-export-csv]').forEach(button => {
    button.addEventListener('click', function() {
        const dataElement = document.getElementById(this.getAttribute('data-export-csv'));
        if (!dataElement) return;

        const data = JSON.parse(dataElement.dataset.exportData || '[]');
        const fileName = this.getAttribute('data-export-filename') || 'export.csv';
        exportToCSV(data, fileName);
    });
});
