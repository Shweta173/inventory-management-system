<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaterialRequest;
use App\Models\Category;
use App\Services\MaterialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    private MaterialService $materialService;

    public function __construct(MaterialService $materialService)
    {
        $this->materialService = $materialService;
    }

    /**
     * Show the materials CRUD page.
     */
    public function index()
    {
        $data = [
            'title'      => 'Materials',
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'list_route' => route('mat.lst'),
            'save_route' => route('mat.sv'),
            'edit_route' => route('mat.edt'),
            'del_route'  => route('mat.del'),
        ];

        return view('materials.index', $data);
    }

    /**
     * Show the manage-materials page (with current balance).
     */
    public function manage()
    {
        $data = [
            'title'      => 'Manage Materials',
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'list_route' => route('mat.manage.lst'),
            'edit_route' => route('mat.edt'),
            'del_route'  => route('mat.del'),
            'save_route' => route('mat.sv'),
        ];

        return view('materials.manage', $data);
    }

    /**
     * Return paginated materials list for DataTables (AJAX POST).
     */
    public function material_list(Request $request): JsonResponse
    {
        $response = $this->materialService->material_list($request);
        return response()->json($response);
    }

    /**
     * Return manage-page list (includes current balance) for DataTables (AJAX POST).
     */
    public function manage_list(Request $request): JsonResponse
    {
        $response = $this->materialService->manage_list($request);
        return response()->json($response);
    }

    /**
     * Create or update a material (AJAX POST).
     */
    public function material_save(MaterialRequest $request): JsonResponse
    {
        $response = $this->materialService->material_save($request);
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
     * Fetch material data for the edit modal (AJAX POST).
     */
    public function material_edit(MaterialRequest $request): JsonResponse
    {
        $response = $this->materialService->material_edit($request);
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
     * Soft-delete a material (AJAX POST).
     */
    public function material_delete(MaterialRequest $request): JsonResponse
    {
        $response = $this->materialService->material_delete($request);
        return response()->json([
            'msg'           => $response['msg'],
            'msgsuc'        => $response['msgclass'],
            'msgfail'       => $response['msgclass'],
            'TargetURL'     => $response['TargetURL'],
            'msgtype'       => $response['msgtype'],
            'msgtype_title' => $response['msgtype_title'],
        ]);
    }
}
