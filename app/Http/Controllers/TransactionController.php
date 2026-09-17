<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class TransactionController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    public function index(): Response
    {
        $user = Auth::user();
        Gate::forUser($user)->authorize('viewAny', Transaction::class);

        $query = Transaction::where('family_id', $user->family_id)
            ->with(['category:id,name']);

        $transactions = $query->latest()->paginate(15);

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
        ]);
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
