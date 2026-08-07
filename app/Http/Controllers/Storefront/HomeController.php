<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\SettingsService;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Hero rotation cutouts lifted from the design prototype (assets/hero-*.png), each
     * pre-aligned to sit flush on the podium graphic. Matched to seeded demo products by slug.
     */
    private const HERO_SEQUENCE = [
        'oversized-graphic-tee' => ['image' => 'hero-tee.png', 'width' => 448, 'offset' => -172],
        'classic-snapback-cap' => ['image' => 'hero-cap.png', 'width' => 399, 'offset' => -148],
        'uptempo-retro-sneaker' => ['image' => 'hero-sneaker.png', 'width' => 490, 'offset' => -193],
        'essential-sweatshorts' => ['image' => 'hero-shorts.png', 'width' => 427, 'offset' => -162],
    ];

    public function index(SettingsService $settings): View
    {
        $featuredProducts = Product::with(['category', 'images', 'variants.size', 'variants.color'])
            ->active()
            ->featured()
            ->latest()
            ->take(4)
            ->get();

        if ($featuredProducts->count() < 4) {
            $featuredProducts = Product::with(['category', 'images', 'variants.size', 'variants.color'])
                ->active()
                ->latest()
                ->take(4)
                ->get();
        }

        $heroSlides = collect(self::HERO_SEQUENCE)
            ->map(fn ($slide, $slug) => [...$slide, 'slug' => $slug])
            ->filter(fn ($slide) => Product::where('slug', $slide['slug'])->where('is_active', true)->exists())
            ->values();

        return view('storefront.home', [
            'featuredProducts' => $featuredProducts,
            'heroSlides' => $heroSlides,
            'settings' => $settings->all(),
        ]);
    }
}
