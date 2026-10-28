<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Material;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaterialService
{
    /**
     * Return DataTable-compatible list of materials.
     */
    public function material_list(Request $request): array
    {
        $query = Material::with('category');

        $totalRecords = Material::count();

        $searchValue = $request->input('search.value', '');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%')
                  ->orWhere('opening_balance', 'like', '%' . $searchValue . '%')
                  ->orWhereHas('category', function ($q2) use ($searchValue) {
                      $q2->where('name', 'like', '%' . $searchValue . '%');
                  });
            });
        }

        $columnIndex = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'desc');
        switch ($columnIndex) {
            case 1: $query->orderBy('name', $orderDir); break;
            case 2: $query->orderBy('opening_balance', $orderDir); break;
            default: $query->orderBy('id', 'desc'); break;
        }

        $filteredCount = $query->count();
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $data   = $query->skip($start)->take($length)->get();

        $resultData = $data->map(function ($item) {
            $actionHtml  = '<div class="dropdown">';
            $actionHtml .= '<button class="action-dots" data-bs-toggle="dropdown" aria-expanded="false">···</button>';
            $actionHtml .= '<ul class="dropdown-menu dropdown-menu-end">';
            $actionHtml .= '<li><a class="dropdown-item material_edit_btn" href="#"'
                . ' data-material_id="' . $item->id . '"'
                . ' data-category_id="' . $item->category_id . '"'
                . ' data-name="' . e($item->name) . '"'
                . ' data-opening_balance="' . $item->opening_balance . '">Edit</a></li>';
            $actionHtml .= '<li><hr class="dropdown-divider my-1"></li>';
            $actionHtml .= '<li><a class="dropdown-item text-danger material_delete_btn" href="#"'
                . ' data-material_id="' . $item->id . '"'
                . ' data-name="' . e($item->name) . '">Delete</a></li>';
            $actionHtml .= '</ul></div>';

            return [
                $item->id,
                e($item->category->name ?? '-'),
                e($item->name),
                number_format((float) $item->opening_balance, 2),
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
     * Create or update a material.
     */
    public function material_save(Request $request): array
    {
        $msg           = '';
        $msgclass      = '';
        $msgtype       = '';
        $msgtype_title = '';
        $TargetURL     = '';
        $data          = [];

        $material_id     = $request->input('material_id');
        $category_id     = $request->input('category_id');
        $name            = trim($request->input('name'));
        $opening_balance = $request->input('opening_balance');

        try {
            DB::beginTransaction();

            if ($material_id) {
                // Update existing material
                $material = Material::find($material_id);
                if ($material) {
                    $material->update([
                        'category_id'     => $category_id,
                        'name'            => $name,
                        'opening_balance' => $opening_balance,
                    ]);
                    $msg  = 'Material updated successfully.';
                    $data = $material->load('category')->toArray();
                } else {
                    $msg      = 'Material not found.';
                    $msgclass = 'bg-danger';
                    $msgtype  = 'val_error';
                    DB::rollBack();
                    return compact('msg', 'msgclass', 'msgtype', 'msgtype_title', 'TargetURL', 'data');
                }
            } else {
                // Create new material
                $material = Material::create([
                    'category_id'     => $category_id,
                    'name'            => $name,
                    'opening_balance' => $opening_balance,
                ]);
                $msg  = 'Material added successfully.';
                $data = $material->load('category')->toArray();
            }

            DB::commit();
            $msgclass      = 'bg-success';
            $msgtype       = 'val_success';
            $msgtype_title = 'Success';
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('MaterialService::material_save - ' . $e->getMessage());
            $msg           = 'Something went wrong. Please try again.';
            $msgclass      = 'bg-danger';
            $msgtype       = 'val_error';
            $msgtype_title = 'Error';
        }

        return compact('msg', 'msgclass', 'msgtype', 'msgtype_title', 'TargetURL', 'data');
    }

    /**
     * Fetch a single material for editing.
     */
    public function material_edit(Request $request): array
    {
        $msg           = '';
        $msgclass      = '';
        $msgtype       = '';
        $msgtype_title = '';
        $TargetURL     = '';
        $data          = [];

        $material_id = $request->input('material_id');
        $material    = Material::with('category')->find($material_id);

        if ($material) {
            $data          = $material->toArray();
            $msg           = 'success';
            $msgclass      = 'bg-success';
            $msgtype       = 'val_success';
            $msgtype_title = 'Success';
        } else {
            $msg           = 'Material not found.';
            $msgclass      = 'bg-danger';
            $msgtype       = 'val_error';
            $msgtype_title = 'Error';
        }

        return compact('msg', 'msgclass', 'msgtype', 'msgtype_title', 'TargetURL', 'data');
    }

    /**
     * Soft-delete a material.
     */
    public function material_delete(Request $request): array
    {
        $msg           = '';
        $msgclass      = '';
        $msgtype       = '';
        $msgtype_title = '';
        $TargetURL     = '';

        $material_id = $request->input('material_id');

        try {
            DB::beginTransaction();

            $material = Material::find($material_id);
            if (!$material) {
                DB::rollBack();
                return [
                    'msg'           => 'Material not found.',
                    'msgclass'      => 'bg-danger',
                    'msgtype'       => 'val_error',
                    'msgtype_title' => 'Error',
                    'TargetURL'     => '',
                ];
            }

            // Soft delete — sets deleted_at timestamp
            $material->delete();
            DB::commit();

            $msg           = 'Material deleted successfully.';
            $msgclass      = 'bg-success';
            $msgtype       = 'val_success';
            $msgtype_title = 'Success';
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('MaterialService::material_delete - ' . $e->getMessage());
            $msg           = 'Something went wrong. Please try again.';
            $msgclass      = 'bg-danger';
            $msgtype       = 'val_error';
            $msgtype_title = 'Error';
        }

        return compact('msg', 'msgclass', 'msgtype', 'msgtype_title', 'TargetURL');
    }

    /**
     * Return all active materials with their current balance for the manage page.
     */
    public function manage_list(Request $request): array
    {
        $query = Material::with(['category', 'transactions']);

        $totalRecords = Material::count();

        $searchValue = $request->input('search.value', '');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%')
                  ->orWhere('opening_balance', 'like', '%' . $searchValue . '%')
                  ->orWhereHas('category', function ($q2) use ($searchValue) {
                      $q2->where('name', 'like', '%' . $searchValue . '%');
                  });
            });
        }

        $columnIndex = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'desc');
        switch ($columnIndex) {
            case 1: $query->orderBy('name', $orderDir); break;
            case 2: $query->orderBy('opening_balance', $orderDir); break;
            default: $query->orderBy('id', 'desc'); break;
        }

        $filteredCount = $query->count();
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $data   = $query->skip($start)->take($length)->get();

        $resultData = $data->map(function ($item) {
            // Sum all transaction quantities for this material
            $totalTransactions  = $item->transactions->sum('quantity');
            $currentBalance     = (float) $item->opening_balance + (float) $totalTransactions;

            $actionHtml  = '<div class="dropdown">';
            $actionHtml .= '<button class="action-dots" data-bs-toggle="dropdown" aria-expanded="false">···</button>';
            $actionHtml .= '<ul class="dropdown-menu dropdown-menu-end">';
            $actionHtml .= '<li><a class="dropdown-item material_edit_btn" href="#"'
                . ' data-material_id="' . $item->id . '"'
                . ' data-category_id="' . $item->category_id . '"'
                . ' data-name="' . e($item->name) . '"'
                . ' data-opening_balance="' . $item->opening_balance . '">Edit</a></li>';
            $actionHtml .= '<li><hr class="dropdown-divider my-1"></li>';
            $actionHtml .= '<li><a class="dropdown-item text-danger material_delete_btn" href="#"'
                . ' data-material_id="' . $item->id . '"'
                . ' data-name="' . e($item->name) . '">Delete</a></li>';
            $actionHtml .= '</ul></div>';

            return [
                $item->id,
                e($item->category->name ?? '-'),
                e($item->name),
                number_format((float) $item->opening_balance, 2),
                number_format($currentBalance, 2),
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
}
