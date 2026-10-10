<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('skj_theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <title><?= esc($title ?? 'SKJ Live Chat') ?> | ระบบสนทนาสดโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</title>
    <link rel="icon" type="image/png" href="https://skj.ac.th/assets/img/logo/logo-skj.png">
    
    <!-- Google Fonts: K2D & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=K2D:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        :root {
            --skj-pink: #e91e63;
            --skj-pink-dark: #c2185b;
            --skj-pink-light: #fce4ec;
            --skj-blue: #1976d2;
            --skj-blue-dark: #0d47a1;
            --skj-blue-light: #e3f2fd;
            --sidebar-width: 260px;
            --topbar-height: 64px;
        }

        body {
            font-family: 'K2D', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f4f6fa;
            color: #334155;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            line-height: 1.65;
            -webkit-font-smoothing: antialiased;
        }

        /* Image & Proportions Standards */
        img {
            max-width: 100%;
            height: auto;
        }
        .avatar-img {
            object-fit: cover;
            aspect-ratio: 1 / 1;
            flex-shrink: 0;
        }
        .logo-img {
            object-fit: contain;
            aspect-ratio: 1 / 1;
            flex-shrink: 0;
        }

        /* Typography & Text Overflow Defense */
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .word-break-all {
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        /* Universal Button Design System & Micro-Interactions */
        .btn {
            font-family: inherit;
            font-weight: 500;
            line-height: 1.45;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 10px;
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            vertical-align: middle;
        }
        .btn:active {
            transform: scale(0.97);
        }
        .btn-primary {
            background-color: var(--skj-pink);
            border-color: var(--skj-pink);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(233, 30, 99, 0.2);
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--skj-pink-dark);
            border-color: var(--skj-pink-dark);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(233, 30, 99, 0.3);
        }
        .btn-sm {
            height: 34px;
            padding: 0 14px;
            font-size: 0.84rem;
            border-radius: 8px;
        }
        .btn-icon-sm {
            width: 34px;
            height: 34px;
            padding: 0;
            border-radius: 8px;
            flex-shrink: 0;
        }
        .btn-icon-md {
            width: 38px;
            height: 38px;
            padding: 0;
            border-radius: 10px;
            flex-shrink: 0;
        }
        .btn-icon-round {
            border-radius: 50% !important;
        }

        /* Form Controls Standard */
        .form-control, .form-select {
            font-family: inherit;
            font-size: 0.9rem;
            border-radius: 10px;
            border-color: #cbd5e1;
            line-height: 1.5;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--skj-pink);
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.12);
        }
        .form-control-sm, .form-select-sm {
            height: 34px;
            font-size: 0.82rem;
            border-radius: 8px;
        }

        /* Badge Standard */
        .badge {
            font-family: inherit;
            font-weight: 600;
            line-height: 1.35;
            padding: 4px 8px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Sidebar Backdrop on Mobile */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(3px);
            z-index: 999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }
        .sidebar-backdrop.show {
            opacity: 1;
            pointer-events: auto;
        }

        /* Sidebar Styling */
        .app-sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.02);
        }

        .sidebar-brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(135deg, #ffffff 0%, #fff5f8 100%);
            gap: 10px;
        }

        .sidebar-brand img {
            width: 38px;
            height: 38px;
            aspect-ratio: 1 / 1;
            object-fit: contain;
            flex-shrink: 0;
        }

        .sidebar-brand-text {
            line-height: 1.25;
            min-width: 0;
            flex: 1;
        }

        .sidebar-brand-text .title {
            font-weight: 700;
            font-size: 1.05rem;
            background: linear-gradient(135deg, var(--skj-pink) 0%, var(--skj-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-brand-text .subtitle {
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-menu {
            padding: 0.85rem 0.75rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .menu-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            padding: 0.5rem 0.75rem 0.25rem;
            margin-top: 0.4rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.62rem 0.85rem;
            color: #475569;
            text-decoration: none;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 500;
            margin-bottom: 0.2rem;
            transition: all 0.2s ease;
            gap: 10px;
        }

        .sidebar-link i {
            width: 22px;
            text-align: center;
            font-size: 1.05rem;
            color: #64748b;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .sidebar-link span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
            min-width: 0;
        }

        .sidebar-link:hover {
            color: var(--skj-pink);
            background: #fdf2f8;
        }

        .sidebar-link:hover i {
            color: var(--skj-pink);
        }

        .sidebar-link.active {
            color: #ffffff;
            background: linear-gradient(135deg, var(--skj-pink) 0%, #d81b60 100%);
            box-shadow: 0 4px 12px rgba(233, 30, 99, 0.25);
            font-weight: 600;
        }

        .sidebar-link.active i {
            color: #ffffff;
        }

        .sidebar-link .badge,
        .sidebar-unread-badge {
            margin-left: auto;
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            padding: 0 7px;
            font-family: 'Inter', sans-serif;
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 1;
            color: #ffffff;
            background: linear-gradient(135deg, #ff1744 0%, #e91e63 100%);
            border-radius: 999px;
            box-shadow: 0 3px 10px rgba(233, 30, 99, 0.45);
            flex-shrink: 0;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            animation: sidebarBadgePop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 1;
        }

        /* Pulsing Radar Ring on Notification */
        .sidebar-unread-badge::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            border-radius: 999px;
            background: linear-gradient(135deg, #ff1744, #e91e63);
            opacity: 0.6;
            z-index: -1;
            animation: sidebarBadgePing 2s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        /* High-contrast active link styling */
        .sidebar-link.active .sidebar-unread-badge {
            background: #ffffff;
            color: var(--skj-pink-dark);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.25);
        }

        .sidebar-link.active .sidebar-unread-badge::before {
            background: #ffffff;
            opacity: 0.45;
        }

        /* Micro-animations */
        @keyframes sidebarBadgePing {
            0% {
                transform: scale(0.92);
                opacity: 0.75;
            }
            70%, 100% {
                transform: scale(1.65);
                opacity: 0;
            }
        }

        @keyframes sidebarBadgePop {
            0% {
                transform: scale(0.4);
                opacity: 0;
            }
            70% {
                transform: scale(1.18);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .sidebar-badge-pop {
            animation: sidebarBadgePop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }

        /* Live status card in sidebar */
        .sidebar-live-card {
            background: linear-gradient(135deg, #fff5f8 0%, #f0f7ff 100%);
            border: 1px solid #fce4ec;
            border-radius: 12px;
            padding: 0.75rem 0.85rem;
            margin: 0.85rem 0.25rem 0.25rem;
            box-shadow: 0 2px 8px rgba(233, 30, 99, 0.04);
            transition: all 0.25s ease;
        }

        .sidebar-live-card:hover {
            border-color: #f8bbd0;
            box-shadow: 0 4px 12px rgba(233, 30, 99, 0.08);
        }

        .sidebar-live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: liveDotPulse 2s infinite;
        }

        @keyframes liveDotPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* Universal Loading State on Buttons */
        .btn-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.85;
            cursor: not-allowed !important;
        }

        .btn-loading .fa-spin,
        .btn-loading .spinner-border {
            margin-right: 4px;
        }

        /* Topbar Styling */
        .app-topbar {
            height: var(--topbar-height);
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            z-index: 999;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: left 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* Main Content Container */
        .app-main {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            min-height: calc(100vh - var(--topbar-height));
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* Agent Status Pill */
        .agent-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .agent-status-badge.online {
            background-color: #dcfce7;
            color: #15803d;
            border-color: #bbf7d0;
        }

        .agent-status-badge.busy {
            background-color: #fef3c7;
            color: #b45309;
            border-color: #fde68a;
        }

        .agent-status-badge.offline {
            background-color: #f1f5f9;
            color: #64748b;
            border-color: #e2e8f0;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .agent-status-badge.online .status-dot { background-color: #22c55e; }
        .agent-status-badge.busy .status-dot { background-color: #f59e0b; }
        .agent-status-badge.offline .status-dot { background-color: #94a3b8; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ==========================================================================
           DARK MODE DESIGN SYSTEM & TOKENS (โทนมืด)
           ========================================================================== */
        [data-bs-theme="dark"] {
            --skj-pink: #f06292;
            --skj-pink-dark: #ec407a;
            --skj-pink-light: rgba(240, 98, 146, 0.15);
            --skj-pink-subtle: rgba(233, 30, 99, 0.12);
            --skj-blue: #42a5f5;
            --skj-blue-dark: #1e88e5;
            --skj-blue-light: rgba(66, 165, 245, 0.15);
        }

        [data-bs-theme="dark"] body {
            background-color: #0b0f19 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .app-sidebar {
            background: #111827 !important;
            border-right-color: #1f2937 !important;
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.25) !important;
        }

        [data-bs-theme="dark"] .sidebar-brand {
            background: linear-gradient(135deg, #111827 0%, #1a1524 100%) !important;
            border-bottom-color: #1f2937 !important;
        }

        [data-bs-theme="dark"] .sidebar-brand-text .subtitle {
            color: #9ca3af !important;
        }

        [data-bs-theme="dark"] .menu-label {
            color: #6b7280 !important;
        }

        [data-bs-theme="dark"] .sidebar-link {
            color: #9ca3af !important;
        }

        [data-bs-theme="dark"] .sidebar-link i {
            color: #6b7280 !important;
        }

        [data-bs-theme="dark"] .sidebar-link:hover {
            color: #f472b6 !important;
            background: rgba(244, 114, 182, 0.12) !important;
        }

        [data-bs-theme="dark"] .sidebar-link:hover i {
            color: #f472b6 !important;
        }

        [data-bs-theme="dark"] .sidebar-link.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #e91e63 0%, #c2185b 100%) !important;
            box-shadow: 0 4px 14px rgba(233, 30, 99, 0.35) !important;
        }

        [data-bs-theme="dark"] .sidebar-live-card {
            background: linear-gradient(135deg, rgba(233, 30, 99, 0.08) 0%, rgba(25, 118, 210, 0.08) 100%) !important;
            border-color: #1f2937 !important;
        }

        [data-bs-theme="dark"] .app-topbar {
            background: #111827 !important;
            border-bottom-color: #1f2937 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important;
        }

        [data-bs-theme="dark"] .app-main {
            background-color: #0b0f19 !important;
        }

        [data-bs-theme="dark"] .card {
            background-color: #111827 !important;
            border-color: #1f2937 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .card-header,
        [data-bs-theme="dark"] .card-footer {
            background-color: #161f30 !important;
            border-color: #1f2937 !important;
        }

        [data-bs-theme="dark"] .table {
            color: #e2e8f0 !important;
            border-color: #1f2937 !important;
        }

        [data-bs-theme="dark"] .table-light {
            background-color: #161f30 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .table-striped > tbody > tr:nth-of-type(odd) > * {
            background-color: rgba(255, 255, 255, 0.02) !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .table-hover > tbody > tr:hover > * {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }

        [data-bs-theme="dark"] .modal-content {
            background-color: #111827 !important;
            border-color: #1f2937 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .modal-header,
        [data-bs-theme="dark"] .modal-footer {
            border-color: #1f2937 !important;
        }

        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #111827 !important;
            border-color: #1f2937 !important;
            color: #e2e8f0 !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4) !important;
        }

        [data-bs-theme="dark"] .dropdown-item {
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .dropdown-item:hover,
        [data-bs-theme="dark"] .dropdown-item:focus {
            background-color: #1f2937 !important;
            color: #ffffff !important;
        }

        [data-bs-theme="dark"] .dropdown-divider {
            border-color: #1f2937 !important;
        }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #0b0f19 !important;
            border-color: #1f2937 !important;
            color: #f1f5f9 !important;
        }

        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            border-color: var(--skj-pink) !important;
            background-color: #0b0f19 !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.25) !important;
        }

        [data-bs-theme="dark"] .input-group-text {
            background-color: #161f30 !important;
            border-color: #1f2937 !important;
            color: #9ca3af !important;
        }

        [data-bs-theme="dark"] .bg-light {
            background-color: #111827 !important;
        }

        [data-bs-theme="dark"] .bg-white {
            background-color: #111827 !important;
        }

        [data-bs-theme="dark"] .text-dark {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .text-secondary,
        [data-bs-theme="dark"] .text-muted {
            color: #9ca3af !important;
        }

        [data-bs-theme="dark"] .border,
        [data-bs-theme="dark"] .border-top,
        [data-bs-theme="dark"] .border-bottom,
        [data-bs-theme="dark"] .border-start,
        [data-bs-theme="dark"] .border-end {
            border-color: #1f2937 !important;
        }

        [data-bs-theme="dark"] .btn-light {
            background-color: #161f30 !important;
            border-color: #1f2937 !important;
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .btn-light:hover {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
            color: #ffffff !important;
        }

        [data-bs-theme="dark"] .list-group-item {
            background-color: #111827 !important;
            border-color: #1f2937 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .agent-status-badge.offline {
            background-color: #161f30 !important;
            border-color: #1f2937 !important;
            color: #9ca3af !important;
        }

        [data-bs-theme="dark"] .bg-primary-subtle {
            background-color: rgba(66, 165, 245, 0.16) !important;
            color: #90caf9 !important;
            border-color: rgba(66, 165, 245, 0.3) !important;
        }
        [data-bs-theme="dark"] .bg-success-subtle {
            background-color: rgba(34, 197, 94, 0.16) !important;
            color: #86efac !important;
            border-color: rgba(34, 197, 94, 0.3) !important;
        }
        [data-bs-theme="dark"] .bg-warning-subtle {
            background-color: rgba(245, 158, 11, 0.16) !important;
            color: #fde68a !important;
            border-color: rgba(245, 158, 11, 0.3) !important;
        }
        [data-bs-theme="dark"] .bg-danger-subtle {
            background-color: rgba(239, 68, 68, 0.16) !important;
            color: #fca5a5 !important;
            border-color: rgba(239, 68, 68, 0.3) !important;
        }
        [data-bs-theme="dark"] .bg-info-subtle {
            background-color: rgba(6, 182, 212, 0.16) !important;
            color: #a5f3fc !important;
            border-color: rgba(6, 182, 212, 0.3) !important;
        }
        [data-bs-theme="dark"] .bg-secondary-subtle {
            background-color: rgba(148, 163, 184, 0.16) !important;
            color: #cbd5e1 !important;
            border-color: rgba(148, 163, 184, 0.3) !important;
        }

        [data-bs-theme="dark"] .alert-warning {
            background-color: #271e11 !important;
            color: #fde68a !important;
            border-color: #533e1c !important;
        }
        [data-bs-theme="dark"] .alert-info {
            background-color: #0c2333 !important;
            color: #a5f3fc !important;
            border-color: #164e63 !important;
        }
        [data-bs-theme="dark"] .alert-secondary {
            background-color: #161f30 !important;
            color: #cbd5e1 !important;
            border-color: #1f2937 !important;
        }

        [data-bs-theme="dark"] .swal2-popup {
            background: #111827 !important;
            color: #e2e8f0 !important;
            border: 1px solid #1f2937 !important;
        }

        [data-bs-theme="dark"] .swal2-title {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .swal2-html-container {
            color: #9ca3af !important;
        }

        [data-bs-theme="dark"] ::-webkit-scrollbar-track {
            background: #0b0f19;
        }
        [data-bs-theme="dark"] ::-webkit-scrollbar-thumb {
            background: #1f2937;
        }
        [data-bs-theme="dark"] ::-webkit-scrollbar-thumb:hover {
            background: #374151;
        }

        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(-100%);
            }
            .app-sidebar.show {
                transform: translateX(0);
                box-shadow: 4px 0 25px rgba(0, 0, 0, 0.4);
            }
            .app-topbar, .app-main {
                left: 0;
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="app-sidebar" id="appSidebar">
        <div class="sidebar-brand">
            <img src="<?= base_url('public/assets/images/logo-skj.png') ?>" alt="SKJ Logo" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=SKJ&background=fce4ec&color=c2185b';">
            <div class="sidebar-brand-text">
                <div class="title">SKJ LiveChat</div>
                <div class="subtitle">Agent Desk & Bot Portal</div>
            </div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-label">สนทนาสด (Live Operations)</div>
            <a href="<?= base_url('chat/desk') ?>" class="sidebar-link <?= ($activeMenu === 'chat_desk') ? 'active' : '' ?>">
                <i class="fa-solid fa-headset"></i>
                <span>หน้าโต๊ะแชท (Chat Desk)</span>
                <span class="sidebar-unread-badge d-none" id="sidebarUnreadBadge">0</span>
            </a>
            <a href="<?= base_url('canned') ?>" class="sidebar-link <?= ($activeMenu === 'canned_replies') ? 'active' : '' ?>">
                <i class="fa-solid fa-bolt"></i>
                <span>ข้อความด่วน (Canned)</span>
            </a>

            <!-- Live Queue Status Mini Card -->
            <div class="sidebar-live-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="d-flex align-items-center gap-2" style="font-size: 0.72rem; font-weight: 600; color: #475569;">
                        <span class="sidebar-live-dot"></span> ระบบสนทนาสด
                    </span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.62rem; padding: 2px 6px;">พร้อมรับแชท</span>
                </div>
                <div class="d-flex align-items-center justify-content-between text-secondary mt-1" style="font-size: 0.76rem;">
                    <span>คิวรอตอบกลับ</span>
                    <span class="fw-bold text-dark font-inter" id="sidebarQueueWaitingCount">0 รายการ</span>
                </div>
            </div>

            <div class="menu-label">ปัญญาประดิษฐ์ & คลังข้อมูล</div>
            <a href="<?= base_url('knowledge') ?>" class="sidebar-link <?= ($activeMenu === 'knowledge') ? 'active' : '' ?>">
                <i class="fa-solid fa-brain"></i>
                <span>คลังความรู้ (Knowledge)</span>
            </a>
            <a href="<?= base_url('settings/ai') ?>" class="sidebar-link <?= ($activeMenu === 'ai_settings') ? 'active' : '' ?>">
                <i class="fa-solid fa-robot"></i>
                <span>ตั้งค่า AI (Gemini Bot)</span>
            </a>

            <div class="menu-label">ระบบ & จัดการทีม</div>
            <a href="<?= base_url('analytics') ?>" class="sidebar-link <?= ($activeMenu === 'analytics') ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-line"></i>
                <span>รายงาน & สถิติ</span>
            </a>
            <a href="<?= base_url('agents') ?>" class="sidebar-link <?= ($activeMenu === 'agents') ? 'active' : '' ?>">
                <i class="fa-solid fa-users-gear"></i>
                <span>จัดการเจ้าหน้าที่</span>
            </a>
            <a href="<?= base_url('settings/telegram') ?>" class="sidebar-link <?= ($activeMenu === 'telegram_settings') ? 'active' : '' ?>">
                <i class="fa-brands fa-telegram"></i>
                <span>แจ้งเตือน Telegram</span>
            </a>
            <a href="<?= base_url('settings/oauth') ?>" class="sidebar-link <?= ($activeMenu === 'settings') ? 'active' : '' ?>">
                <i class="fa-solid fa-sliders"></i>
                <span>ตั้งค่า Google OAuth</span>
            </a>
            <a href="<?= base_url('settings/embed') ?>" class="sidebar-link <?= ($activeMenu === 'embed') ? 'active' : '' ?>">
                <i class="fa-solid fa-code"></i>
                <span>โค้ดติดตั้ง Widget</span>
            </a>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-3 border-top bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center min-w-0" style="flex: 1; margin-right: 8px;">
                    <img src="<?= esc($currentAgent->avatar ?? ('https://ui-avatars.com/api/?name=' . urlencode($currentAgent->fullname ?? 'Agent') . '&background=fce4ec&color=c2185b')) ?>" class="rounded-circle me-2 flex-shrink-0 avatar-img" width="36" height="36" style="border: 1.5px solid #e2e8f0; object-fit: cover;" alt="Avatar" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?= urlencode(esc($currentAgent->fullname ?? 'Agent')) ?>&background=fce4ec&color=c2185b';">
                    <div style="line-height: 1.25; min-width: 0; flex: 1;">
                        <div class="fw-semibold text-truncate small" title="<?= esc($currentAgent->fullname ?? 'Agent') ?>"><?= esc($currentAgent->fullname ?? 'Agent') ?></div>
                        <span class="badge bg-secondary-subtle text-secondary text-uppercase fw-semibold" style="font-size: 0.65rem; padding: 2px 6px;"><?= esc($currentAgent->role ?? 'agent') ?></span>
                    </div>
                </div>
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-sm btn-outline-danger p-0 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; border-radius: 8px;" title="ออกจากระบบ">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebarMobile()"></div>

    <!-- Topbar -->
    <header class="app-topbar">
        <div class="d-flex align-items-center gap-2 gap-sm-3 min-w-0" style="flex: 1; max-width: calc(100% - 240px);">
            <button class="btn btn-light border d-lg-none flex-shrink-0 d-flex align-items-center justify-content-center" id="toggleSidebarBtn" style="width: 36px; height: 36px; border-radius: 10px;">
                <i class="fa-solid fa-bars"></i>
            </button>
            <h5 class="mb-0 fw-semibold text-secondary text-truncate" style="font-size: 0.98rem;">
                <?= esc($title ?? 'SKJ Live Chat') ?>
            </h5>
        </div>

        <div class="d-flex align-items-center gap-2 gap-sm-3 flex-shrink-0">
            <!-- Online status dropdown -->
            <div class="dropdown">
                <button class="agent-status-badge <?= esc($currentAgent->online_status ?? 'online') ?> dropdown-toggle" type="button" data-bs-toggle="dropdown" style="height: 34px;">
                    <span class="status-dot"></span>
                    <span id="currentStatusText">
                        <?= ($currentAgent->online_status ?? 'online') === 'online' ? 'พร้อมให้บริการ' : (($currentAgent->online_status ?? '') === 'busy' ? 'ไม่สะดวก' : 'ออฟไลน์') ?>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)" onclick="setOnlineStatus('online')"><span class="status-dot" style="background:#22c55e;"></span> พร้อมให้บริการ (Online)</a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)" onclick="setOnlineStatus('busy')"><span class="status-dot" style="background:#f59e0b;"></span> ไม่สะดวกรับแชท (Busy)</a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)" onclick="setOnlineStatus('offline')"><span class="status-dot" style="background:#94a3b8;"></span> ออฟไลน์ (Offline)</a></li>
                </ul>
            </div>

            <!-- Sound Notification Toggle -->
            <button class="btn btn-light btn-sm text-secondary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;" id="btnToggleSound" onclick="toggleDeskSound()" title="เปิด/ปิดเสียงแจ้งเตือนแชท">
                <i class="fa-solid fa-volume-high" id="soundIcon"></i>
            </button>

            <!-- Theme Toggle (Light / Dark Mode) -->
            <button class="btn btn-light btn-sm text-secondary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;" id="btnToggleTheme" onclick="toggleAppTheme()" title="สลับโหมดสี (สว่าง / มืด)">
                <i class="fa-solid fa-moon" id="themeIcon"></i>
            </button>

            <!-- External link to main site -->
            <a href="https://skj.ac.th" target="_blank" class="btn btn-light btn-sm text-secondary rounded-pill d-inline-flex align-items-center gap-1 px-2 px-sm-3 flex-shrink-0" style="height: 34px; font-size: 0.82rem;" title="เปิดหน้าเว็บโรงเรียน skj.ac.th">
                <i class="fa-solid fa-globe"></i>
                <span class="d-none d-md-inline">เว็บไซต์โรงเรียน</span>
            </a>
        </div>
    </header>

    <!-- Main App Body -->
    <main class="app-main">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Audio Beep for Incoming Messages -->
    <audio id="chatNotifyAudio" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
    </audio>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

    <script>
        const BASE_URL = '<?= rtrim(base_url(), '/') ?>';

        // Flash Messages
        <?php if (session()->getFlashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'ข้อผิดพลาด / ไม่มีสิทธิ์',
            text: '<?= esc(session()->getFlashdata('error')) ?>',
            confirmButtonColor: '#e91e63'
        });
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ',
            text: '<?= esc(session()->getFlashdata('success')) ?>',
            timer: 2500,
            showConfirmButton: false
        });
        <?php endif; ?>

        // Toggle mobile sidebar with backdrop
        function toggleSidebarMobile() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const isShow = sidebar.classList.toggle('show');
            backdrop?.classList.toggle('show', isShow);
        }
        function closeSidebarMobile() {
            document.getElementById('appSidebar')?.classList.remove('show');
            document.getElementById('sidebarBackdrop')?.classList.remove('show');
        }
        document.getElementById('toggleSidebarBtn')?.addEventListener('click', toggleSidebarMobile);

        // Online Status Updater
        function setOnlineStatus(status) {
            const formData = new FormData();
            formData.append('status', status);

            fetch(`${BASE_URL}/chat/online-status`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const badge = document.querySelector('.agent-status-badge');
                    badge.className = `agent-status-badge ${status} dropdown-toggle`;
                    document.getElementById('currentStatusText').innerText = 
                        status === 'online' ? 'พร้อมให้บริการ' : (status === 'busy' ? 'ไม่สะดวก' : 'ออฟไลน์');
                }
            });
        }

        // Sound Notification Manager (Web Audio API Synthesizer + Fallback)
        let deskSoundMuted = localStorage.getItem('skj_desk_sound_muted') === 'true';
        let audioDeskCtx = null;

        function updateSoundIcon() {
            const icon = document.getElementById('soundIcon');
            if (!icon) return;
            if (deskSoundMuted) {
                icon.className = 'fa-solid fa-volume-xmark text-danger';
                icon.title = 'เสียงแจ้งเตือน: ปิดอยู่ (คลิกเพื่อเปิด)';
            } else {
                icon.className = 'fa-solid fa-volume-high text-success';
                icon.title = 'เสียงแจ้งเตือน: เปิดอยู่ (คลิกเพื่อปิด)';
            }
        }

        function toggleDeskSound() {
            deskSoundMuted = !deskSoundMuted;
            localStorage.setItem('skj_desk_sound_muted', deskSoundMuted);
            updateSoundIcon();
            if (!deskSoundMuted) {
                playNotifySound(true); // Preview sound
            }
        }

        // Theme Switcher Manager (Light / Dark Mode)
        function updateThemeUI(theme) {
            const icon = document.getElementById('themeIcon');
            const btn = document.getElementById('btnToggleTheme');
            if (theme === 'dark') {
                if (icon) {
                    icon.className = 'fa-solid fa-sun text-warning';
                }
                if (btn) btn.title = 'เปลี่ยนเป็นโหมดสว่าง (Light Mode)';
            } else {
                if (icon) {
                    icon.className = 'fa-solid fa-moon text-secondary';
                }
                if (btn) btn.title = 'เปลี่ยนเป็นโหมดมืด (Dark Mode)';
            }
        }

        function toggleAppTheme() {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('skj_theme', newTheme);
            updateThemeUI(newTheme);
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateSoundIcon();
            const activeTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            updateThemeUI(activeTheme);
        });

        // Sound player helper
        function playNotifySound(force = false) {
            if (deskSoundMuted && !force) return;

            // Preferred: Web Audio API synthesized notification bell
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (AudioCtx) {
                    if (!audioDeskCtx) audioDeskCtx = new AudioCtx();
                    if (audioDeskCtx.state === 'suspended') audioDeskCtx.resume();

                    const now = audioDeskCtx.currentTime;

                    // Note 1: E5 (659.25 Hz)
                    const osc1 = audioDeskCtx.createOscillator();
                    const gain1 = audioDeskCtx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(659.25, now);
                    gain1.gain.setValueAtTime(0.12, now);
                    gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.32);
                    osc1.connect(gain1);
                    gain1.connect(audioDeskCtx.destination);
                    osc1.start(now);
                    osc1.stop(now + 0.32);

                    // Note 2: A5 (880.00 Hz)
                    const osc2 = audioDeskCtx.createOscillator();
                    const gain2 = audioDeskCtx.createGain();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(880.00, now + 0.12);
                    gain2.gain.setValueAtTime(0.14, now + 0.12);
                    gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
                    osc2.connect(gain2);
                    gain2.connect(audioDeskCtx.destination);
                    osc2.start(now + 0.12);
                    osc2.stop(now + 0.55);
                    return;
                }
            } catch (err) {}

            // Fallback: Audio Element
            try {
                const audio = document.getElementById('chatNotifyAudio');
                if (audio) {
                    audio.currentTime = 0;
                    audio.play().catch(e => console.log('Audio autoplay prevented:', e));
                }
            } catch (err) {
                console.error(err);
            }
        }

        // Global real-time sidebar notification updater for all admin pages
        (function() {
            let lastUnreadCount = 0;
            const isChatDesk = window.location.pathname.includes('/chat/desk') || window.location.pathname.endsWith('/chat');

            function syncSidebarBadges(stats) {
                if (!stats) return;
                const unread = parseInt(stats.unassigned || 0, 10);
                const sidebarBadge = document.getElementById('sidebarUnreadBadge');
                const queueCardCount = document.getElementById('sidebarQueueWaitingCount');

                if (sidebarBadge) {
                    const prevCount = parseInt(sidebarBadge.innerText, 10) || 0;
                    sidebarBadge.innerText = unread > 99 ? '99+' : unread;
                    sidebarBadge.classList.toggle('d-none', unread === 0);

                    if (unread > 0 && unread !== prevCount) {
                        sidebarBadge.classList.remove('sidebar-badge-pop');
                        void sidebarBadge.offsetWidth; // trigger reflow
                        sidebarBadge.classList.add('sidebar-badge-pop');
                    }
                }

                if (queueCardCount) {
                    queueCardCount.innerText = `${unread} รายการ`;
                    if (unread > 0) {
                        queueCardCount.className = 'fw-bold text-danger font-inter';
                    } else {
                        queueCardCount.className = 'fw-bold text-dark font-inter';
                    }
                }

                // If on other pages and a brand new unread queue arrives, trigger notification sound
                if (!isChatDesk && unread > lastUnreadCount && lastUnreadCount !== 0) {
                    playNotifySound();
                }
                lastUnreadCount = unread;
            }

            // Expose globally so chat desk can also call it
            window.syncSidebarBadges = syncSidebarBadges;

            // Run periodic background sync when on other admin pages
            if (!isChatDesk) {
                function pollSidebarStatus() {
                    fetch(`${BASE_URL}/chat/queue?filter=active`)
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.stats) {
                                syncSidebarBadges(data.stats);
                            }
                        })
                        .catch(() => {});
                }

                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(pollSidebarStatus, 1000);
                    setInterval(pollSidebarStatus, 12000);
                });
            }
        })();

        // Universal Button Loading Helper
        window.setButtonLoading = function(btn, isLoading = true, loadingText = null) {
            if (!btn) return;
            if (typeof btn === 'string') btn = document.querySelector(btn);
            if (!btn) return;

            if (isLoading) {
                if (!btn.dataset.originalHtml) {
                    btn.dataset.originalHtml = btn.innerHTML;
                }
                btn.disabled = true;
                btn.classList.add('btn-loading');
                const text = loadingText !== null ? `<span class="ms-1">${loadingText}</span>` : '';
                btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i>${text}`;
            } else {
                btn.disabled = false;
                btn.classList.remove('btn-loading');
                if (btn.dataset.originalHtml) {
                    btn.innerHTML = btn.dataset.originalHtml;
                    delete btn.dataset.originalHtml;
                }
            }
        };

        // Auto-attach loading state to forms on submit
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.getAttribute('data-no-auto-loading') === 'true') return;
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                setTimeout(() => {
                    if (!e.defaultPrevented) {
                        window.setButtonLoading(submitBtn, true, 'กำลังบันทึก...');
                    }
                }, 10);
            }
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
