<?php
// ================================================
// FILE: app/Http/Controllers/CatalogController.php
// FUNGSI: Menangani halaman katalog dan detail produk
// ================================================

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{

    /**
     * Menampilkan halaman katalog produk.
     * Mendukung filter: kategori, harga, pencarian, diskon, sorting.
     */
    public function index(Request $request)
    {
        // Base Query: produk aktif dan ada stok
        $query = Product::query()
            ->with(['category', 'primaryImage']) // Eager load relasi
            ->active()  // Scope: is_active = true
            ->inStock(); // Scope: stock > 0

        //  Filter: pencarian (nama & deskripsi)
        if ($request->filled('q')) {
            $query->search($request->q); // Scope di Model
        }

        //  Filter: kategori berdasarkan slug
        if ($request->filled('category')) {
            $query->byCategory($request->category); // Scope di Model
        }

        // Filter: rentang harga
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float)$request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float)$request->max_price);
        }

        //  Filter: produk sedang diskon
        if ($request->boolean('on_sale')) {
            $query->onSale(); // Scope: discount_price < price
        }

        // Sorting: default = terbaru
        $sort = $request->get('sort', 'newest');

        match($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            'name_desc'  => $query->orderBy('name', 'desc'),
            default      => $query->latest(), // terbaru berdasarkan created_at
        };

        //  Pagination: 12 produk per halaman
        // withQueryString() menjaga parameter filter di URL
        $products = $query->paginate(12)->withQueryString();

        //  Data sidebar: kategori aktif dengan produk
       $categories = Category::query()
            ->active()
            ->whereHas('activeProducts', function ($q) {
                $q->where('is_active', true)
                ->where('stock', '>', 0);
            })
            ->withCount(['activeProducts'])
            ->orderBy('name')
            ->get();


        // Kirim data ke view
        return view('catalog.index', compact('products', 'categories'));
    }


    /**
     * Menampilkan halaman detail produk.
     * Menggunakan Route Model Binding dengan slug.
     */
    public function show(string $slug)
    {
        // Cari produk berdasarkan slug
        $product = Product::query()
            ->with(['category', 'images']) // Load semua gambar
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail(); // 404 jika tidak ditemukan

        //Produk terkait: kategori sama, kecuali produk ini
        $relatedProducts = Product::query()
            ->with(['category', 'primaryImage'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->inStock()
            ->take(4)
            ->get();

        // Kirim data ke view
        return view('catalog.show', compact('product', 'relatedProducts'));
    }

    
    /**
 * Halaman Home – Produk unggulan per kategori
 */
    public function home()
{
    $categories = Category::query()
        ->active()
        ->with([
            'activeProducts' => function ($query) {
                $query->with('primaryImage')
                      ->latest()
                      ->limit(8);
            }
        ])
        ->whereHas('activeProducts')
        ->orderBy('name')
        ->get();
        
    $latestProducts = Product::query()
        ->active()
        ->inStock()
        ->with('primaryImage')
        ->latest()
        ->limit(8)
        ->get();

    return view('home', compact('categories', 'latestProducts'));
}

}