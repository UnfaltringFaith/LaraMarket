<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CartService;
use Livewire\Attributes\Title;
use App\Livewire\Partials\Navbar;

#[Title('Cart Page')]
class CartPage extends Component
{
    public array $cartItems = [];
    public int $item_count = 0;
    public int $grandTotal = 0;

    public function mount()
    {
        $this->cartItems = CartService::getCartItemsFromCookie();
        $this->item_count = CartService::getTotalCount();
        $this->grandTotal = CartService::calculateGrandTotal($this->cartItems);
    }
    public function render()
    {
        return view('livewire.cart-page');
    }

    public function removeFromCart(int $productId)
    {
        $this->cartItems = (array) CartService::removeCartItem($productId);
        $this->item_count = CartService::getTotalCount();
        $this->grandTotal = CartService::calculateGrandTotal($this->cartItems);
        $this->dispatch('cartUpdated', $this->item_count)->to(Navbar::class);
    }

    public function increaseQuantity(int $productId)
    {
        $this->cartItems = (array) CartService::incrementQuantityToCartItem($productId);
        $this->item_count = CartService::getTotalCount();
        $this->grandTotal = CartService::calculateGrandTotal($this->cartItems);
        $this->dispatch('cartUpdated', $this->item_count)->to(Navbar::class);
    }

    public function decreaseQuantity(int $productId)
    {
        $this->cartItems = (array) CartService::decrementQuantityToCartItem($productId);
        $this->item_count = CartService::getTotalCount();
        $this->grandTotal = CartService::calculateGrandTotal($this->cartItems);
        $this->dispatch('cartUpdated', $this->item_count)->to(Navbar::class);
    }
    public function clearCart()
    {
        CartService::clearCartItems();
        $this->cartItems = [];
        $this->item_count = 0;
        $this->grandTotal = 0;
        $this->dispatch('cartUpdated', $this->item_count)->to(Navbar::class);
    }
}
