<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Services\CartService;
use Livewire\Attributes\Title;
use App\Livewire\Partials\Navbar;
use Illuminate\Support\Facades\Cookie;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;


#[Title('Product Page')]
class ProductPage extends Component
{
    public $slug;

    public int $itemCount = 0;

    public function mount($slug)
    {
        $this->slug = $slug;

        $this->itemCount = CartService::getTotalCount();

        $this->dispatch('cartUpdated',  $this->itemCount)->to(Navbar::class);
    }

    public function updatedItemCount($value)
    {
        dd($value);
    }

    public function addProductToCart($product_id)
    {
        $this->itemCount = (int) CartService::addItemToCart($product_id);
        LivewireAlert::title('Item added to cart!')
            ->position('bottom-end')
            ->success()
            ->timer(1000)
            ->toast()
            ->show();

        $this->dispatch('cartUpdated',  $this->itemCount)->to(Navbar::class);
    }

    public function incrementQuantity($product_id)
    {
        $cartItems = CartService::getCartItemsFromCookie();
        foreach ($cartItems as &$item) {
            if ($item['product_id'] === $product_id) {
                $item['quantity']++;
                $item['total_amount'] = $item['quantity'] * $item['price'];
                $this->itemCount++;
                break;
            }
        }

        CartService::addCartItemsToCookie($cartItems);

        $this->dispatch('cartUpdated',  $this->itemCount)->to(Navbar::class);

    }

    public function decrementQuantity($product_id)
    {
        $cartItems = CartService::getCartItemsFromCookie();
        foreach ($cartItems as &$item) {
            if ($item['product_id'] === $product_id) {
                $item['quantity']--;
                $item['total_amount'] = $item['quantity'] * $item['price'];
                $this->itemCount--;
                break;
            }
        }
        
        CartService::addCartItemsToCookie($cartItems);


        $this->dispatch('cartUpdated',  $this->itemCount)->to(Navbar::class);
    }

    public function render()
    {
        return view('livewire.product-page', [
            'product' => Product::where('slug', $this->slug)->firstOrFail()
        ]);
    }
}
