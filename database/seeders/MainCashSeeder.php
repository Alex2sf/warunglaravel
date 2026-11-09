<?php

namespace Database\Seeders;

use App\Models\CashBalance;
use App\Models\CashTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MainCashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $owner = User::where('role', 'owner')->first() 
                ?? User::where('email', 'fadli@gmail.com')->first()
                ?? User::first();

            $initialAmount = 100_000_000; // 100 Juta Rupiah

            // 1. Pastikan Kasir & Kas Besar ada di cash_balances
            CashBalance::firstOrCreate(
                ['type' => CashBalance::CASHIER],
                ['balance' => 0]
            );

            $mainBalance = CashBalance::firstOrCreate(
                ['type' => CashBalance::MAIN],
                ['balance' => 0]
            );

            // 2. Cek apakah transaksi modal awal kas besar sudah pernah dicatat
            $existingTx = CashTransaction::where('cash_type', 'MAIN')
                ->where('description', 'Modal Awal Kas Besar')
                ->first();

            if (!$existingTx) {
                // Catat transaksi mutasi kas agar audit trail & laporan keuangan valid
                CashTransaction::create([
                    'type' => CashTransaction::TYPE_DEPOSIT,
                    'cash_type' => 'MAIN',
                    'amount' => $initialAmount,
                    'description' => 'Modal Awal Kas Besar',
                    'recorded_by' => $owner?->id,
                    'on_behalf_of' => $owner?->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update saldo Kas Besar
                $mainBalance->update([
                    'balance' => $initialAmount,
                    'last_transaction_at' => now(),
                ]);
            } else {
                // Jika sudah ada catatan transaksi, sinkronkan saldo ke 100 juta
                $mainBalance->update([
                    'balance' => $initialAmount,
                    'last_transaction_at' => now(),
                ]);
            }
        });
    }
}
