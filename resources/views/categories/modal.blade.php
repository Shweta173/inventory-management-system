{{-- Add / Edit Category — Right-side Offcanvas --}}
<div class="offcanvas offcanvas-end"
     tabindex="-1"
     id="categoryOffcanvas"
     aria-labelledby="categoryOffcanvasLabel"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="categoryOffcanvasLabel">Add Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <form id="categoryForm" novalidate>
        @csrf
        <input type="hidden" name="category_id" id="category_id" value="" />

        <div class="offcanvas-body d-flex flex-column gap-4">

            <div>
                <label for="name" class="form-label">
                    Category Name <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control"
                       id="name"
                       name="name"
                       placeholder="e.g. Raw Material"
                       maxlength="100"
                       autocomplete="off"
                       required />
                <div class="form-text">Predefined values: Raw Material, Finish Goods, Spares, Machines, Others.</div>
            </div>

        </div>

        <div class="offcanvas-footer">
            <button type="button" class="btn-outline-inv" data-bs-dismiss="offcanvas">Cancel</button>
            <button type="submit" class="btn-dark-inv">Save Category</button>
        </div>
    </form>

</div>
