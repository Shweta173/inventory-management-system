{{-- Add / Edit Material — Right-side Offcanvas --}}
<div class="offcanvas offcanvas-end"
     tabindex="-1"
     id="materialOffcanvas"
     aria-labelledby="materialOffcanvasLabel"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="materialOffcanvasLabel">Add Material</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <form id="materialForm" novalidate>
        @csrf
        <input type="hidden" name="material_id" id="material_id" value="" />

        <div class="offcanvas-body d-flex flex-column gap-4">

            <div>
                <label for="category_id" class="form-label">
                    Material Category <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">— Select Category —</option>
                </select>
            </div>

            <div>
                <label for="mat_name" class="form-label">
                    Material Name <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control"
                       id="mat_name"
                       name="name"
                       placeholder="e.g. Steel Rod 10mm"
                       maxlength="255"
                       autocomplete="off"
                       required />
                <div class="form-text">Alphanumeric characters, spaces, hyphens and underscores only.</div>
            </div>

            <div>
                <label for="opening_balance" class="form-label">
                    Opening Balance <span class="text-danger">*</span>
                </label>
                <input type="number"
                       class="form-control"
                       id="opening_balance"
                       name="opening_balance"
                       placeholder="0.00"
                       step="0.01"
                       required />
                <div class="form-text">Floating-point value, max 2 decimal places.</div>
            </div>

        </div>

        <div class="offcanvas-footer">
            <button type="button" class="btn-outline-inv" data-bs-dismiss="offcanvas">Cancel</button>
            <button type="submit" class="btn-dark-inv">Save Material</button>
        </div>
    </form>

</div>
