<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'users'           => User::count(),
            'products'        => Product::count(),
            'categories'      => Category::count(),
            'total_orders'    => Order::count(),
            'total_revenue'   => Order::sum('total_amount'),
            'pending_orders'  => Order::where('status', 'pending')->count(),
            'total_products'  => Product::count() ?? 0,
            'low_stock'       => Product::where('stock', '<', 5)->count(),
        ];

        $topProducts = Product::withCount(['orderItems as sold' => function ($q) {
                // Kita hanya hitung item yang berasal dari order yang SUDAH DIBAYAR (paid)
                $q->select(DB::raw('SUM(quantity)'))
                  ->whereHas('order', function($query) {
                      $query->where('payment_status', 'paid');
                  });
            }])
            ->having('sold', '>', 0) // Filter: Hanya tampilkan yang pernah terjual
            ->orderByDesc('sold')    // Urutkan dari yang paling laku
            ->take(5)
            ->get();

        // 4. Data Grafik Pendapatan (7 Hari Terakhir)
        // Kasus: Grouping data per tanggal
        // Kita gunakan DB::raw untuk format tanggal dari timestamp 'created_at'
        $revenueChart = Order::select([
                DB::raw('DATE(created_at) as date'), // Ambil tanggalnya saja (2024-12-10)
                DB::raw('SUM(total_amount) as total') // Total omset hari itu
            ])
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(7)) // Filter 7 hari ke belakang
            ->groupBy('date') // Kelompokkan baris berdasarkan tanggal
            ->orderBy('date', 'asc') // Urutkan kronologis
            ->get();


        // Ambil 5 order terbaru
        $recentOrders = Order::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'topProducts', 'revenueChart'));
    }
}