<?php

namespace App\Livewire;

use App\Models\Brand;
use Livewire\Component;
use Livewire\Attributes\Title;
use Filament\Forms\Components\Livewire;

#[Title('Главная страница')]
class HomePage extends Component
{
    public function render()
    {
        $brands = Brand::where('is_active', 1)
            ->get();

        $categories = \App\Models\Category::where('is_active', 1)
            ->get();
        return view('livewire.home-page', compact('brands', 'categories'));
        // layout('components.layouts.app', ['title' => 'Главная']);
    }
}
