<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AccountSummaryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Account $account): JsonResponse
    {
        $summary = Cache::remember(
            "accounts:{$account->id}:summary",
            now()->addMinutes(10),
            fn () => [
                'account_id' => $account->id,
                'available_balance_cents' => $account->fresh()->available_balance_cents,
                'pending_invoices' => $account->invoices()->where('status', 'pending')->count(),
            ],
        );

        return response()->json($summary);
    }
}
