<?php

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendSettlementReceipt implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $paymentId) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payment = Payment::query()
            ->with('invoice.customer')
            ->findOrFail($this->paymentId);

        Log::info('Settlement receipt sent.', [
            'payment_id' => $payment->id,
            'invoice_reference' => $payment->invoice->reference,
            'customer_email' => $payment->invoice->customer->email,
        ]);
    }
}
