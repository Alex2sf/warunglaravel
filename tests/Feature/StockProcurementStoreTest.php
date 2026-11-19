<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockProcurement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StockProcurementStoreTest extends TestCase
{
    use RefreshDatabase;

    private function product(string $name, int $cogs): Product
    {
        return Product::create([
            'name' => $name,
            'sku' => 'SKU-' . $name,
            'image_url' => '',
            'description' => '',
            'product_type' => 'CONSUMABLE',
            'unit' => 'pcs',
            'price' => $cogs + 1000,
            'cogs' => $cogs,
            'is_enabled' => true,
        ]);
    }

    public function test_second_same_day_submission_keeps_earlier_items(): void
    {
        $kasir = User::factory()->create(['role' => 'kasir']);
        Sanctum::actingAs($kasir);

        $a = $this->product('Bodrexin', 1000);
        $b = $this->product('Softex', 54000);
        $c = $this->product('Charm', 2000);

        $this->postJson('/api/procurements', ['items' => [
            ['product_id' => $a->id, 'quantity' => 10],
            ['product_id' => $b->id, 'quantity' => 6],
        ]])->assertOk();

        $this->postJson('/api/procurements', ['items' => [
            ['product_id' => $b->id, 'quantity' => 4],
            ['product_id' => $c->id, 'quantity' => 12],
        ]])->assertOk();

        $this->assertSame(1, StockProcurement::count());
        $procurement = StockProcurement::with('items')->first();
        $qty = $procurement->items->pluck('quantity_requested', 'product_id');

        $this->assertEquals([$a->id => 10, $b->id => 10, $c->id => 12], $qty->sortKeys()->all());
        $this->assertEquals(10 * 1000 + 10 * 54000 + 12 * 2000, (int) $procurement->total_estimated_amount);
    }
}
