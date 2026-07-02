<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeeItemRequest;
use App\Http\Requests\UpdateFeeItemRequest;
use App\Models\FeeItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * C05 費用項目設定
 *
 * 功能編號：C05
 * 對應文件：docs/sdd/c05-fee-item-sdd.md
 */
class FeeItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FeeItem::with('course')->orderBy('id');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(StoreFeeItemRequest $request): JsonResponse
    {
        $item = FeeItem::create($request->validated());

        return response()->json(['data' => $item->load('course')], 201);
    }

    public function update(UpdateFeeItemRequest $request, FeeItem $feeItem): JsonResponse
    {
        $feeItem->update($request->validated());

        return response()->json(['data' => $feeItem->load('course')]);
    }

    public function destroy(FeeItem $feeItem): JsonResponse
    {
        $feeItem->delete();

        return response()->json(null, 204);
    }
}
