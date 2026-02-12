<?php
namespace App\Saga\Steps;
use App\Saga\Interfaces\SagaStep;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class InventoryStep implements SagaStep
{
    public function execute(array $data): bool
    {
        $product = Product::find($data['product_id']);
        // Simula falha se pedir mais de 5 itens
        if (!$product || $data['quantity'] > 5) {
            Log::error("Estoque insuficiente.");
            return false; 
        }
        $product->decrement('stock', $data['quantity']);
        return true;
    }
    public function compensate(array $data): void
    {
        Log::info("Estoque devolvido.");
        Product::find($data['product_id'])?->increment('stock', $data['quantity']);
    }
}