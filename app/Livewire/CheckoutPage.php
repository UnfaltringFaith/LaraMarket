<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Checkout Page')]
class CheckoutPage extends Component
{
    public array $cartItems = [];
    public int $grandTotal = 0;

    public string $first_name = '';
    public string $last_name = '';
    public string $phone = '';
    public string $address = '';
    public string $city = '';
    public string $state = '';
    public string $zip = '';
    public string $payment_method = '';

    public function render()
    {

        return view('livewire.checkout-page');
    }

    public function mount()
    {
        $this->cartItems = CartService::getCartItemsFromCookie();
        $this->grandTotal = CartService::calculateGrandTotal($this->cartItems);

        // Implement checkout logic here
    }

    public function checkout()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'required|string|max:20',
            'payment_method' => 'required|string|in:CoD,Stride', // Adjust as needed
        ]);

        // Logic to handle order placement will go here
        // For example, saving the order to the database, sending confirmation emails, etc.

        session()->flash('success', 'Order placed successfully!');
        return redirect()->route('home');
    }
}
