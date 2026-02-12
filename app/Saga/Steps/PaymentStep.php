<?php
namespace App\Saga\Steps;
use App\Saga\Interfaces\SagaStep;
use Illuminate\Support\Facades\Log;

class PaymentStep implements SagaStep
{
    public function execute(array $data): bool
    {
        Log::info("Pagamento debitado: Pedido " . $data['order_id']);
        return true; // Sucesso
    }
    public function compensate(array $data): void
    {
        Log::info("Pagamento estornado: Pedido " . $data['order_id']);
    }
}