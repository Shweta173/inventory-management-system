<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Show the categories management page.
     */
    public function index()
    {
        $data = [
            'title'      => 'Material Categories',
            'list_route' => route('cat.lst'),
            'save_route' => route('cat.sv'),
            'edit_route' => route('cat.edt'),
            'del_route'  => route('cat.del'),
        ];

        return view('categories.index', $data);
    }

    /**
     * Return paginated categories list for DataTables (AJAX POST).
     */
    public function category_list(Request $request): JsonResponse
    {
        $response = $this->categoryService->category_list($request);
        return response()->json($response);
    }

    /**
     * Create or update a category (AJAX POST).
     */
    public function category_save(CategoryRequest $request): JsonResponse
    {
        $response = $this->categoryService->category_save($request);
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
     * Fetch category data for the edit modal (AJAX POST).
     */
    public function category_edit(CategoryRequest $request): JsonResponse
    {
        $response = $this->categoryService->category_edit($request);
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
     * Delete a category (AJAX POST).
     */
    public function category_delete(CategoryRequest $request): JsonResponse
    {
        $response = $this->categoryService->category_delete($request);
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
