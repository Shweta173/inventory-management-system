{{-- Add Inward / Outward Transaction — Right-side Offcanvas --}}
<div class="offcanvas offcanvas-end"
     tabindex="-1"
     id="ioOffcanvas"
     aria-labelledby="ioOffcanvasLabel"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="ioOffcanvasLabel">Add Transaction</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <form id="ioForm" novalidate>
        @csrf

        <div class="offcanvas-body d-flex flex-column gap-4">

            <div>
                <label for="io_category_id" class="form-label">
                    Material Category <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="io_category_id" name="category_id" required>
                    <option value="">— Select Category —</option>
                </select>
            </div>

            <div>
                <label for="io_material_id" class="form-label">
                    Material Name <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="io_material_id" name="material_id" required>
                    <option value="">— Select Material —</option>
                </select>
                <div class="form-text">Select a category first to load its materials.</div>
            </div>

            <div>
                <label for="io_date" class="form-label">
                    Date <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control"
                       id="io_date"
                       name="date"
                       placeholder="YYYY-MM-DD"
                       autocomplete="off"
                       required />
            </div>

            <div>
                <label for="io_quantity" class="form-label">
                    Quantity <span class="text-danger">*</span>
                </label>
                <input type="number"
                       class="form-control"
                       id="io_quantity"
                       name="quantity"
                       placeholder="e.g. 10 or -5"
                       step="0.01"
                       required />
                <div class="form-text">
                    <span style="color:#059669;font-weight:600;">Positive</span> = inward &nbsp;|&nbsp;
                    <span style="color:#dc2626;font-weight:600;">Negative</span> = outward. Cannot be zero.
                </div>
            </div>

        </div>

        <div class="offcanvas-footer">
            <button type="button" class="btn-outline-inv" data-bs-dismiss="offcanvas">Cancel</button>
            <button type="submit" class="btn-dark-inv">Save Transaction</button>
        </div>
    </form>

</div>
