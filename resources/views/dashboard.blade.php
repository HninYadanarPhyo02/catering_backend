@extends('layouts.app') {{-- Assuming you have a main layout called app.blade.php --}}

@section('title', 'Dashboard')

@section('content')
<div class="container mt-4">

    {{-- Business-style Dashboard Cards --}}
    <div class="mb-4">
        <!-- <h4 class="fw-bold" style="color: #F4A261; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        Dashboard
    </h4> -->
    </div>

    <div class="d-flex flex-wrap gap-3 mb-4">
        @php
        $cards = [
        ['title' => 'Monthly Menus', 'value' => $monthlymenus, 'icon' => 'utensils', 'color' => '#2A9D8F'],
        ['title' => 'Monthly Available Days', 'value' => $monthlyavailable, 'icon' => 'calendar-alt', 'color' => '#E9C46A'],
        ['title' => 'Total Employees', 'value' => $totalemployees, 'icon' => 'users', 'color' => '#F4A261'],
        ['title' => 'Monthly Orders', 'value' => $monthlyorders, 'icon' => 'receipt', 'color' => '#E76F51'],
        ['title' => 'Monthly Checkout', 'value' => $totalCheckout, 'icon' => 'check-circle', 'color' => '#2A9D8F'],
        ];
        @endphp

        @foreach($cards as $card)
        <div class="card shadow-sm" style="width: 245px; border-left: 5px solid {{ $card['color'] }};">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">{{ $card['title'] }}</h6>
                    <h3 class="mb-0" style="color: {{ $card['color'] }}">{{ $card['value'] }}</h3>
                </div>
                <div style="color: {{ $card['color'] }}">
                    <i class="fas fa-{{ $card['icon'] }} fa-2x"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <!-- {{-- Recent Orders (Business Style) --}}
<div class="card border-light shadow-sm mb-4">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-dark">Recent Orders</h5>
        <i class="fas fa-clock text-muted"></i>
    </div>
    <div class="card-body">
        @if($recentOrders->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="fas fa-inbox fa-2x mb-2"></i>
                <p class="mb-0">No recent orders found.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Order #</th>
                            <th scope="col">Client</th>
                            <th scope="col">Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td class="fw-semibold">#{{ $order->id }}</td>
                            <td>{{ $order->employee->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->date)->format('d M Y') }}</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ 
                                    $order->status === 'Completed' ? 'success' : 
                                    ($order->status === 'Pending' ? 'warning' : 'secondary') }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="fw-semibold">${{ number_format($order->price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div> -->
    <div class="container my-4">
        <h4 class="mb-4 text-primary">Dashboard Overview</h4>

        <div class="row gy-4 gx-3">

            <!-- Line Chart: Daily Sales -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-primary">Sales Overview <small class="text-muted">(Last 14 Days)</small></h6>
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bar Chart: Top Selling Items -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-primary">Top Selling Items</h6>
                        <div class="chart-container">
                            <canvas id="topItemsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie Chart: Feedback Ratings -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-primary">Feedback Ratings</h6>
                        <div class="chart-container">
                            <canvas id="ratingsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Adjusted Chart Style -->
    <style>
        .chart-container {
            position: relative;
            width: 100%;
            padding-top: 85%;
            /* Slightly reduced from 1:1 ratio for better layout */
        }

        .chart-container canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100% !important;
            height: 100% !important;
        }
    </style>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Chart Initialization -->
    <script>
        const salesLabels = @json($dailyLabels);
        const salesData = @json($dailySales);
        const topItemsLabels = @json($topItemsLabels);
        const topItemsData = @json($topItemsData);
        const ratingsLabels = @json($ratingsLabels);
        const ratingsCounts = @json($ratingsCounts);

        const baseTextColor = '#000'; // Strong black

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: baseTextColor, // Legend text color
                        boxWidth: 12
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: baseTextColor // X-axis label color
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: baseTextColor, // Y-axis label color
                        stepSize: 1
                    }
                }
            }
        };

        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Total Sales (MMK)',
                    data: salesData,
                    borderColor: '#2A9D8F',
                    backgroundColor: 'rgba(42, 157, 143, 0.2)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: chartOptions
        });

        new Chart(document.getElementById('topItemsChart'), {
            type: 'bar',
            data: {
                labels: @json($topItemsLabels),
                datasets: [{
                    label: 'Items Sold',
                    data: @json($topItemsData),
                    backgroundColor: 'rgba(255, 123, 14, 0.7)'
                }]
            },
            options: chartOptions
        });


        new Chart(document.getElementById('ratingsChart'), {
            type: 'pie',
            data: {
                labels: ratingsLabels.map(r => r + ' Star'),
                datasets: [{
                    label: 'Feedback Ratings',
                    data: ratingsCounts,
                    backgroundColor: [
                        '#e76f51', '#f4a261', '#e9c46a', '#2a9d8f', '#264653'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: baseTextColor
                        }
                    }
                }
            }
        });
    </script>



    <!-- {{-- Monthly Employee Invoices --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header" style="background-color: #F1FAEE; border-bottom: 1px solid #dee2e6;">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0" style="color: #264653;">Monthly Invoices</h5>
            <i class="fas fa-file-invoice-dollar text-muted"></i>
        </div>
    </div>
    <div class="card-body">
        @if($monthlyInvoices->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="fas fa-inbox fa-2x mb-2"></i>
                <p class="mb-0">No invoices found.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #e9ecef;">
                        <tr>
                            <th>Invoice ID</th>
                            <th>Employee</th>
                            <th>Month</th>
                            <th>Year</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyInvoices as $invoice)
                        <tr>
                            <td class="fw-semibold text-dark">{{ $invoice->invoice_id }}</td>
                            <td class="text-dark">{{ $invoice->employee->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::create()->month($invoice->month)->format('F') }}</td>
                            <td>{{ $invoice->year }}</td>
                            <td class="fw-semibold text-dark">${{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div> -->

    {{-- Monthly Employee Invoices --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header" style="background-color: #F1FAEE; border-bottom: 1px solid #dee2e6;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="color: #264653;">Monthly Invoices</h5>
                <i class="fas fa-file-invoice-dollar text-muted"></i>
            </div>
        </div>
        <div class="card-body">
            @if($monthlyInvoices->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="fas fa-inbox fa-2x mb-2"></i>
                <p class="mb-0">No invoices found.</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #e9ecef;">
                        <tr>
                            <th>Invoice #</th>
                            <th>Employee</th>
                            <th>Month</th>
                            <th>Year</th>
                            <th>Total Amount</th>
                            <th>Action</th> {{-- ➕ New column --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyInvoices as $invoice)
                        @if($invoice->employee && $invoice->details->isNotEmpty()) {{-- assuming 'details' relation holds orders --}}
                        <tr>
                            <td class="fw-semibold text-dark">{{ $invoice->invoice_id }}</td>
                            <td class="text-dark">{{ $invoice->employee->name }}</td>
                            <td>{{ \Carbon\Carbon::create()->month($invoice->month)->format('F') }}</td>
                            <td>{{ $invoice->year }}</td>
                            <td class="fw-semibold text-dark">{{ number_format($invoice->total_amount, 2) }} Kyats</td>
                            <td>
                                <a href="{{ route('invoices.show', $invoice->invoice_id) }}" class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                                <a href="{{ route('invoices.download', $invoice->invoice_id) }}" class="btn btn-sm btn-outline-secondary">
                                    Download
                                </a>
                            </td>
                        </tr>
                        @endif
                        @endforeach

                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    @endsection