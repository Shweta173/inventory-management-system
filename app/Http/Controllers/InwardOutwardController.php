<?php

namespace App\Http\Controllers;

use App\Http\Requests\InwardOutwardRequest;
use App\Models\Category;
use App\Services\InwardOutwardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InwardOutwardController extends Controller
{
    private InwardOutwardService $inwardOutwardService;

    public function __construct(InwardOutwardService $inwardOutwardService)
    {
        $this->inwardOutwardService = $inwardOutwardService;
    }

    /**
     * Show the inward/outward transactions page.
     */
    public function index()
    {
        $data = [
            'title'      => 'Inward / Outward',
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'list_route' => route('io.lst'),
            'save_route' => route('io.sv'),
            'mat_by_cat' => route('io.materials_by_category'),
        ];

        return view('inward_outward.index', $data);
    }

    /**
     * Return paginated transactions list for DataTables (AJAX POST).
     */
    public function inward_outward_list(Request $request): JsonResponse
    {
        $response = $this->inwardOutwardService->inward_outward_list($request);
        return response()->json($response);
    }

    /**
     * Save a new transaction (AJAX POST).
     */
    public function inward_outward_save(InwardOutwardRequest $request): JsonResponse
    {
        $response = $this->inwardOutwardService->inward_outward_save($request);
        return response()->json([
            'msg'           => $response['msg'],
            'msgsuc'        => $response['msgclass'],
            'msgfail'       => $response['msgclass'],
            'TargetURL'     => $response['TargetURL'],
            'msgtype'       => $response['msgtype'],
            'msgtype_title' => $response['msgtype_title'],
            'data'          => $response['data'],
        ]);
    }

    /**
     * Return materials for a given category (AJAX GET — cascading dropdown).
     */
    public function get_materials_by_category(Request $request): JsonResponse
    {
        $category_id = (int) $request->input('category_id', 0);
        $materials   = $this->inwardOutwardService->get_materials_by_category($category_id);

        return response()->json([
            'data'    => $materials,
            'msgtype' => 'val_success',
        ]);
    }
}
