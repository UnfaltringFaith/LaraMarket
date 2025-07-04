<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Services\CartService;
use Illuminate\Support\Facades\Session;

class Navbar extends Component
{
    /**
     * The number of items in the cart.
     *
     * @var int
     */
    public $cartCount = 0;

    public function mount()
    {
        // Initialize cart count from cookie or session
        $this->cartCount = CartService::getTotalCount();
    }

    /**
     * Listen for cart updates and update the cart count.
     *
     * @param  array  $event
     * @return void
     */
    #[On('cartUpdated')]
    public function updateCartCount($count)
    {
        $this->cartCount = $count;
    }

    public function render()
    {
        return view('livewire.partials.navbar');
    }
}
