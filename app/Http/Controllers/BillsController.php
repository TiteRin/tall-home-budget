<?php

namespace App\Http\Controllers;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Services\Bill\BillService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillsController extends Controller
{
    public function __construct(
        private readonly BillService $billService
    ) {}

    public function index(): View
    {
        return view('bills.index');
    }

    /**
     * Create a new bill
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:1',
            'amount' => 'required|numeric|gt:0',
            'distribution_method' => 'required|string|in:' . implode(',', DistributionMethod::values()),
            'member_id' => 'nullable|integer|exists:members,id',
        ]);

        $amount = new Amount($validated['amount']);
        $distributionMethod = DistributionMethod::from($validated['distribution_method']);

        $bill = $this->billService->createBill(
            $validated['name'],
            $amount,
            $distributionMethod,
            null, // Use current household
            $validated['member_id'] ?? null
        );

        return response()->json([
            'message' => 'Bill created successfully',
            'bill' => $bill,
        ], 201);
    }
}
