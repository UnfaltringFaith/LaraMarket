<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MyOrdersPage extends Component
{
    public $orders;
    
    public function render()
    {
        return view('livewire.my-orders-page');

    }

    public function mount()
    {
        $this->orders = Order::where('user_id', Auth::id())->get();
    }
}