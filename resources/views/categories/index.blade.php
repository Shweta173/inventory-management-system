@extends('layouts.master')

@section('title', $title)

@section('content')

{{-- Page header --}}
<div class="d-flex align-items-start justify-content-between mb-3">
    <div>
        <h1 class="page-title mb-0">{{ $title }}</h1>
        <p class="page-subtitle mb-0">Manage material categories used across the inventory.</p>
    </div>
    <button type="button" class="btn-dark-inv" id="add_category_btn">
        <i class="bi bi-plus-lg"></i> Add Category
    </button>
</div>

<div class="inv-card">
    <div class="inv-card-body">

        <div class="table-toolbar">
            <div class="table-search-wrap">
                <input id="cat_search" type="text" placeholder="Search categories…" autocomplete="off" />
                <button id="cat_search_btn" title="Search"><i class="bi bi-search"></i></button>
            </div>
            <button id="reload_cat" class="btn-icon-inv" title="Reload">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>

        {{-- Table — DT wraps this and appends dt-footer below via dom string --}}
        <table id="categories_table" class="table w-100">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Category Name</th>
                    <th>Created At</th>
                    <th style="width:80px">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    </div>
</div>

{{-- Right-side offcanvas --}}
@include('categories.modal')
@endsection


@push('scripts')
<script>
var list_route = '{{ $list_route }}';
var save_route = '{{ $save_route }}';
var edit_route = '{{ $edit_route }}';
var del_route  = '{{ $del_route }}';

$(function () {

    var offcanvas = new bootstrap.Offcanvas(document.getElementById('categoryOffcanvas'));

    var table = $('#categories_table').DataTable({
        processing : true,
        serverSide : true,
        autoWidth  : false,
        ajax       : { url: list_route, type: 'POST' },
        columns    : [
            { data: 0, name: 'id',         width: '5%' },
            { data: 1, name: 'name',       width: '60%' },
            { data: 2, name: 'created_at', width: '22%', orderable: false },
            { data: 3, name: 'action',     width: '8%',  orderable: false, searchable: false },
        ],
        order      : [[0, 'desc']],
        pageLength : 10,
        dom        : "<'table-responsive'tr><'dt-footer'<'dt-show-entries'l>p>",
        pagingType : 'full_numbers',
        lengthMenu : [10, 25, 50, 100],
        language   : {
            processing: '<div class="spinner-border spinner-border-sm text-secondary" role="status"></div>',
            paginate  : { first: 'First', previous: 'Prev', next: 'Next', last: 'Last' },
            lengthMenu: 'Show _MENU_',
        },
        drawCallback: function () {
            this.api().table().container().querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function (el) {
                var inst = bootstrap.Dropdown.getInstance(el);
                if (inst) inst.dispose();
                new bootstrap.Dropdown(el, { popperConfig: { strategy: 'fixed' } });
            });
        },
    });

    wireToolbar(table, 'cat_search', 'cat_search_btn', 'reload_cat');

    // Open Add panel
    $('#add_category_btn').on('click', function () {
        resetForm();
        $('#categoryOffcanvasLabel').text('Add Category');
        offcanvas.show();
    });

    // Open Edit panel (delegated)
    $('#categories_table').on('click', '.category_edit_btn', function () {
        var category_id = $(this).data('category_id');
        $.post(edit_route, { category_id: category_id }, function (res) {
            if (res.msgtype !== 'val_success') { showToast(res.msg, 'error'); return; }
            resetForm();
            $('#category_id').val(res.data.id);
            $('#name').val(res.data.name);
            $('#categoryOffcanvasLabel').text('Edit Category');
            offcanvas.show();
        });
    });

    // Delete (delegated)
    $('#categories_table').on('click', '.category_delete_btn', function () {
        var category_id = $(this).data('category_id');
        var name        = $(this).data('name');
        Swal.fire({
            title: 'Delete category?',
            html:  'You are about to delete <strong>' + name + '</strong>. This cannot be undone.',
            icon:  'warning',
            showCancelButton    : true,
            confirmButtonColor  : '#dc3545',
            confirmButtonText   : 'Yes, delete it',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.post(del_route, { category_id: category_id }, function (res) {
                handleResponse(res, function () { table.ajax.reload(null, false); });
            });
        });
    });

    // Save form
    $('#categoryForm').on('submit', function (e) {
        e.preventDefault();
        $.post(save_route, $(this).serialize(), function (res) {
            handleResponse(res, function () {
                offcanvas.hide();
                table.ajax.reload(null, false);
            });
        });
    });

    document.getElementById('categoryOffcanvas').addEventListener('hidden.bs.offcanvas', function () {
        resetForm();
    });

    function resetForm() {
        $('#categoryForm')[0].reset();
        $('#category_id').val('');
    }
});
</script>
@endpush
