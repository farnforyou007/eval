<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบประเมินรายวิชา คณะการแพทย์แผนไทย</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* --- สไตล์ที่กำหนดเองเพื่อให้เหมือน Odoo UI --- */
        body {
            background-color: #f0eef0;
            /* เปลี่ยนสีพื้นหลังหลัก */
            font-family: 'Inter', 'Sarabun', sans-serif;
            font-size: 14px;
        }

        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
            height: 48px;
            display: flex;
            align-items: center;
        }

        .top-navbar .navbar-brand img {
            max-height: 32px;
        }

        .top-navbar .nav-link {
            color: #4c4c4c;
            font-weight: 500;
            border-radius: 0.25rem;
            transition: background-color 0.2s ease-in-out;
        }

        .top-navbar .nav-link:hover {
            background-color: #e9ecef;
        }

        .top-navbar .nav-link.active {
            color: #000000;
        }

        .sub-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 0.5rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 56px;
        }

        .dropdown-menu .dropdown-item {
            font-size: 0.85rem;
        }

        .breadcrumb {
            margin-bottom: 0;
        }

        .breadcrumb-item {
            font-size: 1.25rem;
        }

        .breadcrumb-item a {
            text-decoration: none;
            color: #6c757d;
        }

        .breadcrumb-item.active {
            color: #212529;
            font-weight: bold;
        }

        .header-actions .header-link {
            text-decoration: none;
            color: #4c4c4c;
            display: flex;
            align-items: center;
            margin-left: 1.5rem;
        }

        .header-actions .header-link i {
            font-size: 1rem;
        }

        .header-actions .badge {
            font-size: 0.7em;
            padding: 0.3em 0.5em;
        }

        .page-content-wrapper {
            display: flex;
            height: calc(100vh - 104px);
        }

        .main-content {
            flex-grow: 1;
            background-color: #f8f9fa;
            padding: 1rem 1.5rem;
            overflow-y: auto;
        }

        .main-content button {
            font-size: 0.85rem;
        }

        .btn-violet {
            background-color: #875a7b;
            color: #fff;

        }

        .btn-violet:hover {
            background-color: #6b4260;
            color: #fff;
        }

        .employee-card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 1.5rem;
        }

        .employee-card .avatar {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 0.5rem;
            width: 120px;
            height: 120px;
            border-radius: 0.5rem;
            font-size: 3rem;
            background-color: #875a7b;
        }

        .employee-info h1 {
            font-size: 1.75rem;
            font-weight: 700;
        }

        .employee-info .text-muted {
            font-size: 0.9rem;
        }

        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 0.5rem 0;
            margin-right: 1.5rem;
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            color: #875a7b;
            border-bottom-color: #875a7b;
            font-weight: 700;
        }

        .section-title {
            color: #875a7b;
            font-weight: bold;
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .form-label {
            color: #212529;
            font-size: medium;
        }

        .form-value {
            font-weight: 500;
        }

        .org-chart-placeholder {
            background-color: #f8f9fa;
            border: 1px dashed #ced4da;
            padding: 1.5rem;
            text-align: center;
            border-radius: 0.375rem;
        }

        /* Resume Timeline Styles */
        .resume-timeline {
            position: relative;
            padding-left: 1.5rem;
        }

        .resume-timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 5px;
            bottom: 5px;
            width: 2px;
            background-color: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-dot {
            position: absolute;
            left: -22px;
            /* Adjust based on padding-left and dot size */
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #875a7b;
        }

        .timeline-content h5 {
            font-size: 1rem;
            font-weight: 600;
        }

        .timeline-content p {
            margin-bottom: 0.25rem;
        }

        /* Sidebar Filter Styles  and employee table */
        .sidebar-filter {
            width: 240px;
            flex-shrink: 0;
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
            padding: 1rem;
        }

        .sidebar-filter .nav-link {
            color: #212529;
            padding: 0.35rem 0.5rem;
            border-radius: 0.25rem;
            margin-bottom: 0.25rem;
        }

        .sidebar-filter .nav-link.active {
            background-color: #e0e0e0;
            font-weight: 600;
        }

        .sidebar-filter .nav-link .badge {
            font-size: 0.8em;
        }

        .main-content-full {
            flex-grow: 1;
            overflow-y: auto;
        }

        .employee-table th {
            font-weight: 600;
            color: #6c757d;
            border-bottom-width: 1px;
        }

        .employee-table td {
            vertical-align: middle;
        }

        .employee-avatar {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 0.75rem;
        }

        .manager-avatar {
            width: 24px;
            height: 24px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 0.5rem;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 960px;
            flex-shrink: 0;
            background-color: #fff;
            border-left: 1px solid #dee2e6;
            padding: 1rem;
            overflow-y: auto;
        }

        .activity-feed .feed-item {
            display: flex;
            margin-bottom: 1.5rem;
        }

        .activity-feed .feed-icon {
            width: 32px;
            height: 32px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-weight: bold;
            flex-shrink: 0;
        }

        .activity-feed .feed-content {
            font-size: 0.85rem;
        }

        .activity-feed .feed-time {
            color: #6c757d;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>

    <!-- 1. แถบเมนูหลักด้านบนสุด -->
    <nav class="navbar top-navbar navbar-expand">
        <div class="container-fluid px-3">
            <span class="navbar-brand">
                <i class="bi bi-clipboard-data" style="font-size: 1.25rem; color: #875a7b;"></i>
                ระบบประเมินรายวิชา
            </span>
            <?php

            $is_admin = array('niti.c');

            ?>
            <ul class="navbar-nav">

                <?php if (isset($userdata) && in_array($userdata['username'], $is_admin)): ?>
                    <li class="nav-item"><a class="nav-link active" href="home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="employees">Employees</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="subjects">จัดการรายวิชา</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <!-- <li class="nav-item"><a class="nav-link" href="#">Learning</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Reporting</a></li> -->
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="bi bi-person-circle"> <?php if (isset($userdata)) echo $userdata['display_name_th']; ?> </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- <li><a class="dropdown-item" href="#">My Profile</a></li>
                        <li><hr class="dropdown-divider"></li> -->
                        <li><a class="dropdown-item" href="/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <!-- 2. แถบเมนูรอง (Breadcrumb และ Actions) -->
    <header class="sub-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="#"><?php echo $siteName; ?></a></li>
            </ol>
        </nav>
    </header>

    <?php

    include($page);
    ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>