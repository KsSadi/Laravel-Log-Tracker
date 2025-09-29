<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Log Tracker')</title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --info-color: #0284c7;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --border-radius: 8px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --transition: all 0.2s ease-in-out;
        }

        body {
            background-color: var(--gray-50);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            color: var(--gray-900);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Clean Navigation */
        .navbar {
            background-color: white;
            border-bottom: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            padding: 1rem 0;
        }

        .navbar-brand {
            color: var(--gray-900) !important;
            font-weight: 600;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .brand-icon {
            width: 32px;
            height: 32px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .navbar-nav .nav-link {
            color: var(--gray-600) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            margin: 0 0.25rem;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
            background-color: var(--gray-100);
        }

        .navbar-nav .nav-link.active {
            color: var(--primary-color) !important;
            background-color: #dbeafe;
        }

        .navbar-toggler {
            border: 1px solid var(--gray-300);
            padding: 0.25rem 0.5rem;
        }

        /* Enhanced Page Header */
        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            padding: 3rem 2.5rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--info-color) 50%, var(--success-color) 100%);
        }

        .page-header h1 {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0.75rem;
            letter-spacing: -0.025em;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-header h1 i {
            color: var(--primary-color);
            background: rgba(37, 99, 235, 0.1);
            padding: 0.75rem;
            border-radius: 12px;
            font-size: 1.5rem;
        }

        .page-header p {
            color: var(--gray-600);
            margin-bottom: 0;
            font-size: 1.125rem;
            line-height: 1.7;
            font-weight: 400;
        }

        .header-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 2.5rem;
        }

        .header-stat {
            background: white;
            border: 1px solid var(--gray-200);
            padding: 1.75rem 2rem;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .header-stat::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-color);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .header-stat:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .header-stat:hover::before {
            transform: scaleX(1);
        }

        .header-stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
            display: block;
        }

        .header-stat-label {
            font-size: 0.875rem;
            color: var(--gray-600);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Revolutionary Stats Cards */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            padding: 0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-8px);
        }

        .stat-card-body {
            padding: 2.5rem;
            position: relative;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }

        .stat-info {
            flex: 1;
        }

        .stat-info h6 {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--gray-500);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stat-info h6::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .stat-info .stat-number {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 1rem;
            line-height: 1;
            letter-spacing: -0.05em;
            position: relative;
        }

        .stat-progress {
            width: 100%;
            height: 4px;
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 2px;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .stat-progress-bar {
            height: 100%;
            border-radius: 2px;
            transition: width 2s ease-in-out;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-change {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: white;
            box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
            position: relative;
        }

        .stat-icon::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 18px;
            padding: 2px;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: subtract;
        }

        /* Error Card Styling */
        .error-card::before {
            background: linear-gradient(90deg, #dc2626 0%, #ef4444 100%);
        }
        .error-card .stat-info h6::before { background-color: #dc2626; }
        .error-card .stat-number {
            color: #dc2626;
            text-shadow: 0 0 20px rgba(220, 38, 38, 0.2);
        }
        .error-card .stat-icon {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }
        .error-card .stat-trend {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-color: #fecaca;
            color: #dc2626;
        }
        .error-card .stat-progress-bar {
            background: linear-gradient(90deg, #dc2626 0%, #ef4444 100%);
        }
        .error-card .stat-change {
            background-color: rgba(220, 38, 38, 0.1);
            color: #dc2626;
        }

        /* Warning Card Styling */
        .warning-card::before {
            background: linear-gradient(90deg, #d97706 0%, #f59e0b 100%);
        }
        .warning-card .stat-info h6::before { background-color: #d97706; }
        .warning-card .stat-number {
            color: #d97706;
            text-shadow: 0 0 20px rgba(217, 119, 6, 0.2);
        }
        .warning-card .stat-icon {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        }
        .warning-card .stat-trend {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border-color: #fed7aa;
            color: #d97706;
        }
        .warning-card .stat-progress-bar {
            background: linear-gradient(90deg, #d97706 0%, #f59e0b 100%);
        }
        .warning-card .stat-change {
            background-color: rgba(217, 119, 6, 0.1);
            color: #d97706;
        }

        /* Info Card Styling */
        .info-card::before {
            background: linear-gradient(90deg, #0284c7 0%, #0ea5e9 100%);
        }
        .info-card .stat-info h6::before { background-color: #0284c7; }
        .info-card .stat-number {
            color: #0284c7;
            text-shadow: 0 0 20px rgba(2, 132, 199, 0.2);
        }
        .info-card .stat-icon {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        }
        .info-card .stat-trend {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-color: #bae6fd;
            color: #0284c7;
        }
        .info-card .stat-progress-bar {
            background: linear-gradient(90deg, #0284c7 0%, #0ea5e9 100%);
        }
        .info-card .stat-change {
            background-color: rgba(2, 132, 199, 0.1);
            color: #0284c7;
        }

        /* Success Card Styling */
        .success-card::before {
            background: linear-gradient(90deg, #059669 0%, #10b981 100%);
        }
        .success-card .stat-info h6::before { background-color: #059669; }
        .success-card .stat-number {
            color: #059669;
            text-shadow: 0 0 20px rgba(5, 150, 105, 0.2);
        }
        .success-card .stat-icon {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }
        .success-card .stat-trend {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-color: #bbf7d0;
            color: #059669;
        }
        .success-card .stat-progress-bar {
            background: linear-gradient(90deg, #059669 0%, #10b981 100%);
        }
        .success-card .stat-change {
            background-color: rgba(5, 150, 105, 0.1);
            color: #059669;
        }

        /* Enhanced Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background-color: white;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .chart-card:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-2px);
        }

        .chart-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chart-title i {
            color: var(--primary-color);
        }

        .chart-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.025em;
        }

        .chart-body {
            padding: 2rem;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* Status Indicator */
        .live-status {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: var(--success-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: var(--shadow);
            z-index: 1000;
            transition: var(--transition);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: currentColor;
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Enhanced Analysis Section */
        .analysis-section {
            background-color: white;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .analysis-section:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .analysis-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            color: var(--gray-900);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .analysis-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .analysis-title i {
            color: var(--primary-color);
        }

        .live-badge {
            background-color: var(--success-color);
            color: white;
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            letter-spacing: 0.025em;
        }

        .analysis-body {
            padding: 2rem;
        }

        .analysis-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .analysis-card {
            border: 1px solid var(--gray-200);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: var(--transition);
        }

        .analysis-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.15);
        }

        .analysis-card-header {
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .analysis-card-header.error-header {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: var(--danger-color);
            border-bottom: 1px solid #fecaca;
        }

        .analysis-card-header.warning-header {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            color: var(--warning-color);
            border-bottom: 1px solid #fed7aa;
        }

        .analysis-table {
            margin: 0;
        }

        .analysis-table tbody tr {
            border-bottom: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .analysis-table tbody tr:hover {
            background-color: var(--gray-50);
        }

        .analysis-table tbody tr:last-child {
            border-bottom: none;
        }

        .analysis-table td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }

        .error-type {
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
            font-size: 0.875rem;
            color: var(--gray-700);
            font-weight: 500;
        }

        .error-count-badge {
            background-color: var(--danger-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .peak-count-badge {
            background-color: var(--warning-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .time-range {
            color: var(--gray-700);
            font-weight: 500;
        }

        /* Enhanced Recent Logs Section */
        .recent-logs-section {
            background-color: white;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .recent-logs-section:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .logs-section-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logs-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logs-section-title i {
            color: var(--primary-color);
        }

        .logs-actions {
            display: flex;
            gap: 0.75rem;
        }

        .action-btn {
            background-color: white;
            border: 1px solid var(--gray-300);
            color: var(--gray-700);
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s ease-in-out;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            letter-spacing: 0.025em;
        }

        .action-btn.primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 1px 3px 0 rgba(37, 99, 235, 0.2);
        }

        .action-btn.primary:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px 0 rgba(37, 99, 235, 0.3);
        }

        .action-btn.success {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
            box-shadow: 0 1px 3px 0 rgba(5, 150, 105, 0.2);
        }

        .action-btn.success:hover {
            background-color: #047857;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px 0 rgba(5, 150, 105, 0.3);
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.15);
        }

        .logs-table {
            width: 100%;
            margin: 0;
        }

        .logs-table thead th {
            background-color: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            padding: 0.75rem 1rem;
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .logs-table tbody tr {
            border-bottom: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .logs-table tbody tr:hover {
            background-color: var(--gray-50);
        }

        .logs-table tbody tr:last-child {
            border-bottom: none;
        }

        .logs-table tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }

        .log-timestamp {
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
            font-size: 0.875rem;
            color: var(--gray-500);
            font-weight: 500;
        }

        .log-message {
            color: var(--gray-700);
            font-size: 0.875rem;
            line-height: 1.5;
            word-break: break-word;
        }

        .log-level-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--gray-400);
        }

        .empty-state h4 {
            color: var(--gray-700);
            margin-bottom: 0.25rem;
            font-size: 1rem;
            font-weight: 600;
        }

        .empty-state p {
            font-size: 0.875rem;
            margin: 0;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: currentColor;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Footer - Matching GlowStack Design */
        .footer {
            background: #2d3748;
            color: white;
            margin-top: 4rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .footer-content {
            padding: 1.5rem 0;
            text-align: center;
        }

        .footer-brand {
            color: #4facfe;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .footer-link {
            color: #4facfe;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-link:hover {
            color: #00f2fe;
            text-decoration: none;
        }

        .footer-text {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            margin: 0;
        }

        .footer-tagline {
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
            margin: 0;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .charts-section {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem;
            }

            .header-stats {
                flex-direction: column;
                gap: 1rem;
            }

            .stats-overview {
                grid-template-columns: 1fr;
            }

            .analysis-grid {
                grid-template-columns: 1fr;
            }

            .logs-section-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .logs-actions {
                justify-content: center;
            }

            .chart-body,
            .analysis-body {
                padding: 1rem;
            }
        }

        /* Smooth transitions */
        .page-content {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar:horizontal {
            display: none;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }

        .coffee-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
            margin-top: 0.75rem;
        }

        .coffee-button:hover {
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
        }

        .coffee-button i {
            font-size: 1rem;
        }
    </style>
    @stack('styles')
</head>
<body>
<!-- Clean Navigation -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ route('log-tracker.dashboard') }}">
            <div class="brand-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <span>Log Tracker</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('log-tracker.dashboard') ? 'active' : '' }}"
                       href="{{ route('log-tracker.dashboard') }}">
                        <i class="fas fa-chart-line"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('log-tracker.index') ? 'active' : '' }}"
                       href="{{ route('log-tracker.index') }}">
                        <i class="fas fa-list"></i>
                        Log Files
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('log-tracker.export.form') ? 'active' : '' }}"
                       href="{{ route('log-tracker.export.form') }}">
                        <i class="fas fa-download"></i>
                        Export Logs
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

@yield('content')


<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-brand">
                <i class="fa-brands fa-searchengin"></i> LogTracker <v2 class="3">v2.3</v2>
            </div>
            <p class="footer-text">
                © <span id="year"></span>
                <a href="https://github.com/KsSadi/Laravel-Log-Tracker" class="footer-link" target="_blank">
                    LogTracker
                </a>
                Efficient logging, effortless insights.
            </p>
            <a href="https://www.buymeacoffee.com/kssadi" class="coffee-button" target="_blank">
                <i class="fas fa-coffee"></i>
                <span>Buy me a coffee</span>
            </a>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

@stack('scripts')
</body>
</html>
