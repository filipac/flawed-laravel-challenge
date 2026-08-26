<?php

namespace App\Http\Controllers;

use App\Actions\SettleAccountInvoices;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountSettlementController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Request $request,
        Account $account,
        SettleAccountInvoices $settleAccountInvoices,
    ): JsonResponse {
        return response()->json(
            $settleAccountInvoices->handle($account, $request),
        );
    }
}
