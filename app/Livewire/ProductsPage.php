<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

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
    public int $price_range = 1000;

    #[Url]
    public $sort_by = 'latest';

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
