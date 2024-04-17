<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function makeOrder(Request $request)
    {
        try {
            // Валидация входных данных
            $request->validate([
                'date' => 'required|date',
                'user_id' => 'required|exists:users,id',
                'address' => 'nullable|string',
                'location' => 'nullable|json',
                'accept_date' => 'nullable|date',
                'delivered_date' => 'nullable|date',
                'is_denied' => 'nullable|boolean',
                'description' => 'nullable|string',
                'status' => 'nullable|string|in:on hold,pending,completed', // Допустимые значения для статуса заказа
                'total' => 'required|numeric|min:0',
                'cashback' => 'nullable|numeric|min:0',
                'products' => 'required|array', // Проверяем, что входные данные для продуктов - массив
                'products.*.product_id' => 'required|exists:products,id', // Проверяем, что каждый идентификатор продукта существует
                'products.*.count' => 'required|integer|min:1', // Проверяем, что количество продукта целое положительное число
            ]);


            // Создание нового заказа
            $order = new Order();
            $order->date = $request->date;
            $order->user_id = $request->user_id;
            $order->address = $request->address;
            $order->location = $request->location;
            $order->accept_date = $request->accept_date;
            $order->delivered_date = $request->delivered_date;
            $order->is_denied = $request->is_denied ?? false;
            $order->description = $request->description;
            $order->status = $request->status ?? 'on hold';
            $order->total = $request->total;
            $order->cashback = $request->cashback;

            $order->save();
            $total_price = 0;
            $total_count= 0;
            foreach ($request->products as $productData) {
                $orderProduct = new OrderProduct();
                $orderProduct->product_id = $productData['product_id'];
                $orderProduct->count = $productData['count'];
                $orderProduct->serial = $this->generateSerial(); // Генерируем серийный номер
                $orderProduct->order_id = $order->id;
                $orderProduct->save();
                // Получаем цену продукта и добавляем ее к общей стоимости
                $product = Product::findOrFail($productData['product_id']);
                $total_price += $product->price * $productData['count'];
                // Добавляем количество продукта к общему количеству
                $total_count += $productData['count'];
            }
            $order->total = $total_price;
            $order->save();
            DB::commit();

            // Возвращаем успешный ответ
            return response()->json(['message' => 'Order created successfully'], 201);
        } catch (\Exception $e) {
            // В случае ошибки откатываем транзакцию
            DB::rollBack();

            // Возвращаем сообщение об ошибке
            return response()->json(['error' => 'Failed to create order'], 500);
        }
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


    private function generateSerial()
    {
        // Генерация уникального серийного номера (можете изменить по своему усмотрению)
        return strtoupper(substr(md5(uniqid()), 0, 8));
    }
}
