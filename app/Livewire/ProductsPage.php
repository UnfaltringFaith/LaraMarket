<?php

namespace App\Livewire;

use App\Livewire\Partials\Navbar;
use App\Models\Brand;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Services\CartService;
use Livewire\Attributes\Title;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

#[Title('Products Page')]
class ProductsPage extends Component
{
    use WithPagination;

    #[Url]
    public $selected_categories = [];

    #[Url]
    public $selected_brands = [];

    #[Url]
    public $is_featured = false;
    #[Url]
    public $on_sale = false;

    #[Url]
    public int $price_range = 500000;

    #[Url]
    public $sort_by = 'latest'; 

    public function mount()
    {
            $this->dispatch('cartUpdated',  CartService::getTotalCount())->to(Navbar::class);
    }
    public function addProductToCart($product_id)
    {
        $cartCount = CartService::addItemToCart($product_id);
        LivewireAlert::title('Item added to cart!')
            ->position('bottom-end')
            ->success()
            ->timer(1000)
            ->toast()
            ->show();

        $this->dispatch('cartUpdated',  $cartCount)->to(Navbar::class);
    }
    public function render()
    {

        $productQuery = Product::query()
            ->where('is_active', 1);

        if (!empty($this->selected_categories)) {
            $productQuery = $productQuery->whereHas('category', function($q) {
                $q->whereIn('slug', $this->selected_categories);
            });
        }

        if (!empty($this->selected_brands)) {
            $productQuery = $productQuery->whereHas('brand', function($q) {
                $q->whereIn('slug', $this->selected_brands);
            });
        }

        if ($this->is_featured) {
            $productQuery = $productQuery->where('is_featured', 1);
        }

        if ($this->on_sale) {
            $productQuery = $productQuery->where('on_sale', 1);
        }

        if  ($this->price_range > 0) {
            $productQuery = $productQuery->whereBetween('price', [1000, $this->price_range]);
        }

        if ($this->sort_by === 'price-high-to-low') {
            $productQuery = $productQuery->orderBy('price', 'desc');
        } 
        
        if ($this->sort_by === 'latest') {
            $productQuery = $productQuery->latest();
        }
        return view('livewire.products-page', [
            "products" => $productQuery->paginate(9),
            "categories" => Category::where('is_active', 1)->get(),
            "brands" => Brand::where('is_active', 1)->get(),
        ]);
    }
}
