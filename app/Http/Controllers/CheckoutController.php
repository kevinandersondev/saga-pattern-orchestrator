<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Saga\SagaOrchestrator;
use App\Saga\Steps\PaymentStep;
use App\Saga\Steps\InventoryStep;
use App\Models\Order;
use App\Models\Product;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        // Cria produto se não existir (apenas para teste)
        if (Product::count() == 0) Product::create(['name' => 'PC Gamer', 'stock' => 100]);

        // Cria o pedido Pendente
        $order = Order::create(['amount' => 1000, 'status' => 'pending']);

        $data = [
            'order_id' => $order->id,
            'product_id' => 1,
            'quantity' => $request->input('quantity') // Se > 5, falha
        ];

        // Roda a Saga
        $saga = new SagaOrchestrator();
        $result = $saga->addStep(new PaymentStep())->addStep(new InventoryStep())->run($data);

        // Atualiza status final
        $order->update(['status' => ($result['status'] === 'success' ? 'confirmed' : 'cancelled')]);

        return response()->json($result);
    }
}