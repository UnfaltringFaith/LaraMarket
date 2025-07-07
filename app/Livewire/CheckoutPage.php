<?php

namespace App\Livewire;

use YooKassa\Client;
use App\Models\Order;
use Livewire\Component;
use App\Services\CartService;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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

    public string $status;
    public int $amount;
    public string $currency;
    public string $paymentMethod;
    public string $card;
    public string $paymentDate;
    public string $paid;

    public function render()
    {
        return view('livewire.checkout-page');
    }

    public function mount()
    {
        $this->cartItems = CartService::getCartItemsFromCookie();
        $this->grandTotal = CartService::calculateGrandTotal($this->cartItems);
        $this->payment_method = 'CoD'; // Default payment method
    }

    public function checkout()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'required|string|max:20',
            'payment_method' => 'required|string|in:CoD,Stride', // Adjust as needed
        ]);

        $client = new Client();
        $client->setAuth('1119983', 'test_D_cKkUQ0NXorcE9I4zNyNWxm5dMwg0gDOxMtKqD997Y');

        $payment = $client->createPayment(
            array(
                'amount' => array(
                    'value' => $this->grandTotal, // Assuming grand total is in cents
                    'currency' => 'RUB',
                ),
                'confirmation' => array(
                    'type' => 'redirect',
                    'return_url' => url('/success'),
                ),
                'capture' => true,
                'description' => 'Заказ №1',
                'metadata' => array(
                    'order_id' => uniqid('', true)
                )
            ),
            
        );
        
        
        session(['payment_id' => $payment->id]);
        
        Mail::to(Auth::user()->email)->send(new \App\Mail\OrderPaid(Order::find(1)));

        $order = Order::create([
            'user_id' => Auth::id(),
            'grand_total' => $payment->amount->value,
            'status' => 'new',
            'currency' => $payment->amount->currency,
            'shipping_amount' => 0,
            'shipping_method' => 'test',
            'notes' => null,
            'payment_id' => $payment->id, // Save payment ID for later reference
        ]);


        session(['payment_id' => $payment->id]);
        
        return redirect($payment->confirmation->ConfirmationUrl ?: 'https://www.example.com/return_url');
        

        // Logic to handle order placement will go here
        // For example, saving the order to the database, sending confirmation emails, etc.

        session()->flash('success', 'Order placed successfully!');
        return redirect()->route('home');
    }
}
