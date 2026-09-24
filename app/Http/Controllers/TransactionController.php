<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Services\CategoryService;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private CategoryService $categoryService,
    ) {}

    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = Auth::user();
        Gate::forUser($user)->authorize('viewAny', Transaction::class);

        $query = Transaction::where('family_id', $user->family_id)
            ->with(['category:id,name']);

        $categoryId = $request->integer('category_id');
        if ($categoryId > 0) {
            $query->where('category_id', $categoryId)
                ->whereHas('category', function ($categoryQuery) use ($user): void {
                    $categoryQuery->where('family_id', $user->family_id);
                });
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        $pageProps = [
            'transactions' => $transactions,
            'categories' => $this->categoryService->getFamilyCategories($user),
            'selected_category_id' => $categoryId > 0 ? $categoryId : null,
        ];

        if ($user->family && Gate::forUser($user)->allows('viewStatistics', $user->family)) {
            $pageProps['statistics'] = $this->categoryService->getFamilyStatistics($user);
        }

        return Inertia::render('Transactions/Index', [
            ...$pageProps,
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
