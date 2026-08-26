<?php

use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;

test('returns the account balance and pending invoice count', function () {
    $account = Account::factory()->withAvailableBalance(125_000)->create();
    $customer = Customer::factory()->for($account)->create();
    Invoice::factory()->count(2)->for($customer)->create();
    Invoice::factory()->for($customer)->settled()->create();

    $response = $this->getJson(route('accounts.summary', $account));

    $response->assertOk()->assertExactJson([
        'account_id' => $account->id,
        'available_balance_cents' => 125_000,
        'pending_invoices' => 2,
    ]);
});
