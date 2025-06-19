<?php

namespace Database\Seeders;

use App\Models\Invoice;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InvoiceSeeder extends Seeder
{
    
    public function run()
    {
        Invoice::create([
            'customer_name' => 'John Doe',
            'event_name' => 'Wedding Ceremony',
            'invoice_date' => now(),
            'total_amount' => 1000,
            'paid_amount' => 0,
            'status' => 'unpaid',
        ]);

        Invoice::create([
            'customer_name' => 'Acme Corp',
            'event_name' => 'Corporate Meeting',
            'invoice_date' => now()->subDays(10),
            'total_amount' => 2500,
            'paid_amount' => 1500,
            'status' => 'partial',
        ]);
    }
}
