<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $account = Account::factory()
            ->withAvailableBalance(250_000)
            ->create(['name' => 'Northstar Supplies']);

        $firstCustomer = Customer::factory()->for($account)->create([
            'name' => 'Acme Retail',
            'email' => 'billing@acme.test',
        ]);
        $secondCustomer = Customer::factory()->for($account)->create([
            'name' => 'Globex Market',
            'email' => 'accounts@globex.test',
        ]);

        Invoice::factory()->for($firstCustomer)->create([
            'reference' => 'INV-1001',
            'amount_cents' => 35_000,
        ]);
        Invoice::factory()->for($secondCustomer)->create([
            'reference' => 'INV-1002',
            'amount_cents' => 47_500,
        ]);
        Invoice::factory()->for($secondCustomer)->settled()->create([
            'reference' => 'INV-0999',
            'amount_cents' => 12_000,
        ]);
    }
}
