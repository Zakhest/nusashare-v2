<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Alpha Admin - NusaShare' ?></title>
    <meta name="description" content="Panel administrasi NusaShare - Kelola pengguna, konten, dan keuangan platform.">

    <!-- Favicon -->
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/png">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- AdminLTE 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4/dist/css/adminlte.min.css">

    <!-- Material Symbols (untuk Alpine.js parts yang tersisa) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Alpine.js (untuk interaktivitas dinamis) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* ============================================
           NUSASHARE ADMIN — CUSTOM THEME OVERRIDES
           Primary: #6366F1 (Indigo) | Accent: #06B6D4 (Cyan)
           ============================================ */

        :root {
            --ns-primary: #6366F1;
            --ns-primary-rgb: 99, 102, 241;
            --ns-primary-light: #818CF8;
            --ns-primary-dark: #4F46E5;
            --ns-accent: #06B6D4;
            --ns-accent-rgb: 6, 182, 212;
            --ns-success: #10B981;
            --ns-warning: #F59E0B;
            --ns-danger: #EF4444;
            --ns-sidebar-bg: #1E2130;
            --ns-sidebar-border: rgba(255,255,255,0.06);
            --ns-sidebar-text: rgba(255,255,255,0.70);
            --ns-sidebar-active-bg: rgba(99, 102, 241, 0.15);
            --ns-sidebar-active-text: #A5B4FC;
            --ns-sidebar-active-border: #6366F1;
            --ns-sidebar-header-bg: #161927;
        }

        /* ── Font ── */
        body, .app-wrapper {
            font-family: 'Inter', sans-serif !important;
        }

        /* ── Sidebar ── */
        .app-sidebar {
            background-color: var(--ns-sidebar-bg) !important;
            border-right: 1px solid var(--ns-sidebar-border) !important;
            width: 260px !important;
        }

        .sidebar-brand {
            background-color: var(--ns-sidebar-header-bg) !important;
            border-bottom: 1px solid var(--ns-sidebar-border) !important;
            padding: 1rem 1.25rem !important;
            min-height: 64px;
        }

        .sidebar-brand .brand-text {
            font-weight: 800;
            font-size: 1rem;
            color: #fff !important;
            letter-spacing: -0.025em;
        }

        .brand-badge {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--ns-accent);
        }

        /* Nav items */
        .nav-sidebar .nav-item .nav-link {
            color: var(--ns-sidebar-text) !important;
            border-radius: 8px !important;
            margin: 1px 8px !important;
            padding: 8px 12px !important;
            transition: all 0.18s ease !important;
            font-size: 0.82rem;
            font-weight: 500;
        }

        .nav-sidebar .nav-item .nav-link:hover {
            background-color: rgba(255,255,255,0.06) !important;
            color: #fff !important;
        }

        .nav-sidebar .nav-item .nav-link.active {
            background-color: var(--ns-sidebar-active-bg) !important;
            color: var(--ns-sidebar-active-text) !important;
            border-left: 3px solid var(--ns-sidebar-active-border) !important;
            font-weight: 600;
        }

        .nav-sidebar .nav-item .nav-link .nav-icon {
            color: inherit !important;
        }

        /* Nav group label */
        .nav-header {
            color: rgba(255,255,255,0.30) !important;
            font-size: 9px !important;
            font-weight: 700 !important;
            letter-spacing: 0.12em !important;
            text-transform: uppercase !important;
            padding: 14px 20px 4px !important;
        }

        /* Sidebar footer / user panel */
        .sidebar-footer {
            background-color: var(--ns-sidebar-header-bg) !important;
            border-top: 1px solid var(--ns-sidebar-border) !important;
            padding: 0.75rem 1rem;
        }

        .sidebar-user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--ns-primary), var(--ns-accent));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar-user-name {
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .sidebar-user-role {
            font-size: 10px;
            color: rgba(255,255,255,0.40);
        }

        /* ── Top Navbar ── */
        .app-header.navbar {
            background-color: #fff !important;
            border-bottom: 1px solid #E5E7EB !important;
            min-height: 64px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }

        .app-header .navbar-nav .nav-link {
            color: #6B7280 !important;
            border-radius: 8px;
            padding: 6px 10px !important;
            transition: all 0.15s;
        }

        .app-header .navbar-nav .nav-link:hover {
            background-color: #F3F4F6;
            color: #111827 !important;
        }

        /* Breadcrumb in header */
        .page-breadcrumb .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 12px;
        }

        /* ── Main Content Area ── */
        .app-main {
            background-color: #F8F9FB !important;
        }

        .app-content-header {
            background: #fff;
            border-bottom: 1px solid #E5E7EB;
            padding: 0.75rem 1.5rem;
        }

        .app-content-header .page-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #111827;
        }

        .app-content {
            padding: 1.5rem !important;
        }

        /* ── NusaShare Brand Cards (Bootstrap override) ── */
        .card {
            border-radius: 12px !important;
            border: 1px solid #E5E7EB !important;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05) !important;
        }

        .card-header {
            background: #fff !important;
            border-bottom: 1px solid #E5E7EB !important;
            border-radius: 12px 12px 0 0 !important;
            padding: 1rem 1.25rem !important;
            font-weight: 600;
        }

        /* Small box stats */
        .small-box {
            border-radius: 14px !important;
            border: 1px solid #E5E7EB !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06) !important;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .small-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.10) !important;
        }

        .small-box .inner h3 {
            font-size: 1.8rem;
            font-weight: 800;
        }

        .small-box .inner p {
            font-size: 0.78rem;
            font-weight: 500;
        }

        .small-box .icon {
            font-size: 3.5rem !important;
        }

        /* Info Box */
        .info-box {
            border-radius: 12px !important;
            border: 1px solid #E5E7EB !important;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05) !important;
        }

        /* ── Buttons ── */
        .btn-primary, .btn-ns-primary {
            background-color: var(--ns-primary) !important;
            border-color: var(--ns-primary) !important;
            color: #fff !important;
        }

        .btn-primary:hover, .btn-ns-primary:hover {
            background-color: var(--ns-primary-dark) !important;
            border-color: var(--ns-primary-dark) !important;
        }

        .btn-outline-primary {
            color: var(--ns-primary) !important;
            border-color: var(--ns-primary) !important;
        }

        .btn-outline-primary:hover {
            background-color: var(--ns-primary) !important;
            color: #fff !important;
        }

        /* ── Badges / pills ── */
        .badge-ns-primary {
            background-color: rgba(var(--ns-primary-rgb), 0.12);
            color: var(--ns-primary);
            border: 1px solid rgba(var(--ns-primary-rgb), 0.25);
            border-radius: 20px;
            padding: 2px 8px;
            font-size: 10px;
            font-weight: 700;
        }

        .badge-ns-accent {
            background-color: rgba(var(--ns-accent-rgb), 0.12);
            color: var(--ns-accent);
            border: 1px solid rgba(var(--ns-accent-rgb), 0.25);
            border-radius: 20px;
            padding: 2px 8px;
            font-size: 10px;
            font-weight: 700;
        }

        .badge-active {
            background-color: rgba(16,185,129,0.12);
            color: #059669;
            border: 1px solid rgba(16,185,129,0.25);
            border-radius: 20px;
            padding: 2px 8px;
            font-size: 10px;
            font-weight: 700;
        }

        .badge-warning-soft {
            background-color: rgba(245,158,11,0.12);
            color: #D97706;
            border: 1px solid rgba(245,158,11,0.25);
            border-radius: 20px;
            padding: 2px 8px;
            font-size: 10px;
            font-weight: 700;
        }

        .badge-danger-soft {
            background-color: rgba(239,68,68,0.12);
            color: #DC2626;
            border: 1px solid rgba(239,68,68,0.25);
            border-radius: 20px;
            padding: 2px 8px;
            font-size: 10px;
            font-weight: 700;
        }

        .badge-muted-soft {
            background-color: rgba(107,114,128,0.10);
            color: #6B7280;
            border: 1px solid rgba(107,114,128,0.20);
            border-radius: 20px;
            padding: 2px 8px;
            font-size: 10px;
            font-weight: 700;
        }

        /* ── Status indicator dot ── */
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        /* ── Tables ── */
        .table th {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6B7280;
            border-top: none;
        }

        .table td {
            font-size: 13px;
            vertical-align: middle;
        }

        .table-hover > tbody > tr:hover > * {
            background-color: rgba(var(--ns-primary-rgb), 0.03);
        }

        /* ── Sidebar Mini State ── */
        .sidebar-mini .app-sidebar {
            width: 4.6rem !important;
        }

        /* ── Active Nav Indicator Pulse ── */
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.4); }
        }

        .pulse-dot {
            animation: pulse-dot 1.8s infinite;
        }

        /* ── Custom scrollbar ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #F3F4F6; }
        ::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--ns-primary); }

        /* ── Alpine modal overlay ── */
        [x-cloak] { display: none !important; }

        /* ── Transition for content sections ── */
        .section-enter {
            animation: sectionFadeIn 0.25s ease forwards;
        }

        @keyframes sectionFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Topbar status pill ── */
        .topbar-status-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background-color: rgba(16,185,129,0.08);
            border: 1px solid rgba(16,185,129,0.20);
            border-radius: 99px;
            font-size: 11px;
            font-weight: 600;
            color: #059669;
        }

        .topbar-maintenance-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background-color: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.20);
            border-radius: 99px;
            font-size: 11px;
            font-weight: 600;
            color: #DC2626;
        }

        /* ── Sidebar nav badge (pending count) ── */
        .nav-badge {
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            font-size: 10px;
            font-weight: 700;
            border-radius: 99px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Primary color for form controls focus ── */
        .form-control:focus,
        .form-select:focus {
            border-color: var(--ns-primary) !important;
            box-shadow: 0 0 0 3px rgba(var(--ns-primary-rgb), 0.12) !important;
        }

        /* ── section tab-like content ── */
        .ns-section { display: none; }
        .ns-section.active { display: block; }

        /* ── hover effect for action buttons in table ── */
        .btn-icon-xs {
            width: 28px;
            height: 28px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 7px;
            font-size: 14px;
            border: 1px solid transparent;
            transition: all 0.15s;
        }
    </style>

</head>
<body class="layout-fixed sidebar-expand-lg" x-data="adminPanelStore()">

    <div class="app-wrapper">

        <!-- ══════════════════════════════════════
             TOP NAVBAR
        ═══════════════════════════════════════ -->
        <nav class="app-header navbar navbar-expand" id="topbar-nav">
            <div class="container-fluid px-3">

                <!-- Sidebar Toggle (mobile) -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list fs-5"></i>
                        </a>
                    </li>
                </ul>

                <!-- Page Title (dynamic via Alpine) -->
                <div class="d-none d-md-flex align-items-center gap-2 ms-2">
                    <i class="bi bi-house-door-fill text-muted" style="font-size:13px;"></i>
                    <span class="text-muted" style="font-size:12px;">NusaShare Admin</span>
                    <span class="text-muted">/</span>
                    <span class="fw-semibold text-capitalize" style="font-size:12px; color:#111827;"
                          x-text="activeTab.replace(/_/g, ' ')"></span>
                </div>

                <!-- Right Controls -->
                <ul class="navbar-nav ms-auto align-items-center gap-2">

                    <!-- System Status Pill -->
                    <li class="nav-item d-none d-lg-block">
                        <div class="topbar-status-pill" x-show="!systemSettings.maintenanceMode">
                            <span class="status-dot bg-success pulse-dot"></span>
                            Sistem Online
                        </div>
                        <div class="topbar-maintenance-pill" x-show="systemSettings.maintenanceMode" x-cloak>
                            <span class="status-dot bg-danger pulse-dot"></span>
                            Maintenance Aktif
                        </div>
                    </li>

                    <!-- Visit Site -->
                    <li class="nav-item d-none d-lg-block">
                        <a href="<?= base_url('explore') ?>" target="_blank"
                           class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" style="font-size:11px; border-radius:8px;">
                            <i class="bi bi-box-arrow-up-right"></i>
                            Kunjungi Situs
                        </a>
                    </li>

                    <!-- Notification Dropdown — pakai Bootstrap native dropdown agar otomatis viewport-aware -->
                    <li class="nav-item dropdown">
                        <button class="nav-link position-relative" type="button"
                                id="notif-dropdown-btn"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                style="background:none; border:none; border-radius:8px;">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                                  style="background-color:var(--ns-primary); font-size:9px; padding:3px 5px;">
                                3
                            </span>
                        </button>

                        <div class="dropdown-menu dropdown-menu-end shadow-lg"
                             aria-labelledby="notif-dropdown-btn"
                             style="width:300px; border-radius:12px; border:1px solid #E5E7EB;">
                            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                <span class="fw-bold" style="font-size:13px;">Notifikasi Admin</span>
                                <span style="font-size:11px; color:var(--ns-accent); cursor:pointer;">
                                    Tandai Semua Dibaca
                                </span>
                            </div>
                            <div style="max-height:240px; overflow-y:auto;">
                                <a href="#" class="dropdown-item py-2 px-3 border-bottom" style="white-space:normal;">
                                    <p class="fw-semibold mb-0" style="font-size:12px;">5 Top-Up Pending Verifikasi</p>
                                    <small class="text-muted">12 menit yang lalu</small>
                                </a>
                                <a href="#" class="dropdown-item py-2 px-3 border-bottom" style="white-space:normal;">
                                    <p class="fw-semibold mb-0" style="font-size:12px;">Kreator Baru Mengajukan Approval</p>
                                    <small class="text-muted">1 jam yang lalu</small>
                                </a>
                                <a href="#" class="dropdown-item py-2 px-3" style="white-space:normal;">
                                    <p class="fw-semibold mb-0" style="font-size:12px;">Laporan NSFW Masuk (ID: 104)</p>
                                    <small class="text-muted">3 jam yang lalu</small>
                                </a>
                            </div>
                        </div>
                    </li>

                    <!-- Admin user dropdown -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                           data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration:none;">
                            <div class="sidebar-user-avatar" style="width:30px; height:30px; font-size:11px; border-radius:8px;">
                                <?= strtoupper(substr($adminUsername ?? 'A', 0, 2)) ?>
                            </div>
                            <span class="d-none d-lg-block" style="font-size:12px; font-weight:600; color:#111827;">
                                <?= esc($adminUsername ?? 'Admin') ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="border-radius:10px; min-width:180px;">
                            <li>
                                <span class="dropdown-item-text text-muted" style="font-size:11px;">
                                    Super Administrator
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="<?= base_url('logout') ?>" class="dropdown-item text-danger d-flex align-items-center gap-2" style="font-size:13px;">
                                    <i class="bi bi-box-arrow-right"></i> Keluar
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </nav>

        <!-- ══════════════════════════════════════
             SIDEBAR
        ═══════════════════════════════════════ -->
        <aside class="app-sidebar" data-bs-theme="dark">

            <!-- Brand / Logo -->
            <div class="sidebar-brand d-flex align-items-center gap-2">
                <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="NusaShare"
                     style="width:34px; height:34px; object-fit:contain; border-radius:8px;">
                <div class="sidebar-brand-text overflow-hidden">
                    <div class="brand-text">NusaShare</div>
                    <div class="brand-badge">Alpha Admin</div>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <div class="sidebar-wrapper" style="flex:1; overflow-y:auto; overflow-x:hidden;">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

                        <!-- ─── UTAMA ─── -->
                        <li class="nav-header">UTAMA</li>

                        <li class="nav-item">
                            <a href="#" class="nav-link" :class="activeTab === 'dashboard' ? 'active' : ''"
                               @click.prevent="setTab('dashboard')">
                                <i class="nav-icon bi bi-speedometer2"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" :class="activeTab === 'users' ? 'active' : ''"
                               @click.prevent="setTab('users')">
                                <i class="nav-icon bi bi-people-fill"></i>
                                <p>Manajemen User</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" :class="activeTab === 'creators' ? 'active' : ''"
                               @click.prevent="setTab('creators')">
                                <i class="nav-icon bi bi-person-badge-fill"></i>
                                <p>Manajemen Kreator</p>
                            </a>
                        </li>

                        <!-- ─── MODERASI KONTEN ─── -->
                        <li class="nav-header">MODERASI KONTEN</li>

                        <li class="nav-item">
                            <a href="#" class="nav-link" :class="activeTab === 'works' ? 'active' : ''"
                               @click.prevent="setTab('works')">
                                <i class="nav-icon bi bi-book-fill"></i>
                                <p>Kelola Karya &amp; Bab</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" :class="activeTab === 'gallery' ? 'active' : ''"
                               @click.prevent="setTab('gallery')">
                                <i class="nav-icon bi bi-images"></i>
                                <p>Galeri Media</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link position-relative" :class="activeTab === 'reports' ? 'active' : ''"
                               @click.prevent="setTab('reports')">
                                <i class="nav-icon bi bi-exclamation-triangle-fill"></i>
                                <p>
                                    Laporan Konten
                                    <span class="badge nav-badge ms-auto"
                                          style="background-color:#EF4444;"
                                          x-show="getPendingReportsCount() > 0"
                                          x-text="getPendingReportsCount()">
                                    </span>
                                </p>
                            </a>
                        </li>

                        <!-- ─── KEUANGAN ─── -->
                        <li class="nav-header">KEUANGAN</li>

                        <li class="nav-item">
                            <a href="#" class="nav-link" :class="activeTab === 'transactions' ? 'active' : ''"
                               @click.prevent="setTab('transactions')">
                                <i class="nav-icon bi bi-arrow-left-right"></i>
                                <p>Riwayat Transaksi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" :class="activeTab === 'topup' ? 'active' : ''"
                               @click.prevent="setTab('topup')">
                                <i class="nav-icon bi bi-credit-card-fill"></i>
                                <p>
                                    Manajemen Top Up
                                    <span class="badge nav-badge ms-auto"
                                          style="background-color:#F59E0B;"
                                          x-show="getPendingTopupsCount() > 0"
                                          x-text="getPendingTopupsCount()">
                                    </span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" :class="activeTab === 'economy' ? 'active' : ''"
                               @click.prevent="setTab('economy')">
                                <i class="nav-icon bi bi-database-fill"></i>
                                <p>Ekonomi &amp; Saldo</p>
                            </a>
                        </li>

                        <!-- ─── CMS & LANDING ─── -->
                        <li class="nav-header">CMS &amp; LANDING</li>

                        <li class="nav-item">
                            <a href="#" class="nav-link" :class="activeTab === 'cms_landing' ? 'active' : ''"
                               @click.prevent="setTab('cms_landing')">
                                <i class="nav-icon bi bi-layout-text-sidebar-reverse"></i>
                                <p>CMS Landing Page</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" :class="activeTab === 'cms_faq' ? 'active' : ''"
                               @click.prevent="setTab('cms_faq')">
                                <i class="nav-icon bi bi-question-circle-fill"></i>
                                <p>Kelola FAQ</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" :class="activeTab === 'cms_pages' ? 'active' : ''"
                               @click.prevent="setTab('cms_pages')">
                                <i class="nav-icon bi bi-file-earmark-text-fill"></i>
                                <p>Halaman Statis</p>
                            </a>
                        </li>

                        <!-- ─── PENGATURAN ─── -->
                        <li class="nav-header">PENGATURAN</li>

                        <li class="nav-item">
                            <a href="#" class="nav-link" :class="activeTab === 'notifications' ? 'active' : ''"
                               @click.prevent="setTab('notifications')">
                                <i class="nav-icon bi bi-bell-fill"></i>
                                <p>Sistem Notifikasi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" :class="activeTab === 'audit' ? 'active' : ''"
                               @click.prevent="setTab('audit')">
                                <i class="nav-icon bi bi-shield-lock-fill"></i>
                                <p>Audit Log Admin</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" :class="activeTab === 'system' ? 'active' : ''"
                               @click.prevent="setTab('system')">
                                <i class="nav-icon bi bi-gear-fill"></i>
                                <p>Pengaturan Sistem</p>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div class="sidebar-user-avatar">
                        <?= strtoupper(substr($adminUsername ?? 'A', 0, 2)) ?>
                    </div>
                    <div class="overflow-hidden">
                        <div class="sidebar-user-name text-truncate"><?= esc($adminUsername ?? 'Admin') ?></div>
                        <div class="sidebar-user-role">Super Administrator</div>
                    </div>
                </div>
                <a href="<?= base_url('logout') ?>" title="Keluar"
                   class="d-flex align-items-center justify-content-center flex-shrink-0"
                   style="width:30px; height:30px; border-radius:8px; background:rgba(239,68,68,0.10); color:#EF4444; transition:all 0.15s; text-decoration:none;"
                   onmouseover="this.style.background='rgba(239,68,68,0.20)'" onmouseout="this.style.background='rgba(239,68,68,0.10)'">
                    <i class="bi bi-box-arrow-right" style="font-size:14px;"></i>
                </a>
            </div>

        </aside>

        <!-- ══════════════════════════════════════
             MAIN CONTENT
        ═══════════════════════════════════════ -->
        <main class="app-main">

            <!-- Content Header -->
            <div class="app-content-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h1 class="page-title mb-0 text-capitalize" x-text="activeTab.replace(/_/g, ' ')"></h1>
                        <small class="text-muted" style="font-size:11px;">NusaShare Alpha Admin Panel</small>
                    </div>
                    <div class="d-none d-md-flex align-items-center gap-2">
                        <span class="text-muted" style="font-size:11px;">
                            <?= date('d M Y, H:i') ?> WIB
                        </span>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="app-content">

                <!-- 1. DASHBOARD -->
                <div x-show="activeTab === 'dashboard'" class="section-enter">
                    <?= view('admin/sections/dashboard') ?>
                </div>

                <!-- 2. USER MANAGEMENT -->
                <div x-show="activeTab === 'users'" class="section-enter">
                    <?= view('admin/sections/user_management') ?>
                </div>

                <!-- 3. CREATOR MANAGEMENT -->
                <div x-show="activeTab === 'creators'" class="section-enter">
                    <?= view('admin/sections/creator_management') ?>
                </div>

                <!-- 4. CONTENT WORKS -->
                <div x-show="activeTab === 'works'" class="section-enter">
                    <?= view('admin/sections/content_works') ?>
                </div>

                <!-- 5. CONTENT GALLERY -->
                <div x-show="activeTab === 'gallery'" class="section-enter">
                    <?= view('admin/sections/content_gallery') ?>
                </div>

                <!-- 6. CONTENT REPORTS -->
                <div x-show="activeTab === 'reports'" class="section-enter">
                    <?= view('admin/sections/content_reports') ?>
                </div>

                <!-- 7. FINANCE TRANSACTIONS -->
                <div x-show="activeTab === 'transactions'" class="section-enter">
                    <?= view('admin/sections/finance_transactions') ?>
                </div>

                <!-- 8. FINANCE TOPUP -->
                <div x-show="activeTab === 'topup'" class="section-enter">
                    <?= view('admin/sections/finance_topup') ?>
                </div>

                <!-- 9. FINANCE ECONOMY -->
                <div x-show="activeTab === 'economy'" class="section-enter">
                    <?= view('admin/sections/finance_economy') ?>
                </div>

                <!-- 10. CMS LANDING -->
                <div x-show="activeTab === 'cms_landing'" class="section-enter">
                    <?= view('admin/sections/cms_landing') ?>
                </div>

                <!-- 11. CMS FAQ -->
                <div x-show="activeTab === 'cms_faq'" class="section-enter">
                    <?= view('admin/sections/cms_faq') ?>
                </div>

                <!-- 12. CMS PAGES -->
                <div x-show="activeTab === 'cms_pages'" class="section-enter">
                    <?= view('admin/sections/cms_pages') ?>
                </div>

                <!-- 13. SETTINGS NOTIFICATIONS -->
                <div x-show="activeTab === 'notifications'" class="section-enter">
                    <?= view('admin/sections/settings_notifications') ?>
                </div>

                <!-- 14. SETTINGS AUDIT -->
                <div x-show="activeTab === 'audit'" class="section-enter">
                    <?= view('admin/sections/settings_audit') ?>
                </div>

                <!-- 15. SETTINGS SYSTEM -->
                <div x-show="activeTab === 'system'" class="section-enter">
                    <?= view('admin/sections/settings_system') ?>
                </div>

            </div>
            <!-- /.app-content -->

        </main>

    </div>
    <!-- /.app-wrapper -->

    <!-- ══════════════════════════════════════
         SCRIPTS
    ═══════════════════════════════════════ -->
    <!-- Bootstrap 5 Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AdminLTE 4 -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4/dist/js/adminlte.min.js"></script>

    <!-- GLOBAL ALPINE.JS DATA STORE -->
    <script>
        function adminPanelStore() {
            return {
                activeTab: 'dashboard',

                setTab(tab) {
                    this.activeTab = tab;
                    // Close mobile sidebar on tab change
                    if (window.innerWidth < 992) {
                        document.body.classList.add('sidebar-collapse');
                    }
                },

                // USER DATA
                users: [
                    { id: 1, username: 'ahmad_kreatif', email: 'ahmad@nusa.com', status: 'Active', cc_balance: 1520, created_at: '2026-01-10' },
                    { id: 2, username: 'siti_baca', email: 'siti@gmail.com', status: 'Active', cc_balance: 340, created_at: '2026-02-14' },
                    { id: 3, username: 'budi_dunia', email: 'budi_sud@yahoo.com', status: 'Suspended', cc_balance: 0, created_at: '2026-03-01' },
                    { id: 4, username: 'dewi_novel', email: 'dewi.lestari@gmail.com', status: 'Active', cc_balance: 8900, created_at: '2026-03-20' },
                    { id: 5, username: 'rizky_manga', email: 'rizky.r@outlook.com', status: 'Muted', cc_balance: 10, created_at: '2026-04-05' },
                    { id: 6, username: 'plagiat_killer', email: 'spammer@gmail.com', status: 'Banned', cc_balance: 0, created_at: '2026-05-01' }
                ],

                // CREATORS DATA
                creators: [
                    { id: 1, name: 'Budi Sudarsono', username: 'budisud', starsoul: 88, works_count: 5, status: 'Active', monetization: true, applied_at: '2026-03-02' },
                    { id: 2, name: 'Dewi Lestari', username: 'dewi_novel', starsoul: 96, works_count: 12, status: 'Active', monetization: true, applied_at: '2026-03-22' },
                    { id: 3, name: 'Rian Manga', username: 'rianmanga', starsoul: 45, works_count: 1, status: 'Pending', monetization: false, applied_at: '2026-05-18' },
                    { id: 4, name: 'Toni Illustrator', username: 'tonidesign', starsoul: 72, works_count: 3, status: 'Pending', monetization: false, applied_at: '2026-05-21' },
                    { id: 5, name: 'Hacker Soul', username: 'soulhack', starsoul: 15, works_count: 0, status: 'Suspended', monetization: false, applied_at: '2026-05-10' }
                ],

                // WORKS DATA
                works: [
                    { id: 1, title: 'Garuda Rise', creator: 'budisud', type: 'Manga', paywall: 'Premium', status: 'Published', chapters: 12, cover_url: 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=400' },
                    { id: 2, title: 'Cinta di Batas Senja', creator: 'dewi_novel', type: 'Novel', paywall: 'Free', status: 'Published', chapters: 34, cover_url: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400' },
                    { id: 3, title: 'The Lost Temple', creator: 'rianmanga', type: 'Illustrasi', paywall: 'Premium', status: 'Hidden', chapters: 1, cover_url: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=400' },
                    { id: 4, title: 'Sketsa Awan', creator: 'tonidesign', type: 'Illustrasi', paywall: 'Free', status: 'Published', chapters: 5, cover_url: 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?w=400' }
                ],

                // REPORTS DATA
                reports: [
                    { id: 101, reporter: 'siti_baca', target_type: 'Comment', target_title: 'Spamming link promosi judi', category: 'Spam', status: 'Pending', content_preview: 'Ayo main di situs xxx depo bonus 100%!', created_at: '2026-05-22 10:15' },
                    { id: 102, reporter: 'rizky_manga', target_type: 'Chapter', target_title: 'Garuda Rise - Bab 4', category: 'Plagiat', status: 'Pending', content_preview: 'Menjiplak keseluruhan alur manga One Piece Bab 100.', created_at: '2026-05-22 14:30' },
                    { id: 103, reporter: 'budi_dunia', target_type: 'Work', target_title: 'The Lost Temple - Asset 3', category: 'NSFW', status: 'Pending', content_preview: 'Mengandung gambar visual eksplisit vulgar.', created_at: '2026-05-23 08:00' },
                    { id: 104, reporter: 'ahmad_kreatif', target_type: 'Comment', target_title: 'Ujaran Kebencian SARA', category: 'NSFW', status: 'Resolved', content_preview: 'Komentar rasis yang menyinggung kelompok tertentu.', created_at: '2026-05-21 16:45' }
                ],

                // TOPUPS DATA
                topups: [
                    { id: 'TX-9021', user: 'siti_baca', amount_idr: 50000, amount_cc: 500, method: 'Bank Transfer BCA', status: 'Pending', receipt: 'https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?w=600', created_at: '2026-05-23 07:15' },
                    { id: 'TX-9022', user: 'budi_dunia', amount_idr: 100000, amount_cc: 1000, method: 'GoPay', status: 'Success', receipt: 'https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?w=600', created_at: '2026-05-22 19:40' },
                    { id: 'TX-9023', user: 'rizky_manga', amount_idr: 25000, amount_cc: 250, method: 'Bank Transfer Mandiri', status: 'Pending', receipt: 'https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?w=600', created_at: '2026-05-23 09:00' }
                ],

                // TRANSACTIONS LOG
                transactions: [
                    { id: 'TRX-5001', user: 'siti_baca', type: 'Unlock Chapter', work: 'Garuda Rise - Bab 5', amount_cc: 10, created_at: '2026-05-23 09:12' },
                    { id: 'TRX-5002', user: 'rizky_manga', type: 'Download PDF', work: 'Cinta di Batas Senja', amount_cc: 50, created_at: '2026-05-23 08:34' },
                    { id: 'TRX-5003', user: 'ahmad_kreatif', type: 'Support Creator', work: 'Support budisud', amount_cc: 100, created_at: '2026-05-23 07:50' },
                    { id: 'TRX-5004', user: 'dewi_novel', type: 'Unlock Chapter', work: 'Garuda Rise - Bab 6', amount_cc: 10, created_at: '2026-05-22 22:15' },
                    { id: 'TRX-5005', user: 'siti_baca', type: 'Unlock Chapter', work: 'The Lost Temple - Bab 1', amount_cc: 15, created_at: '2026-05-22 18:02' }
                ],

                // AUDIT LOGS
                auditLogs: [
                    { timestamp: '2026-05-23 09:30', admin: 'Zakhest Admin', action: 'Ban User', target: 'plagiat_killer', detail: 'Spamming NSFW links in public comments' },
                    { timestamp: '2026-05-23 08:15', admin: 'Zakhest Admin', action: 'Adjust CC', target: 'siti_baca (+500 CC)', detail: 'Manual adjustment: Payment gateway verification delay' },
                    { timestamp: '2026-05-23 07:00', admin: 'Zakhest Admin', action: 'Approve Creator', target: 'budisud', detail: 'Approved creator proposal with 5 works check' },
                    { timestamp: '2026-05-22 16:45', admin: 'Zakhest Admin', action: 'Freeze CC', target: 'System Economy', detail: 'Deactivated global CC lock due to maintenance' }
                ],

                // FAQ DATA
                faqCategories: [
                    { id: 1, name: 'Pembaca & Pembayaran' },
                    { id: 2, name: 'Kreator & Monetisasi' }
                ],
                faqs: [
                    { id: 1, category_id: 1, question: 'Bagaimana cara mengisi saldo CC?', answer: 'Anda dapat masuk ke halaman Top Up, memilih nominal, melakukan transfer bank, dan mengunggah bukti pembayaran untuk diverifikasi oleh admin.', open: false },
                    { id: 2, category_id: 1, question: 'Apakah CC yang sudah dibeli bisa di-refund?', answer: 'Sesuai dengan kebijakan layanan kami, koin CC yang sudah berhasil dibeli tidak dapat dikembalikan atau di-refund.', open: false },
                    { id: 3, category_id: 2, question: 'Bagaimana syarat mencairkan pendapatan?', answer: 'Kreator dapat melakukan pencairan saldo CC minimal 5000 CC (Setara Rp 50.000) setiap tanggal 25 tiap bulannya.', open: false }
                ],

                // ECONOMY SETTINGS
                economySettings: {
                    conversionRate: 100,
                    globalFreeze: false
                },

                // SYSTEM SETTINGS
                systemSettings: {
                    siteName: 'NusaShare',
                    siteLogo: '',
                    siteDescription: 'Gerbang Masa Depan Kreator Indonesia',
                    uploadNovelLimit: 10,
                    uploadMangaLimit: 50,
                    storageTierQuota: 1024,
                    maintenanceMode: false
                },

                // CMS HOME
                cmsHome: {
                    heroTitle: 'Masa depan karya kreatif Indonesia dimulai di sini.',
                    heroSubtitle: 'Bagikan karya. Bangun reputasi. Dapatkan apresiasi yang nyata dari komunitas yang menghargai proses.',
                    ctaText: 'Jelajahi Karya',
                    bannerUrl: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200'
                },

                // BROADCASTS
                broadcasts: [
                    { id: 1, title: 'Server Maintenance Notice', message: 'Kami akan melakukan pembaharuan server pada pukul 23.59 WIB selama 2 jam.', target: 'Semua User', date: '2026-05-20' },
                    { id: 2, title: 'Event Kreator Bulan Juni', message: 'Tulis ceritamu di bulan Juni dan menangkan total hadiah 50,000 CC.', target: 'Kreator', date: '2026-05-18' }
                ],

                // ── Computed ──
                getPendingReportsCount() {
                    return this.reports.filter(r => r.status === 'Pending').length;
                },
                getPendingTopupsCount() {
                    return this.topups.filter(t => t.status === 'Pending').length;
                },

                // ── Actions ──
                logAction(action, target, detail) {
                    this.auditLogs.unshift({
                        timestamp: new Date().toISOString().replace('T', ' ').substring(0, 16),
                        admin: '<?= esc($adminUsername ?? 'Admin') ?>',
                        action, target, detail
                    });
                },
                banUser(username) {
                    let user = this.users.find(u => u.username === username);
                    if (user) { user.status = 'Banned'; this.logAction('Ban User', username, 'Banned from administrator dashboard'); }
                },
                suspendUser(username) {
                    let user = this.users.find(u => u.username === username);
                    if (user) { user.status = 'Suspended'; this.logAction('Suspend User', username, 'Suspended from administrator dashboard'); }
                },
                muteUser(username) {
                    let user = this.users.find(u => u.username === username);
                    if (user) { user.status = 'Muted'; this.logAction('Mute User', username, 'Muted writing access'); }
                },
                activateUser(username) {
                    let user = this.users.find(u => u.username === username);
                    if (user) { user.status = 'Active'; this.logAction('Activate User', username, 'Restored status to active'); }
                },
                adjustUserCC(username, amount, reason) {
                    let user = this.users.find(u => u.username === username);
                    if (user) { user.cc_balance += parseInt(amount); this.logAction('Adjust CC', username + ' (' + amount + ' CC)', reason); }
                },
                approveCreator(username) {
                    let c = this.creators.find(c => c.username === username);
                    if (c) { c.status = 'Active'; c.monetization = true; this.logAction('Approve Creator', username, 'Approved creator account'); }
                },
                rejectCreator(username) {
                    let c = this.creators.find(c => c.username === username);
                    if (c) { c.status = 'Suspended'; c.monetization = false; this.logAction('Reject Creator', username, 'Rejected creator account'); }
                },
                freezeCreatorMonetization(username) {
                    let c = this.creators.find(c => c.username === username);
                    if (c) { c.monetization = false; this.logAction('Freeze Monetization', username, 'Deactivated monetization'); }
                },
                unfreezeCreatorMonetization(username) {
                    let c = this.creators.find(c => c.username === username);
                    if (c) { c.monetization = true; this.logAction('Unfreeze Monetization', username, 'Activated monetization'); }
                },
                verifyTopup(id) {
                    let topup = this.topups.find(t => t.id === id);
                    if (topup) {
                        topup.status = 'Success';
                        let user = this.users.find(u => u.username === topup.user);
                        if (user) user.cc_balance += topup.amount_cc;
                        this.logAction('Approve Topup', id, 'Verified deposit ' + topup.amount_cc + ' CC');
                    }
                },
                rejectTopup(id) {
                    let topup = this.topups.find(t => t.id === id);
                    if (topup) { topup.status = 'Failed'; this.logAction('Reject Topup', id, 'Rejected top-up request'); }
                },
                resolveReport(id) {
                    let r = this.reports.find(r => r.id === id);
                    if (r) { r.status = 'Resolved'; this.logAction('Resolve Report', 'Report ID: ' + id, 'Marked solved'); }
                },
                deleteWork(id) {
                    this.works = this.works.filter(w => w.id !== id);
                    this.logAction('Delete Content', 'Work ID: ' + id, 'Removed content due to violations');
                },
                toggleWorkPaywall(id) {
                    let w = this.works.find(w => w.id === id);
                    if (w) { w.paywall = (w.paywall === 'Premium') ? 'Free' : 'Premium'; this.logAction('Toggle Paywall', w.title, 'Updated paywall to ' + w.paywall); }
                },
                toggleWorkStatus(id) {
                    let w = this.works.find(w => w.id === id);
                    if (w) { w.status = (w.status === 'Published') ? 'Hidden' : 'Published'; this.logAction('Toggle Visibility', w.title, 'Updated to ' + w.status); }
                }
            };
        }
    </script>

</body>
</html>
