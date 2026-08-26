<?php

use App\Jobs\SendSettlementReceipt;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Support\Facades\Queue;

test('settles pending invoices and returns the updated account balance', function () {
    $account = Account::factory()->withAvailableBalance(100_000)->create();
    $customer = Customer::factory()->for($account)->create();
    $firstInvoice = Invoice::factory()->for($customer)->create(['amount_cents' => 20_000]);
    $secondInvoice = Invoice::factory()->for($customer)->create(['amount_cents' => 30_000]);
    Queue::fake([SendSettlementReceipt::class]);

    $response = $this->postJson(route('accounts.settle', $account), [
        'fee_percent' => 2.5,
        'processed_by' => 'Jordan Lee',
    ]);

    $response->assertOk()->assertJson([
        'account_id' => $account->id,
        'available_balance_cents' => 48_750,
        'settled' => [
            [
                'invoice_id' => $firstInvoice->id,
                'customer' => $customer->name,
                'amount_cents' => 20_000,
                'fee_cents' => 500,
            ],
            [
                'invoice_id' => $secondInvoice->id,
                'customer' => $customer->name,
                'amount_cents' => 30_000,
                'fee_cents' => 750,
            ],
        ],
    ]);

    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
        'available_balance_cents' => 48_750,
    ]);
    $this->assertDatabaseHas('invoices', ['id' => $firstInvoice->id, 'status' => 'settled']);
    $this->assertDatabaseHas('invoices', ['id' => $secondInvoice->id, 'status' => 'settled']);
    $this->assertDatabaseHas('payments', [
        'invoice_id' => $firstInvoice->id,
        'amount_cents' => 20_000,
        'fee_cents' => 500,
        'processed_by' => 'Jordan Lee',
    ]);
    Queue::assertPushed(SendSettlementReceipt::class, 2);
});

test('ignores invoices that were already settled', function () {
    $account = Account::factory()->withAvailableBalance(100_000)->create();
    $customer = Customer::factory()->for($account)->create();
    $settledInvoice = Invoice::factory()->for($customer)->settled()->create();
    Queue::fake([SendSettlementReceipt::class]);

    $response = $this->postJson(route('accounts.settle', $account), [
        'fee_percent' => 2.5,
        'processed_by' => 'Jordan Lee',
    ]);

    $response->assertOk()->assertJsonPath('settled', []);
    $this->assertDatabaseMissing('payments', ['invoice_id' => $settledInvoice->id]);
    Queue::assertNothingPushed();
});
