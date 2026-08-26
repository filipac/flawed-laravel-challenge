<?php

namespace App\Actions;

use App\Jobs\SendSettlementReceipt;
use App\Models\Account;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettleAccountInvoices
{
    /**
     * @return array{account_id: int, available_balance_cents: int, settled: list<array{invoice_id: int, customer: string, amount_cents: int, fee_cents: int}>}
     */
    public function handle(Account $account, Request $request): array
    {
        $feePercent = $request->input('fee_percent');
        $processedBy = $request->input('processed_by');
        $pendingInvoices = $account->invoices()
            ->where('status', 'pending')
            ->get();
        $settledInvoices = [];

        foreach ($pendingInvoices as $invoice) {
            $amountAlreadyPaid = (int) Payment::query()
                ->where('invoice_id', $invoice->id)
                ->sum('amount_cents');
            $amountToSettle = $invoice->amount_cents - $amountAlreadyPaid;

            if ($amountToSettle <= 0) {
                continue;
            }

            $feeCents = (int) round($amountToSettle * ((float) $feePercent / 100));
            $payment = Payment::query()->create([
                'invoice_id' => $invoice->id,
                'amount_cents' => $amountToSettle,
                'fee_cents' => $feeCents,
                'processed_by' => $processedBy,
            ]);

            $invoice->update([
                'status' => 'settled',
                'settled_at' => now(),
            ]);

            SendSettlementReceipt::dispatch($payment->id);

            $account->available_balance_cents -= $amountToSettle + $feeCents;
            $account->save();

            $settledInvoices[] = [
                'invoice_id' => $invoice->id,
                'customer' => $invoice->customer->name,
                'amount_cents' => $amountToSettle,
                'fee_cents' => $feeCents,
            ];
        }

        Cache::forget("accounts:{$account->id}:summary");

        return [
            'account_id' => $account->id,
            'available_balance_cents' => $account->available_balance_cents,
            'settled' => $settledInvoices,
        ];
    }
}
