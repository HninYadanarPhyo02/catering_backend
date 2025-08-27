@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-3 mt-4">

    {{-- Dashboard Cards --}}
    <div class="d-flex flex-wrap gap-3 mb-4 justify-content-center">
        @php
        $cards = [
            ['title' => 'Monthly Menus', 'value' => $monthlymenus, 'icon' => 'utensils', 'bg' => '#FFA726', 'text' => '#fff'],
            ['title' => 'Monthly Available Days', 'value' => $monthlyavailable, 'icon' => 'calendar-alt', 'bg' => '#66BB6A', 'text' => '#fff'],
            ['title' => 'Total Users', 'value' => $totalemployees, 'icon' => 'users', 'bg' => '#FFB74D', 'text' => '#fff'],
            ['title' => 'Monthly Orders', 'value' => $monthlyorders, 'icon' => 'receipt', 'bg' => '#EF5350', 'text' => '#fff'],
            ['title' => 'Monthly Checkout', 'value' => $totalCheckout, 'icon' => 'check-circle', 'bg' => '#42A5F5', 'text' => '#fff'],
        ];
        @endphp

        @foreach($cards as $card)
        <div class="card shadow-sm" style="width: 245px; border-left: 5px solid {{ $card['bg'] }}; background-color: {{ $card['bg'] }}; color: {{ $card['text'] }};">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">{{ $card['title'] }}</h6>
                    <h3 class="mb-0">{{ $card['value'] }}</h3>
                </div>
                <div>
                    <i class="fas fa-{{ $card['icon'] }} fa-2x"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Dashboard Overview --}}
    <div class="container-fluid mt-4">
        <h4 class="mb-4">Dashboard Overview</h4>
        <div class="row gy-4 gx-3">

            {{-- Line Chart (Full Width) --}}
    <div class="row mb-0">
        <div class="col-12">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-dark">Sales Overview <small class="text-muted">(Last 14 Days)</small></h6>
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bar + Pie Charts (Side by Side) --}}
    <div class="row gy-2 gx-3">
        {{-- Bar Chart --}}
        <div class="col-12 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-dark">Top Selling Items</h6>
                    <div class="chart-container chart-tall">
                        <canvas id="topItemsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pie Chart --}}
        <div class="col-12 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-dark">Feedback Ratings</h6>
                    <div class="chart-container chart-pie">
                        <canvas id="ratingsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

        </div>
    </div>

    {{-- Monthly Invoices --}}
    <div class="container-fluid px-3 mt-4">
    <div class="card border-0 shadow-sm mt-4 ">
        <div class="card-header" style="background-color: #FFF3E0; border-bottom: 1px solid #dee2e6;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark">Monthly Invoices</h5>
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
                    <thead style="background-color: #FFE0B2;">
                        <tr>
                            <th>Invoice #</th>
                            <th>Employee</th>
                            <th>Month</th>
                            <th>Year</th>
                            <th>Total Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyInvoices as $invoice)
                        @if($invoice->employee && $invoice->details->isNotEmpty())
                        <tr>
                            <td class="fw-semibold text-dark">{{ $invoice->invoice_id }}</td>
                            <td class="text-dark">{{ $invoice->employee->name }}</td>
                            <td>{{ \Carbon\Carbon::create()->month($invoice->month)->format('F') }}</td>
                            <td>{{ $invoice->year }}</td>
                            <td class="fw-semibold text-dark">{{ number_format($invoice->total_amount, 2) }} Kyats</td>
                            <td>
                                <a href="{{ route('invoices.show', $invoice->invoice_id) }}" class="btn btn-sm" style="background-color: #FFA726; color: white;">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>

                                <form action="{{ route('invoices.send-mail', $invoice->invoice_id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm"
                                        style="color: #66BB6A; border: 1px solid #66BB6A; background-color: transparent;"
                                        onmouseover="this.style.backgroundColor='#66BB6A'; this.style.color='white';"
                                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#66BB6A';"
                                        onclick="return confirm('Send invoice email to {{ $invoice->employee->name }}?')">
                                        <i class="fas fa-paper-plane me-1"></i> Send
                                    </button>
                                </form>
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
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var salesLabels = @json($salesLabels);
var salesData = @json($salesData);
const topItemsLabels = @json($topItemsLabels);
const topItemsData = @json($topItemsData);
const ratingsLabels = @json($ratingsLabels);
const ratingsCounts = @json($ratingsCounts);

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: {
                color: '#000'
            }
        }
    },
    scales: {
        x: { ticks: { color: '#000' } },
        y: { ticks: { color: '#000', stepSize: 1 }, beginAtZero: true }
    }
};


new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: salesLabels,
            datasets: [{
                label: 'Total Sales (MMK)',
                data: salesData,
                borderColor: '#FFA726',
                backgroundColor: 'rgba(255, 167, 38, 0.2)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        padding: 10,
                        callback: function(value) { return value.toLocaleString(); }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45,
                        autoSkip: false
                    }
                }
            }
        }
    });


new Chart(document.getElementById('topItemsChart'), {
    type: 'bar',
    data: {
        labels: topItemsLabels,
        datasets: [{
            label: 'Items Sold',
            data: topItemsData,
            backgroundColor: '#EF5350'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true },
                x: {
            ticks: {
                maxRotation: 50, // rotate labels
                minRotation: 45,
                autoSkip: false
            }
        }}
    }
});

new Chart(document.getElementById('ratingsChart'), {
    type: 'pie',
    data: {
        labels: ratingsLabels.map(r => r + ' Star'),
        datasets: [{
            data: ratingsCounts,
            backgroundColor: ['#EF5350','#FFB74D','#FFA726','#66BB6A','#42A5F5']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: '#000' } } } , radius: '95%'}
});
</script>

{{-- Chart container CSS --}}
<style>
.chart-container {
    position: relative;
    width: 100%;
    height: 50vh; /* fixed height so charts look neat */
}
.chart-container canvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100% !important;
    height: 100% !important;
}
.chart-container.chart-tall {
    height: 75vh; /* increase height as needed */
}
.chart-container.chart-pie {
    height: 380px; /* reduce height */
    max-width: 450px; /* optional: reduce width */
    margin: 0 auto; /* center the chart */
}

</style>

@endsection
