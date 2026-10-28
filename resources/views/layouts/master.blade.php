<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Inventory') — Inventory System</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" />
    <!-- DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-bs5/2.0.3/dataTables.bootstrap5.min.css" />
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css" />

    <style>
        /* ── Design tokens ─────────────────────────────────────────────── */
        :root {
            --sidebar-bg:        #111827;
            --sidebar-active-bg: #5c4420;
            --sidebar-hover-bg:  rgba(255,255,255,.07);
            --sidebar-text:      rgba(255,255,255,.72);
            --sidebar-active-text: #ffffff;
            --sidebar-width:     220px;
            --header-h:          56px;
            --main-bg:           #f3f4f6;
            --card-bg:           #ffffff;
            --border:            #e5e7eb;
            --text-primary:      #111827;
            --text-secondary:    #6b7280;
            --btn-dark:          #111827;
            --btn-dark-hover:    #1f2937;
            --green-badge-bg:    #d1fae5;
            --green-badge-text:  #065f46;
        }

        /* ── Reset & base ──────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
               font-size: .875rem; background: var(--main-bg); color: var(--text-primary); overflow-x: hidden; }

        /* ── App shell ─────────────────────────────────────────────────── */
        .app-shell { display: flex; min-height: 100vh; }

        /* ── Sidebar ───────────────────────────────────────────────────── */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar-logo {
            display: flex; align-items: center; gap: .6rem;
            padding: 1rem 1.1rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-logo .logo-badge {
            width: 34px; height: 34px; border-radius: 8px;
            background: var(--sidebar-active-bg);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .8rem; color: #fff; flex-shrink: 0;
        }
        .sidebar-logo .logo-text { color: #fff; font-weight: 600; font-size: .9rem; letter-spacing: .02em; }

        .sidebar-nav { padding: .5rem 0; flex: 1; }
        .sidebar-nav .nav-item { list-style: none; }
        .sidebar-nav .nav-link {
            display: flex; align-items: center; justify-content: space-between;
            padding: .52rem 1.2rem;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: .84rem;
            transition: background .15s, color .15s;
            border-radius: 0;
            cursor: pointer;
        }
        .sidebar-nav .nav-link:hover {
            background: var(--sidebar-hover-bg);
            color: #fff;
        }
        .sidebar-nav .nav-link.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-text);
            font-weight: 500;
        }
        /* Parent active (has sub-items open) */
        .sidebar-nav .nav-link.parent-active {
            background: var(--sidebar-active-bg);
            color: #fff;
        }
        .sidebar-nav .nav-link .nav-arrow { font-size: .7rem; transition: transform .2s; }
        .sidebar-nav .nav-link.parent-active .nav-arrow { transform: rotate(90deg); }
        /* Sub-items */
        .sidebar-sub { list-style: none; padding: 0; margin: 0; background: rgba(0,0,0,.18); }
        .sidebar-sub .nav-link { padding-left: 2rem; font-size: .82rem; color: rgba(255,255,255,.6); }
        .sidebar-sub .nav-link:hover { color: #fff; background: rgba(255,255,255,.05); }
        .sidebar-sub .nav-link.active { color: #fff; background: rgba(255,255,255,.1); font-weight: 500; }

        /* ── Main content ──────────────────────────────────────────────── */
        .main-wrap {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── Top header ────────────────────────────────────────────────── */
        .top-header {
            height: var(--header-h);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky; top: 0; z-index: 900;
        }
        .user-badge {
            display: flex; align-items: center; gap: .5rem;
            cursor: pointer; font-size: .85rem; color: var(--text-primary);
        }
        .user-badge .avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--btn-dark);
            color: #fff; font-weight: 600; font-size: .8rem;
            display: flex; align-items: center; justify-content: center;
        }

        /* ── Page body ─────────────────────────────────────────────────── */
        .page-body { padding: 1.75rem; flex: 1; }
        .page-title { font-size: 1.6rem; font-weight: 700; color: var(--text-primary); margin-bottom: .2rem; }
        .page-subtitle { color: var(--text-secondary); font-size: .82rem; margin-bottom: 1.5rem; }

        /* ── Card ──────────────────────────────────────────────────────── */
        .inv-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; }
        .inv-card-body { padding: 1.25rem 1.5rem; }

        /* ── Buttons ───────────────────────────────────────────────────── */
        .btn-dark-inv {
            background: var(--btn-dark); color: #fff; border: none;
            padding: .45rem 1rem; border-radius: 6px; font-size: .84rem; font-weight: 500;
            cursor: pointer; transition: background .15s;
            display: inline-flex; align-items: center; gap: .3rem;
        }
        .btn-dark-inv:hover { background: var(--btn-dark-hover); color: #fff; }
        .btn-outline-inv {
            background: #fff; color: var(--text-primary); border: 1px solid var(--border);
            padding: .45rem 1rem; border-radius: 6px; font-size: .84rem; font-weight: 500;
            cursor: pointer; transition: background .15s;
        }
        .btn-outline-inv:hover { background: var(--main-bg); }
        .btn-icon-inv {
            width: 36px; height: 36px; border-radius: 6px;
            background: var(--btn-dark); color: #fff; border: none;
            display: inline-flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 1rem;
        }
        .btn-icon-inv:hover { background: var(--btn-dark-hover); }

        /* ── Search toolbar ────────────────────────────────────────────── */
        .table-toolbar { display: flex; align-items: center; justify-content: flex-end; gap: .5rem; margin-bottom: .75rem; }
        .table-search-wrap { display: flex; }
        .table-search-wrap input {
            height: 36px; border: 1px solid var(--border); border-right: none;
            border-radius: 6px 0 0 6px; padding: 0 .75rem; font-size: .83rem;
            outline: none; background: #fff; color: var(--text-primary); width: 220px;
        }
        .table-search-wrap input:focus { border-color: #9ca3af; }
        .table-search-wrap button {
            height: 36px; width: 36px; background: var(--btn-dark); color: #fff;
            border: none; border-radius: 0 6px 6px 0; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
        }
        .table-search-wrap button:hover { background: var(--btn-dark-hover); }

        /* ── DataTable overrides ───────────────────────────────────────── */
        table.dataTable { border-collapse: collapse !important; width: 100% !important; }
        table.dataTable thead tr th {
            background: #fff !important;
            color: var(--text-secondary) !important;
            font-weight: 600 !important;
            font-size: .78rem !important;
            text-transform: uppercase !important;
            letter-spacing: .04em !important;
            border-bottom: 2px solid var(--border) !important;
            padding: .65rem 1rem !important;
            white-space: nowrap;
        }
        table.dataTable tbody tr td {
            border-bottom: 1px solid var(--border) !important;
            padding: .75rem 1rem !important;
            font-size: .84rem !important;
            color: var(--text-primary) !important;
            vertical-align: middle !important;
        }
        table.dataTable tbody tr:hover td { background: #f9fafb !important; }
        table.dataTable tbody tr:last-child td { border-bottom: none !important; }
        /* Hide DT default filter/info — length is shown via dt-show-entries */
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info { display: none !important; }

        /* ── dt-footer: show-entries LEFT, pagination RIGHT ───────────── */
        .dt-footer { display: flex; align-items: center; justify-content: space-between; padding: 1rem 0 .25rem; flex-wrap: wrap; gap: .5rem; border-top: 1px solid var(--border); margin-top: .5rem; }

        /* Length control — force inline layout, override BS5 form-select block */
        .dataTables_length,
        .dt-length { display: flex; align-items: center; }
        .dataTables_length label,
        .dt-length label {
            display: flex !important; align-items: center; gap: .5rem;
            font-size: .82rem; color: var(--text-secondary); margin: 0; font-weight: 400;
            white-space: nowrap;
        }
        .dataTables_length select,
        .dt-length select {
            display: inline-block !important;
            width: auto !important;
            min-width: 72px;
            height: 34px;
            border: 1px solid var(--border) !important;
            border-radius: 6px !important;
            padding: 0 2rem 0 .65rem !important;
            font-size: .82rem !important;
            background: #fff !important;
            color: var(--text-primary) !important;
            box-shadow: none !important;
            cursor: pointer;
            appearance: auto !important;
            -webkit-appearance: auto !important;
        }
        .dataTables_length select:focus,
        .dt-length select:focus { border-color: #9ca3af !important; outline: none; }

        /* Pagination — Bootstrap 5 DT renders as <ul class="pagination"> */
        .dataTables_paginate { display: flex; align-items: center; }
        .dataTables_paginate .pagination { display: flex; align-items: center; gap: .15rem; margin: 0; padding: 0; list-style: none; }
        .dataTables_paginate .page-item .page-link {
            padding: .28rem .62rem; border-radius: 5px !important; font-size: .82rem;
            color: var(--text-secondary) !important; font-weight: 500;
            border: none !important; background: transparent !important;
            box-shadow: none !important; transition: background .12s, color .12s;
            line-height: 1.4;
        }
        .dataTables_paginate .page-item .page-link:hover {
            background: var(--border) !important; color: var(--text-primary) !important;
        }
        .dataTables_paginate .page-item.active .page-link,
        .dataTables_paginate .page-item.active .page-link:hover {
            background: var(--btn-dark) !important;
            color: #fff !important;
            border-color: var(--btn-dark) !important;
        }
        .dataTables_paginate .page-item.disabled .page-link,
        .dataTables_paginate .page-item.disabled .page-link:hover {
            opacity: .35; pointer-events: none;
            background: transparent !important; color: var(--text-secondary) !important;
        }
        /* Also cover non-BS5 paginate_button style for fallback */
        .dataTables_paginate .paginate_button {
            padding: .28rem .62rem; border-radius: 5px; font-size: .82rem;
            color: var(--text-secondary) !important; font-weight: 500;
            border: none !important; background: transparent !important; cursor: pointer;
        }
        .dataTables_paginate .paginate_button:hover { background: var(--border) !important; color: var(--text-primary) !important; }
        .dataTables_paginate .paginate_button.current,
        .dataTables_paginate .paginate_button.current:hover { background: var(--btn-dark) !important; color: #fff !important; }
        .dataTables_paginate .paginate_button.disabled { opacity: .35; cursor: default; background: transparent !important; }

        /* ── Badges ────────────────────────────────────────────────────── */
        .badge-active {
            background: var(--green-badge-bg); color: var(--green-badge-text);
            padding: .25rem .65rem; border-radius: 20px; font-size: .75rem; font-weight: 600;
        }
        .badge-inactive {
            background: #fee2e2; color: #991b1b;
            padding: .25rem .65rem; border-radius: 20px; font-size: .75rem; font-weight: 600;
        }

        /* ── Three-dot action ──────────────────────────────────────────── */
        .action-dots {
            background: none; border: none; color: var(--text-secondary);
            font-size: 1.1rem; letter-spacing: .1em; cursor: pointer;
            padding: .1rem .4rem; border-radius: 4px; line-height: 1;
        }
        .action-dots:hover { background: var(--main-bg); color: var(--text-primary); }
        .dropdown-menu { font-size: .84rem; border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0,0,0,.1); border-radius: 8px; }
        .dropdown-item { padding: .5rem 1rem; color: var(--text-primary); }
        .dropdown-item:hover { background: var(--main-bg); }
        .dropdown-item.text-danger:hover { background: #fef2f2; }

        /* ── Offcanvas panel ───────────────────────────────────────────── */
        .offcanvas.offcanvas-end { width: 480px; border-left: 1px solid var(--border); }
        .offcanvas-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
        .offcanvas-title { font-size: 1.1rem; font-weight: 700; color: var(--text-primary); }
        .offcanvas-body { padding: 1.5rem; }
        .offcanvas-footer { padding: 1rem 1.5rem; border-top: 1px solid var(--border); background: #fff;
            display: flex; align-items: center; justify-content: flex-end; gap: .75rem; }
        .form-label { font-size: .83rem; font-weight: 600; color: var(--text-primary); margin-bottom: .35rem; }
        .form-control, .form-select {
            border: 1px solid var(--border); border-radius: 6px;
            font-size: .84rem; padding: .5rem .75rem; color: var(--text-primary);
        }
        .form-control:focus, .form-select:focus {
            border-color: #9ca3af; box-shadow: none; outline: none;
        }
        .form-control::placeholder { color: #d1d5db; }
        .form-text { font-size: .75rem; color: var(--text-secondary); margin-top: .25rem; }

        /* ── Footer ────────────────────────────────────────────────────── */
        .app-footer { padding: 1rem 1.5rem; border-top: 1px solid var(--border);
            background: #fff; font-size: .78rem; color: var(--text-secondary); text-align: center; }

        /* ── Processing overlay ────────────────────────────────────────── */
        .dataTables_processing { background: rgba(255,255,255,.9) !important; border: none !important;
            box-shadow: 0 2px 8px rgba(0,0,0,.1); border-radius: 6px !important;
            font-size: .83rem !important; color: var(--text-secondary) !important; }
    </style>

    @stack('styles')
</head>
<body>
<div class="app-shell">

    {{-- Dark sidebar --}}
    @include('layouts.partials.sidebar')

    {{-- Main wrap --}}
    <div class="main-wrap">

        {{-- Top header --}}
        @include('layouts.partials.header')

        {{-- Page body --}}
        <main class="page-body">
            @yield('content')
        </main>

        @include('layouts.partials.footer')
    </div>
</div>

<!-- JS Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables.net/2.0.3/dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-bs5/2.0.3/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.12.0/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>

<script>
    // Attach CSRF token to every AJAX request
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    /**
     * Show a SweetAlert2 toast in the top-right corner.
     */
    function showToast(msg, type) {
        type = type || 'success';
        Swal.mixin({
            toast: true, position: 'top-end',
            showConfirmButton: false, timer: 3500, timerProgressBar: true,
        }).fire({ icon: type, title: msg });
    }

    /**
     * Handle the standard JSON response from all AJAX actions.
     * Calls onSuccess only when the response indicates success.
     */
    function handleResponse(response, onSuccess) {
        var isSuccess = response.msgtype === 'val_success'
            || (response.msgsuc && response.msgsuc.indexOf('success') !== -1);
        if (isSuccess) {
            showToast(response.msg || 'Done!', 'success');
            if (typeof onSuccess === 'function') onSuccess(response);
        } else {
            showToast(response.msg || 'An error occurred.', 'error');
        }
    }

    /**
     * Wire up the custom search toolbar to a DataTable instance.
     * @param {DataTable} table  - the DataTable instance
     * @param {string} searchId  - id of the custom search <input>
     * @param {string} btnId     - id of the search <button>
     * @param {string} reloadId  - id of the reload <button>
     * @param {string} showId    - id of the page-length <select>
     */
    // Logout confirmation
    document.addEventListener('DOMContentLoaded', function () {
        var logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function () {
                Swal.fire({
                    title: 'Logout?',
                    text: 'Are you sure you want to logout?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#111827',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Logout',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                }).then(function (result) {
                    if (result.isConfirmed) {
                        document.getElementById('logoutForm').submit();
                    }
                });
            });
        }
    });

    function wireToolbar(table, searchId, btnId, reloadId, showId) {
        // search button
        $('#' + btnId).on('click', function () { table.search($('#' + searchId).val()).draw(); });
        // search on Enter
        $('#' + searchId).on('keypress', function (e) {
            if (e.which === 13) table.search($(this).val()).draw();
        });
        // reload button
        $('#' + reloadId).on('click', function () { table.ajax.reload(); });
        // page-length select
        $('#' + showId).on('change', function () { table.page.len($(this).val()).draw(); });
    }
</script>

@stack('scripts')
</body>
</html>
