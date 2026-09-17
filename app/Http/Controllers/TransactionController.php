<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class TransactionController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    public function index(): View
    {
        $user = Auth::user();

        // Check if user can view all transactions or only their own based on permissions
        $query = Transaction::where('family_id', $user->family_id)
            ->with(['category:id,name', 'user:id,name']);

        $transactions = $query->latest()->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function store(TransactionRequest $request): RedirectResponse
    {
        try {
            $this->transactionService->createTransaction($request->validated(), Auth::user());

            return redirect()->back()->with('success', 'Transaction added successfully and family balance updated.');
        } catch (Throwable $e) {
            Log::error('Transaction creation failed.', [
                'user_id' => Auth::id(),
                'exception' => $e,
            ]);

            return redirect()->back()->with(
                'error',
                __('Unable to add the transaction. Please try again.'),
            );
        }
    }
}
