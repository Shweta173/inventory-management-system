<?php

namespace App\Services;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    /**
     * Return DataTable-compatible list of categories.
     */
    public function category_list(Request $request): array
    {
        $query = Category::query();

        $totalRecords = $query->count();

        $searchValue = $request->input('search.value', '');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%')
                  ->orWhere('created_at', 'like', '%' . $searchValue . '%');
            });
        }

        $columnIndex = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'asc');
        $columns     = ['id', 'name', 'created_at'];
        $orderColumn = $columns[$columnIndex] ?? 'id';
        $query->orderBy($orderColumn, $orderDir);

        $filteredCount = $query->count();
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $data   = $query->skip($start)->take($length)->get();

        // Map each row to a DataTable row array
        $resultData = $data->map(function ($item) {
            // Three-dot dropdown action menu
            $actionHtml  = '<div class="dropdown">';
            $actionHtml .= '<button class="action-dots" data-bs-toggle="dropdown" aria-expanded="false">···</button>';
            $actionHtml .= '<ul class="dropdown-menu dropdown-menu-end">';
            $actionHtml .= '<li><a class="dropdown-item category_edit_btn" href="#" data-category_id="' . $item->id . '" data-name="' . e($item->name) . '">Edit</a></li>';
            $actionHtml .= '<li><hr class="dropdown-divider my-1"></li>';
            $actionHtml .= '<li><a class="dropdown-item text-danger category_delete_btn" href="#" data-category_id="' . $item->id . '" data-name="' . e($item->name) . '">Delete</a></li>';
            $actionHtml .= '</ul></div>';

            return [
                $item->id,
                e($item->name),
                $item->created_at ? $item->created_at->format('d M Y') : '-',
                $actionHtml,
            ];
        });

        return [
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredCount,
            'data'            => $resultData,
        ];
    }

    /**
     * Create or update a category.
     */
    public function category_save(Request $request): array
    {
        $msg           = '';
        $msgclass      = '';
        $msgtype       = '';
        $msgtype_title = '';
        $TargetURL     = '';
        $data          = [];

        $category_id = $request->input('category_id');
        $name        = trim($request->input('name'));

        try {
            DB::beginTransaction();

            if ($category_id) {
                // Update existing category
                $category = Category::find($category_id);
                if ($category) {
                    $category->update(['name' => $name]);
                    $msg      = 'Category updated successfully.';
                    $msgclass = 'bg-success';
                    $msgtype  = 'val_success';
                    $data     = $category->toArray();
                } else {
                    $msg      = 'Category not found.';
                    $msgclass = 'bg-danger';
                    $msgtype  = 'val_error';
                }
            } else {
                // Create new category
                $category = Category::create(['name' => $name]);
                $msg      = 'Category added successfully.';
                $msgclass = 'bg-success';
                $msgtype  = 'val_success';
                $data     = $category->toArray();
            }

            DB::commit();
            $msgtype_title = 'Success';
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('CategoryService::category_save - ' . $e->getMessage());
            $msg           = 'Something went wrong. Please try again.';
            $msgclass      = 'bg-danger';
            $msgtype       = 'val_error';
            $msgtype_title = 'Error';
        }

        return compact('msg', 'msgclass', 'msgtype', 'msgtype_title', 'TargetURL', 'data');
    }

    /**
     * Fetch a single category for editing.
     */
    public function category_edit(Request $request): array
    {
        $msg           = '';
        $msgclass      = '';
        $msgtype       = '';
        $msgtype_title = '';
        $TargetURL     = '';
        $data          = [];

        $category_id = $request->input('category_id');
        $category    = Category::find($category_id);

        if ($category) {
            $data          = $category->toArray();
            $msg           = 'success';
            $msgclass      = 'bg-success';
            $msgtype       = 'val_success';
            $msgtype_title = 'Success';
        } else {
            $msg           = 'Category not found.';
            $msgclass      = 'bg-danger';
            $msgtype       = 'val_error';
            $msgtype_title = 'Error';
        }

        return compact('msg', 'msgclass', 'msgtype', 'msgtype_title', 'TargetURL', 'data');
    }

    /**
     * Delete a category (only if it has no associated materials).
     */
    public function category_delete(Request $request): array
    {
        $msg           = '';
        $msgclass      = '';
        $msgtype       = '';
        $msgtype_title = '';
        $TargetURL     = '';

        $category_id = $request->input('category_id');

        try {
            DB::beginTransaction();

            $category = Category::find($category_id);
            if (!$category) {
                DB::rollBack();
                return [
                    'msg'           => 'Category not found.',
                    'msgclass'      => 'bg-danger',
                    'msgtype'       => 'val_error',
                    'msgtype_title' => 'Error',
                    'TargetURL'     => '',
                ];
            }

            // Prevent deletion if any materials use this category
            $materialCount = $category->materials()->whereNull('deleted_at')->count();
            if ($materialCount > 0) {
                DB::rollBack();
                return [
                    'msg'           => "Cannot delete category — it has {$materialCount} material(s) assigned to it.",
                    'msgclass'      => 'bg-danger',
                    'msgtype'       => 'val_error',
                    'msgtype_title' => 'Error',
                    'TargetURL'     => '',
                ];
            }

            $category->delete();
            DB::commit();

            $msg           = 'Category deleted successfully.';
            $msgclass      = 'bg-success';
            $msgtype       = 'val_success';
            $msgtype_title = 'Success';
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('CategoryService::category_delete - ' . $e->getMessage());
            $msg           = 'Something went wrong. Please try again.';
            $msgclass      = 'bg-danger';
            $msgtype       = 'val_error';
            $msgtype_title = 'Error';
        }

        return compact('msg', 'msgclass', 'msgtype', 'msgtype_title', 'TargetURL');
    }
}
