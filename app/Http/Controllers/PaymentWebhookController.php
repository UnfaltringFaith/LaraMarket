<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Простое логирование для тестирования
        Log::info('=== WEBHOOK TEST START ===');
        Log::info('Request method: ' . $request->method());
        Log::info('Request URL: ' . $request->fullUrl());
        Log::info('Request headers: ', $request->headers->all());
        Log::info('Request body: ', $request->all());
        Log::info('Raw input: ' . $request->getContent());
        Log::info('=== WEBHOOK TEST END ===');

        // Простой ответ
        return response()->json([
            'status' => 'received',
            'timestamp' => now()->toISOString()
        ], 200);
    }

    private function handlePaymentSucceeded($payment)
    {
        Log::info('Payment succeeded', ['payment' => $payment]);
        
        // Находим заказ по payment_id из metadata или другим способом
        $paymentId = $payment['id'];
        $metadata = $payment['metadata'] ?? [];
        
        // Если в metadata есть order_id
        if (isset($metadata['order_id'])) {
            $order = Order::find($metadata['order_id']);
            if ($order) {
                $order->update([
                    'status' => 'paid',
                    'payment_id' => $paymentId,
                    'payment_status' => 'succeeded'
                ]);
                
                Log::info('Order status updated to paid', ['order_id' => $order->id]);
                
                // Здесь можно добавить отправку email уведомления
                // Mail::to($order->user->email)->send(new OrderPaid($order));
            }
        }
    }

    private function handlePaymentCanceled($payment)
    {
        Log::info('Payment canceled', ['payment' => $payment]);
        
        $paymentId = $payment['id'];
        $metadata = $payment['metadata'] ?? [];
        
        if (isset($metadata['order_id'])) {
            $order = Order::find($metadata['order_id']);
            if ($order) {
                $order->update([
                    'payment_status' => 'canceled'
                ]);
                
                Log::info('Order payment canceled', ['order_id' => $order->id]);
            }
        }
    }

    private function handlePaymentWaitingForCapture($payment)
    {
        Log::info('Payment waiting for capture', ['payment' => $payment]);
        
        // Здесь можно добавить логику для двухстадийных платежей
    }
}
