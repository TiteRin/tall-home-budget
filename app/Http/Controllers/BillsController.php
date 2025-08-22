<?php

namespace App\Http\Controllers;

use App\Actions\CreateBill;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillsController extends Controller
{
    public function __construct()
    {
    }

    public function index(): View
    {
        return view('bills.index');
    }

    /**
     * Create a new bill
     *
     * @param Request $request
     * @param CreateBill $createBillAction
     * @return JsonResponse
     */
    public function store(Request $request, CreateBill $createBillAction): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:1',
            'amount' => 'required|numeric|gt:0',
            'distribution_method' => 'required|string|in:' . implode(',', DistributionMethod::values()),
            'member_id' => 'nullable|integer|exists:members,id',
        ]);

        $amount = new Amount($validated['amount']);
        $distributionMethod = DistributionMethod::from($validated['distribution_method']);

        try {
            $bill = $createBillAction->handle(
                $validated['name'],
                $amount,
                $distributionMethod,
                $validated['member_id'] ?? null
            );

            return response()->json([
                'message' => 'Bill created successfully',
                'bill' => $bill,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the bill',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
