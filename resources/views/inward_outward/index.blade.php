@extends('layouts.master')

@section('title', $title)

@section('content')

{{-- Page header --}}
<div class="d-flex align-items-start justify-content-between mb-3">
    <div>
        <h1 class="page-title mb-0">{{ $title }}</h1>
        <p class="page-subtitle mb-0">Record stock movements — positive quantity for inward, negative for outward.</p>
    </div>
    <button type="button" class="btn-dark-inv" id="add_transaction_btn">
        <i class="bi bi-plus-lg"></i> Add Transaction
    </button>
</div>

<div class="inv-card">
    <div class="inv-card-body">

        <div class="table-toolbar">
            <div class="table-search-wrap">
                <input id="io_search" type="text" placeholder="Search transactions…" autocomplete="off" />
                <button id="io_search_btn" title="Search"><i class="bi bi-search"></i></button>
            </div>
            <button id="reload_io" class="btn-icon-inv" title="Reload">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>

        <table id="io_table" class="table w-100">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Category</th>
                    <th>Material</th>
                    <th>Date</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    </div>
</div>

@include('inward_outward.modal')
@endsection


@push('scripts')
<script>
var list_route     = '{{ $list_route }}';
var save_route     = '{{ $save_route }}';
var mat_by_cat_url = '{{ $mat_by_cat }}';

var categories = @json($categories);

$(function () {

    var offcanvas = new bootstrap.Offcanvas(document.getElementById('ioOffcanvas'));

    var $catSelect = $('#io_category_id');
    $.each(categories, function (i, cat) {
        $catSelect.append($('<option>', { value: cat.id, text: cat.name }));
    });

    flatpickr('#io_date', {
        dateFormat   : 'Y-m-d',
        allowInput   : true,
        disableMobile: false,
    });

    var table = $('#io_table').DataTable({
        processing : true,
        serverSide : true,
        autoWidth  : false,
        ajax       : { url: list_route, type: 'POST' },
        columns    : [
            { data: 0, name: 'id',       width: '5%' },
            { data: 1, name: 'category', width: '20%', orderable: false },
            { data: 2, name: 'material', width: '40%', orderable: false },
            { data: 3, name: 'date',     width: '20%' },
            { data: 4, name: 'quantity', width: '10%' },
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
    });

    wireToolbar(table, 'io_search', 'io_search_btn', 'reload_io');

    $('#add_transaction_btn').on('click', function () {
        resetForm();
        offcanvas.show();
    });

    // Cascading dropdown
    $('#io_category_id').on('change', function () {
        var category_id = $(this).val();
        var $matSelect  = $('#io_material_id');

        $matSelect.empty()
                  .append('<option value="">— Loading… —</option>')
                  .prop('disabled', true);

        if (!category_id) {
            $matSelect.empty()
                      .append('<option value="">— Select Material —</option>')
                      .prop('disabled', false);
            return;
        }

        $.get(mat_by_cat_url, { category_id: category_id }, function (res) {
            $matSelect.empty().append('<option value="">— Select Material —</option>');
            if (res.data && res.data.length > 0) {
                $.each(res.data, function (i, mat) {
                    $matSelect.append($('<option>', { value: mat.id, text: mat.name }));
                });
            } else {
                $matSelect.append('<option value="" disabled>No materials in this category</option>');
            }
            $matSelect.prop('disabled', false);
        });
    });

    $('#ioForm').on('submit', function (e) {
        e.preventDefault();
        $.post(save_route, $(this).serialize(), function (res) {
            handleResponse(res, function () {
                offcanvas.hide();
                table.ajax.reload(null, false);
            });
        });
    });

    document.getElementById('ioOffcanvas').addEventListener('hidden.bs.offcanvas', function () {
        resetForm();
    });

    function resetForm() {
        $('#ioForm')[0].reset();
        $('#io_material_id')
            .empty()
            .append('<option value="">— Select Material —</option>')
            .prop('disabled', false);
        if ($('#io_date')[0] && $('#io_date')[0]._flatpickr) {
            $('#io_date')[0]._flatpickr.clear();
        }
    }
});
</script>
@endpush
