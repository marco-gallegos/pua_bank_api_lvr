<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Transaction::where('user_id', $request->user()->id)->whereMonth('date', now()->month)->whereYear('date', now()->year)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_id' => 'required|uuid|exists:categories,id',
            'bank_account_id' => 'nullable|uuid|exists:bank_accounts,id',
            'credit_card_id' => 'nullable|uuid|exists:credit_cards,id',
            'amount' => 'required|numeric',
            'date' => 'required|date_format:Y-m-d H:i:s',
            'description' => 'required|string|max:255',
            'type' => 'required|string|in:expense,income,transfer',
        ]);

        $finalData = [
            'user_id' => $request->user()->id,
            'category_id' => $validatedData['category_id'],
            'bank_account_id' => $validatedData['bank_account_id'] ?? null,
            'credit_card_id' => $validatedData['credit_card_id'] ?? null,
            'amount' => $validatedData['amount'],
            'date' => $validatedData['date'],
            'description' => $validatedData['description'],
            'type' => $validatedData['type'],
        ];

        try {
            $transaction = Transaction::create($finalData);
            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create transaction',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $transaction = Transaction::where('user_id', $request->user()->id)->find($id);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        return $transaction;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaction = Transaction::where('user_id', $request->user()->id)->find($id);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $validatedData = $request->validate([
            'category_id' => 'sometimes|required|uuid|exists:categories,id',
            'bank_account_id' => 'nullable|uuid|exists:bank_accounts,id',
            'credit_card_id' => 'nullable|uuid|exists:credit_cards,id',
            'amount' => 'sometimes|required|numeric',
            'date' => 'sometimes|required|date',
            'description' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|in:expense,income,transfer',
        ]);

        try {
            $transaction->update($validatedData);
            return $transaction;
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update transaction',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $transaction = Transaction::where('user_id', $request->user()->id)->find($id);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully']);
    }

    /**
     * Get The Current montly balance from day 1 to today.  
     */
    public function getCurrentMonthlyBalance(Request $request)
    {
        $transactions = Transaction::where('user_id', $request->user()->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');

        return response()->json([
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense,
        ]);
    }
}
