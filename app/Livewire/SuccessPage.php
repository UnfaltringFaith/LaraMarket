<?php

namespace App\Livewire;

use YooKassa\Client;
use App\Models\Order;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SuccessPage extends Component
{
    public string $status;
    public int $amount;
    public string $currency;
    public string $paymentMethod;
    public string $card;
    public string $paymentDate;
    public string $paid;

    public $order;

    public function render()
    {
        return view('livewire.success-page');
    }

    public function mount()
    {
        $client = new Client();
        $client->setAuth('1119983', 'test_D_cKkUQ0NXorcE9I4zNyNWxm5dMwg0gDOxMtKqD997Y');

        $paymentId = session('payment_id');
        $payment = $client->getPaymentInfo($paymentId);

        $this->order = Order::where('payment_id', $paymentId)->first();
    }
}
