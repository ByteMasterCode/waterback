<?php

namespace App\Http\Controllers;

use App\Models\HistoryManager;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{


    public function index(){
        $orders = Order::with('user')
            ->latest() // Сортировка по наиболее позднему времени создания (последним добавленным)
            ->get();

        // Возврат списка заказов в формате JSON
        return response()->json($orders);
    }

    public function update(Request $request, $id)
    {
        try {
            // Находим заказ по его ID
            $order = Order::findOrFail($id);

            // Валидация входных данных
            $request->validate([
                'date' => 'sometimes|date',
                'user_id' => 'sometimes|exists:users,id',
                'address' => 'nullable|string',
                'location' => 'nullable|json',
                'accept_date' => 'nullable|date',
                'delivered_date' => 'nullable|date',
                'is_denied' => 'nullable|boolean',
                'description' => 'nullable|string',
                'status' => 'nullable|string|in:on hold,pending,completed',
                'total' => 'sometimes|numeric|min:0',
                'cashback' => 'nullable|numeric|min:0',
            ]);

            // Обновляем данные заказа
            $order->fill($request->all())->save();

            // Обновляем продукты заказа
            if ($request->has('products')) {
                $total_price = 0;
                foreach ($request->products as $productData) {
                    $orderProduct = OrderProduct::where('order_id', $id)
                        ->where('product_id', $productData['product_id'])
                        ->first();

                    if ($orderProduct) {
                        // Обновляем количество продукта
                        $orderProduct->count = $productData['count'];
                        $orderProduct->save();
                        // Получаем цену продукта и добавляем ее к общей стоимости
                        $product = Product::findOrFail($productData['product_id']);
                        $total_price += $product->price * $productData['count'];
                    }
                }
                // Обновляем общую стоимость заказа
                $order->total = $total_price;
                $order->save();
            }

            // Возвращаем успешный ответ
            return response()->json(['message' => 'Order updated successfully'], 200);
        } catch (\Exception $e) {
            // Возвращаем сообщение об ошибке
            return response()->json(['error' => 'Failed to update order'], 500);
        }
    }


    public function destroy($id)
    {
        try {
            // Находим заказ по его ID
            $order = Order::findOrFail($id);

            // Удаляем заказ из базы данных
            $order->delete();
            HistoryManager::create([
                'actions' => 'deleted',
                'description' => 'Buyurtma o`chirildi |  vaqti : '.$order->date.' address : '.$order->address.' tartib raqami : '.$order->id,
            ]);
            // Возвращаем успешный ответ
            return response()->json(['message' => 'Order deleted successfully'], 200);
        } catch (\Exception $e) {
            // Возвращаем сообщение об ошибке
            return response()->json(['error' => 'Failed to delete order'], 500);
        }
    }



}
