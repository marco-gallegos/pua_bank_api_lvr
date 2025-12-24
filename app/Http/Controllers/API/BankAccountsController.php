<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

// models
use App\Models\BankAccount;

class BankAccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return BankAccount::where('user_id', $request->user()->id)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // collect just specific fields validate it and then create the bank account
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'balance' => 'required|numeric',
        ]);

        $finalData = [
            'user_id' => $request->user()->id,
            'name' => $validatedData['name'],
            'type' => $validatedData['type'],
            'current_balance' => $validatedData['balance'],
        ];
        try {
            $bankAccount = BankAccount::create($finalData);
            return $bankAccount;
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return BankAccount::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bankAccount = BankAccount::find($id);
        $bankAccount->update($request->all());
        return $bankAccount;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bankAccount = BankAccount::find($id);
        $bankAccount->delete();
        return $bankAccount;
    }
}
