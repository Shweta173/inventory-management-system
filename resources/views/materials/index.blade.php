@extends('layouts.master')

@section('title', $title)

@section('content')

{{-- Page header --}}
<div class="d-flex align-items-start justify-content-between mb-3">
    <div>
        <h1 class="page-title mb-0">{{ $title }}</h1>
        <p class="page-subtitle mb-0">Add and manage inventory materials with opening balances.</p>
    </div>
    <button type="button" class="btn-dark-inv" id="add_material_btn">
        <i class="bi bi-plus-lg"></i> Add Material
    </button>
</div>

<div class="inv-card">
    <div class="inv-card-body">

        <div class="table-toolbar">
            <div class="table-search-wrap">
                <input id="mat_search" type="text" placeholder="Search materials…" autocomplete="off" />
                <button id="mat_search_btn" title="Search"><i class="bi bi-search"></i></button>
            </div>
            <button id="reload_mat" class="btn-icon-inv" title="Reload">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>

        <table id="materials_table" class="table w-100">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Category</th>
                    <th>Material Name</th>
                    <th>Opening Balance</th>
                    <th style="width:80px">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    </div>
</div>

@include('materials.modal')
@endsection


@push('scripts')
<script>
var list_route = '{{ $list_route }}';
var save_route = '{{ $save_route }}';
var edit_route = '{{ $edit_route }}';
var del_route  = '{{ $del_route }}';

var categories = @json($categories);

$(function () {

    var offcanvas = new bootstrap.Offcanvas(document.getElementById('materialOffcanvas'));

    var $catSelect = $('#category_id');
    $.each(categories, function (i, cat) {
        $catSelect.append($('<option>', { value: cat.id, text: cat.name }));
    });

    var table = $('#materials_table').DataTable({
        processing : true,
        serverSide : true,
        autoWidth  : false,
        ajax       : { url: list_route, type: 'POST' },
        columns    : [
            { data: 0, name: 'id',              width: '5%' },
            { data: 1, name: 'category_name',   width: '20%', orderable: false },
            { data: 2, name: 'name',            width: '42%' },
            { data: 3, name: 'opening_balance', width: '20%' },
            { data: 4, name: 'action',          width: '8%',  orderable: false, searchable: false },
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

    wireToolbar(table, 'mat_search', 'mat_search_btn', 'reload_mat');

    $('#add_material_btn').on('click', function () {
        resetForm();
        $('#materialOffcanvasLabel').text('Add Material');
        offcanvas.show();
    });

    $('#materials_table').on('click', '.material_edit_btn', function () {
        var $a = $(this);
        resetForm();
        $('#material_id').val($a.data('material_id'));
        $('#category_id').val($a.data('category_id'));
        $('#mat_name').val($a.data('name'));
        $('#opening_balance').val($a.data('opening_balance'));
        $('#materialOffcanvasLabel').text('Edit Material');
        offcanvas.show();
    });

    $('#materials_table').on('click', '.material_delete_btn', function () {
        var material_id = $(this).data('material_id');
        var name        = $(this).data('name');
        Swal.fire({
            title: 'Delete material?',
            html: 'You are about to soft-delete <strong>' + name + '</strong>.',
            icon: 'warning',
            showCancelButton   : true,
            confirmButtonColor : '#dc3545',
            confirmButtonText  : 'Yes, delete it',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.post(del_route, { material_id: material_id }, function (res) {
                handleResponse(res, function () { table.ajax.reload(null, false); });
            });
        });
    });

    $('#materialForm').on('submit', function (e) {
        e.preventDefault();
        $.post(save_route, $(this).serialize(), function (res) {
            handleResponse(res, function () {
                offcanvas.hide();
                table.ajax.reload(null, false);
            });
        });
    });

    document.getElementById('materialOffcanvas').addEventListener('hidden.bs.offcanvas', function () {
        resetForm();
    });

    function resetForm() {
        $('#materialForm')[0].reset();
        $('#material_id').val('');
        $('#category_id').val('');
    }
});
</script>
@endpush
