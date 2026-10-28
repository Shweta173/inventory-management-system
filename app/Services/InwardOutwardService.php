<?php

namespace App\Services;

use App\Models\Category;
use App\Models\InwardOutward;
use App\Models\Material;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InwardOutwardService
{
    /**
     * Return DataTable-compatible list of inward/outward transactions.
     */
    public function inward_outward_list(Request $request): array
    {
        $query = InwardOutward::with(['material.category']);

        $totalRecords = InwardOutward::count();

        $searchValue = $request->input('search.value', '');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('date', 'like', '%' . $searchValue . '%')
                  ->orWhere('quantity', 'like', '%' . $searchValue . '%')
                  ->orWhereHas('material', function ($q2) use ($searchValue) {
                      $q2->where('name', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('category', function ($q3) use ($searchValue) {
                            $q3->where('name', 'like', '%' . $searchValue . '%');
                        });
                  });
            });
        }

        $columnIndex = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'desc');
        switch ($columnIndex) {
            case 3: $query->orderBy('date', $orderDir); break;
            case 4: $query->orderBy('quantity', $orderDir); break;
            default: $query->orderBy('id', 'desc'); break;
        }

        $filteredCount = $query->count();
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $data   = $query->skip($start)->take($length)->get();

        $resultData = $data->map(function ($item) {
            // Show quantity with sign: positive = inward, negative = outward
            $quantityClass = $item->quantity >= 0 ? 'text-success' : 'text-danger';
            $quantitySign  = $item->quantity >= 0 ? '+' : '';

            return [
                $item->id,
                e($item->material->category->name ?? '-'),
                e($item->material->name ?? '-'),
                $item->date ? $item->date->format('d M Y') : '-',
                '<span class="' . $quantityClass . ' fw-semibold">'
                    . $quantitySign . number_format((float) $item->quantity, 2)
                    . '</span>',
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
     * Save a new inward/outward transaction.
     */
    public function inward_outward_save(Request $request): array
    {
        $msg           = '';
        $msgclass      = '';
        $msgtype       = '';
        $msgtype_title = '';
        $TargetURL     = '';
        $data          = [];

        $material_id = $request->input('material_id');
        $date        = $request->input('date');
        $quantity    = $request->input('quantity');

        try {
            DB::beginTransaction();

            // Verify the material is still active (not soft-deleted)
            $material = Material::whereNull('deleted_at')->find($material_id);
            if (!$material) {
                DB::rollBack();
                return [
                    'msg'           => 'Selected material not found or has been deleted.',
                    'msgclass'      => 'bg-danger',
                    'msgtype'       => 'val_error',
                    'msgtype_title' => 'Error',
                    'TargetURL'     => '',
                    'data'          => [],
                ];
            }

            $transaction = InwardOutward::create([
                'material_id' => $material_id,
                'date'        => $date,
                'quantity'    => $quantity,
            ]);

            DB::commit();

            $msg           = 'Transaction saved successfully.';
            $msgclass      = 'bg-success';
            $msgtype       = 'val_success';
            $msgtype_title = 'Success';
            $data          = $transaction->load('material.category')->toArray();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('InwardOutwardService::inward_outward_save - ' . $e->getMessage());
            $msg           = 'Something went wrong. Please try again.';
            $msgclass      = 'bg-danger';
            $msgtype       = 'val_error';
            $msgtype_title = 'Error';
        }

        return compact('msg', 'msgclass', 'msgtype', 'msgtype_title', 'TargetURL', 'data');
    }

    /**
     * Return active materials for a given category (used for cascading dropdown).
     */
    public function get_materials_by_category(int $category_id): array
    {
        return Material::where('category_id', $category_id)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
    }
}
