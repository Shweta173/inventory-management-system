@extends('layouts.master')

@section('title', $title)

@section('content')

<div class="mb-3">
    <h1 class="page-title mb-0">{{ $title }}</h1>
    <p class="page-subtitle mb-0">View opening balance and current balance for each material.</p>
</div>

<div class="inv-card">
    <div class="inv-card-body">

        <div class="table-toolbar">
            <div class="table-search-wrap">
                <input id="mgmt_search" type="text" placeholder="Search materials…" autocomplete="off" />
                <button id="mgmt_search_btn" title="Search"><i class="bi bi-search"></i></button>
            </div>
            <button id="reload_mgmt" class="btn-icon-inv" title="Reload">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>

        <table id="manage_materials_table" class="table w-100">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Category</th>
                    <th>Material Name</th>
                    <th>Opening Balance</th>
                    <th>Current Balance</th>
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

    var table = $('#manage_materials_table').DataTable({
        processing : true,
        serverSide : true,
        autoWidth  : false,
        ajax       : { url: list_route, type: 'POST' },
        columns    : [
            { data: 0, name: 'id',              width: '5%' },
            { data: 1, name: 'category',        width: '16%', orderable: false },
            { data: 2, name: 'name',            width: '27%' },
            { data: 3, name: 'opening_balance', width: '17%' },
            { data: 4, name: 'current_balance', width: '17%', orderable: false, searchable: false },
            { data: 5, name: 'action',          width: '8%',  orderable: false, searchable: false },
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
        rowCallback: function (row, data) {
            var current = parseFloat(String(data[4]).replace(/,/g, ''));
            if (current < 0) {
                $('td:eq(4)', row).css({ color: '#dc2626', fontWeight: '600' });
            }
        },
    });

    wireToolbar(table, 'mgmt_search', 'mgmt_search_btn', 'reload_mgmt');

    $('#manage_materials_table').on('click', '.material_edit_btn', function () {
        var $a = $(this);
        resetForm();
        $('#material_id').val($a.data('material_id'));
        $('#category_id').val($a.data('category_id'));
        $('#mat_name').val($a.data('name'));
        $('#opening_balance').val($a.data('opening_balance'));
        $('#materialOffcanvasLabel').text('Edit Material');
        offcanvas.show();
    });

    $('#manage_materials_table').on('click', '.material_delete_btn', function () {
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
