<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Jadikan phone_number customer boleh null (untuk pelanggan umum/walk-in warung)
        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone_number', 20)->nullable()->change();
        });

        // 2. Tambah kolom delivery_scheduled_at jika belum ada di tabel orders
        if (!Schema::hasColumn('orders', 'delivery_scheduled_at')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->timestamp('delivery_scheduled_at')->nullable()->after('completed_time');
            });
        }

        // 3. Pastikan data Pelanggan Umum tersedia
        DB::table('customers')->updateOrInsert(
            ['name' => 'Pelanggan Umum'],
            [
                'phone_number' => null,
                'address' => '-',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'delivery_scheduled_at')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('delivery_scheduled_at');
            });
        }

        DB::table('customers')->whereNull('phone_number')->update(['phone_number' => '-']);

        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone_number', 20)->nullable(false)->change();
        });
    }
};
