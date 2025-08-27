@extends('layouts.app')
@section('title','Analytics & Reports')

@section('content')
<style>
    .chart-container {
        position: relative;
        width: 100%;
        min-height: 300px; /* compact & responsive */
    }

    .chart-container canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100% !important;
        height: 100% !important;
    }
</style>

<div class="container-fluid px-4 mt-4">
    <h3 class="mb-4" style="color: #e76f51; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">Reports & Analysis</h3>

    <section id="analytics">
        <div class="row g-3">

            <!-- Sales Overview -->
            <div class="col-md-6">
                <div class="card p-3 shadow-sm" style="background-color: #fff3e6;">
                    <h5 class="fw-bold mb-2" style="color: #f4a261;">Sales Overview</h5>
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- User Engagement -->
            <div class="col-md-6">
                <div class="card p-3 shadow-sm" style="background-color: #fff3e6;">
                    <h5 class="fw-bold mb-2" style="color: #f4a261;">User Engagement</h5>
                    <div class="chart-container">
                        <canvas id="engagementChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Selling Items -->
            <div class="col-md-6">
                <div class="card p-3 shadow-sm" style="background-color: #fff3e6;">
                    <h5 class="fw-bold mb-2" style="color: #f4a261;">Top Selling Items</h5>
                    <div class="chart-container">
                        <canvas id="topSellingChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Monthly Sale Trends -->
            <div class="col-md-6">
                <div class="card p-3 shadow-sm" style="background-color: #fff3e6;">
                    <h5 class="fw-bold mb-2" style="color: #f4a261;">Monthly Sale Trends</h5>
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
        plugins: {
            legend: {
                labels: {
                    color: '#264653', // text color
                    font: { weight: 'bold' }
                }
            },
            tooltip: {
                backgroundColor: '#f4a261',
                titleColor: '#fff',
                bodyColor: '#fff'
            }
        },
        scales: {
            x: { 
                ticks: { color: '#264653' },
                grid: { color: '#ffe6cc' } 
            },
            y: { 
                beginAtZero: true,
                ticks: { color: '#264653' },
                grid: { color: '#ffe6cc' }
            }
        }
    };

    // Sales Overview
    new Chart(document.getElementById('salesChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales',
                data: salesData,
                backgroundColor: '#42A5F5',
                barPercentage: 1,      // width of each bar (0-1)
            categoryPercentage: 0.6  // space between bars
            }]
        },
        options: commonOptions
    });

    // User Engagement
    new Chart(document.getElementById('engagementChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Active Users',
                data: engagementData,
                borderColor: '#FFA726',
                backgroundColor: 'rgba(236, 179, 93, 0.71)',
                fill: true,
                tension: 0.3
            }]
        },
        options: commonOptions
    });

    // Top Selling Items
    new Chart(document.getElementById('topSellingChart'), {
        type: 'bar',
        data: {
            labels: topSellingLabels,
            datasets: [{
                label: 'Items Sold',
                data: topSellingData,
                backgroundColor: '#FFA726',
                barPercentage: 0.9,      // width of each bar (0-1)
            categoryPercentage: 0.6  // space between bars
            }]
        },
        options: {
            ...commonOptions,
            plugins: { legend: { display: false } }
        }
    });

    // Monthly Sale Trends (Dual Bars)
    new Chart(document.getElementById('monthlyTrendChart'), {
        type: 'bar',
        data: {
            labels: monthlyTrendLabels,
            datasets: [
                {
                    label: 'Registered Orders',
                    data: monthlyRegisteredData,
                    backgroundColor: '#42A5F5',
                    borderRadius: 4
                },
                {
                    label: 'Attendance',
                    data: monthlyAttendanceData.map(val => val ?? 0),
                    backgroundColor: '#66BB6A',
                    borderRadius: 4
                }
            ]
        },
        options: commonOptions
    });
</script>
@endsection
