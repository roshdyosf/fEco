<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Http\Requests\TransactionRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;


class TransactionController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    public function index(TransactionRequest $request)
    {
        $user = Auth::user();

        // Check if user can view all transactions or only their own based on permissions
        $query = Transaction::where('family_id', $user->family_id)
            ->with(['category:id,name', 'user:id,name']);

        $transactions = $query->latest()->paginate(15);

        return view('transactions.index', compact('transactions'));
    }


    public function store(TransactionRequest $request)
    {
        try {
            $this->transactionService->createTransaction($request->validated(), Auth::user());

            return redirect()->back()->with('success', 'Transaction added successfully and family balance updated.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add transaction: ' . $e->getMessage());
        }
    }
}
