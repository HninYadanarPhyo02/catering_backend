@extends('layouts.app')
@section('title','Orders')

@section('content')
<style>
    .chart-container {
        position: relative;
        width: 100%;
        padding-top: 40%;
        /* Adjust chart height here (40% = shorter height) */
    }

    .chart-container canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100% !important;
        height: 100% !important;
    }
</style>

<div class="container-fluid px-3 mt-4">
    <h3 class="mb-4" style="color: rgba(235, 110, 8, 0.7);">Reports & Analysis</h3>

    <section id="analytics">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card p-3 shadow-sm" style="background-color: #F8F9FA;">
                    <h5 class="text-primary mb-2">Sales Overview</h5>
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-3 shadow-sm" style="background-color: #F8F9FA;">
                    <h5 class="text-primary mb-2">User Engagement</h5>
                    <div class="chart-container">
                        <canvas id="engagementChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-3 shadow-sm" style="background-color: #F8F9FA;">
                    <h5 class="text-primary mb-2">Top Selling Items</h5>
                    <div class="chart-container">
                        <canvas id="topSellingChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-3 shadow-sm" style="background-color: #F8F9FA;">
                    <h5 class="text-primary mb-2">Monthly Sale Trends</h5>
                    <div class="chart-container">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = @json($labels);
    const salesData = @json($sales);
    const engagementData = @json($engagement);
    const topSellingLabels = @json($topSellingLabels);
    const topSellingData = @json($topSellingData);
    const monthlyTrendLabels = @json($monthlyTrendLabels);
    const monthlyRegisteredData = @json($monthlyRegisteredData);
    const monthlyAttendanceData = @json($monthlyAttendanceData);

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    };

    new Chart(document.getElementById('salesChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales',
                data: salesData,
                backgroundColor: 'rgba(12, 153, 137, 0.7)'
            }]
        },
        options: commonOptions
    });

    new Chart(document.getElementById('engagementChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Active Users',
                data: engagementData,
                borderColor: 'rgba(235, 110, 8, 0.7)',
                fill: false,
                tension: 0.3
            }]
        },
        options: commonOptions
    });

    new Chart(document.getElementById('topSellingChart'), {
        type: 'bar',
        data: {
            labels: topSellingLabels,
            datasets: [{
                label: 'Items Sold',
                data: topSellingData,
                backgroundColor: 'rgba(235, 110, 8, 0.7)',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: function(tooltipItems) {
                            return tooltipItems[0].label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 45,
                        font: {
                            size: 10
                        },
                        callback: function(value, index, values) {
                            const label = this.getLabelForValue(value);
                            return label.length > 15 ? label.substring(0, 15) + '…' : label;
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });


    // ✅ Dual Vertical Bars: Registered Orders vs Attendance (last 6 months)
    new Chart(document.getElementById('monthlyTrendChart'), {
        type: 'bar',
        data: {
            labels: monthlyTrendLabels,
            datasets: [{
                    label: 'Registered Orders',
                    data: monthlyRegisteredData,
                    backgroundColor: 'rgba(12, 153, 137, 0.7)'
                },
                {
                    label: 'Attendance (Checked Out)',
                    data: monthlyAttendanceData.map(val => val ?? 0), // fallback to 0 if null/undefined
                    backgroundColor: 'rgba(235, 110, 8, 0.7)'
                }
            ]
        },
        options: {
            ...commonOptions,
            plugins: {
                tooltip: {
                    enabled: true
                },
            },
            scales: {
                y: {
                    beginAtZero: true
                },
                x: {
                    stacked: false
                }
            }
        }
    });
</script>
@endsection