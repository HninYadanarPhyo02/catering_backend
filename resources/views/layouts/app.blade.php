<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: rgb(233, 233, 233);
        }

        .sidebar {
            height: 100vh;
            position: fixed;
            width: 240px;
            background-color: #1f2937;
            color: #ffffff;
            overflow-y: auto;
            /* Enable vertical scrolling */
            scrollbar-width: thin;
            /* For Firefox */
        }

        /* Optional: for WebKit browsers like Chrome */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 4px;
        }

        .sidebar a {
            /* color: white; */
            color: #fff;
            display: block;
            padding: 10px 15px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: rgb(152, 167, 165);
            /* Complementary accent */
            color: black;
        }

        .content {
            margin-left: 220px;
            padding: 20px;
            flex-grow: 1;
        }

        .topbar {
            background-color: rgb(233, 233, 233);
            padding: 10px 20px;
            border-bottom: 1px solid #dee2e6;
        }

        .logo {
            height: 60px;
        }

        .sidebar a i {
            width: 20px;
        }

        .alert {
            display: block !important;
        }
    </style>

</head>


<body>
    <div class="sidebar">
        <div class="text-center py-3">
            <img src="{{ asset('images/food.png') }}" alt="System Logo" class="img-fluid" style="max-height: 110px;">
        </div>

        <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
        <a href="{{ route('order') }}"><i class="fas fa-receipt me-2"></i> Available Menus</a>
        <a href="{{ route('menu') }}"><i class="fas fa-utensils me-2"></i> Menus</a>
        <a href="{{ route('holidays') }}"><i class="fa-solid fa-sun"></i> Holidays</a>
        <a href="{{ route('cusmang') }}"><i class="fas fa-users me-2"></i> Customer</a>
        <a href="{{ route('invoices') }}"><i class="fas fa-file-invoice-dollar me-2"></i> Invoices</a>
        <!-- <a href="{{ route('payment') }}"> <i class="fas fa-credit-card me-2"></i> Payments</a> -->
        <a href="{{ route('reports') }}"><i class="fas fa-chart-line me-2"></i> Analytics & Reports</a>
        <a href="{{ route('feedback') }}"><i class="fas fa-comment-dots me-2"></i> Feedback</a>
        <a href="{{ route('announcement') }}"><i class="fas fa-bullhorn me-2"></i> Announcements</a>
        <a href="{{ route('attendance.index') }}"><i class="fas fa-calendar-check me-2"></i> Attendance</a>
        <a href="{{ route('registeredorder') }}"><i class="fas fa-clipboard-list me-2"></i> Employee's Orders</a>

    </div>


    <div class="content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="mb-2">
                    <h3 class="fw-bold" style="color: #264653; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                        Catering Management System
                    </h3>
                </div>
            </div>


            <div class="dropdown">
    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-user-circle fa-2x me-2"></i>
       <span>{{ Auth::user()->name }}</span>

    </a>

    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
        <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user me-2"></i> Profile</a></li>
        <li><hr class="dropdown-divider"></li>

        <!-- Logout trigger -->
        <li>
            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>

<!-- Hidden logout form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

        </div>


        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>