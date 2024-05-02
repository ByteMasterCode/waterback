<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\HistoryManager;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getOrderCountsAndLastTenOrders()
    {
        // Получаем общее количество заказов
        $totalOrders = Order::count();

        // Получаем количество заказов с каждым статусом
        $pendingOrdersCount = Order::where('status', 'pending')->count();
        $onHoldOrdersCount = Order::where('status', 'on hold')->count();
        $completedOrdersCount = Order::where('status', 'completed')->count();

        // Получаем последние 10 заказов
        $lastTenOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Возвращаем данные в формате JSON
        return response()->json([
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrdersCount,
            'on_hold_orders' => $onHoldOrdersCount,
            'completed_orders' => $completedOrdersCount,
            'last_ten_orders' => $lastTenOrders,
        ]);
    }

    public function getLastTenHistory()
    {
        // Получаем последние 10 записей истории
        $lastTenHistory = HistoryManager::latest()
            ->take(10)
            ->get();

        // Возвращаем данные в формате JSON
        return response()->json($lastTenHistory);
    }

    public function getProductsCount()
    {
        // Получаем последние 10 записей истории
        $count = Product::all()->count();

        // Возвращаем данные в формате JSON
        return response()->json($count);
    }

    public function getBrandsCount()
    {
        // Получаем последние 10 записей истории
        $count = Brand::all()->count();

        // Возвращаем данные в формате JSON
        return response()->json($count);
    }

    public function getCategoryCount()
    {
        // Получаем последние 10 записей истории
        $count = Category::all()->count();

        // Возвращаем данные в формате JSON
        return response()->json($count);
    }

    public function getUsersCount()
    {
        // Получаем последние 10 записей истории
        $count = User::all()->count();

        // Возвращаем данные в формате JSON
        return response()->json($count);
    }


}
