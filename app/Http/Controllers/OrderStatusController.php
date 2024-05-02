<?php

namespace App\Http\Controllers;

use App\Models\HistoryManager;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderStatusController extends Controller
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
                'description' => 'nullable|string',
                'status' => 'nullable|string|in:on hold,pending,completed',
                'products' => 'required|array',
                'products.*.product_id' => 'required|exists:products,id', // Проверяем, что каждый идентификатор продукта существует
                'products.*.count' => 'required|integer|min:1', // Проверяем, что количество продукта целое положительное число
            ]);


            // Создание нового заказа
            $order = new Order();
            $order->date = $request->date;
            $order->user_id = $request->user_id;
            $order->address = $request->address;
            $order->location = $request->location;
            $order->is_denied = false;
            $order->total = 0;
            $order->description = $request->description;
            $order->status = $request->status ?? 'on hold';
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

            HistoryManager::create([
                'actions' => 'created',
                'description' => 'Buyurtma yaratildi mahalliy yaratildi | | | vaqdi : '.$order->date.' address : '.$order->address.' tartib raqami : '.$order->id,
            ]);

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


    public function pendingOrder(Request $request)
    {
        try {

            // Получаем ID заказа из запроса
            $orderId = $request->input('order_id');

            // Находим заказ по его ID
            $order = Order::findOrFail($orderId);

            // Меняем статус заказа на "pending"
            $order->status = 'pending';
            $order->save();
            if (Auth::check()) {
                // Получаем аутентифицированного пользователя через Auth::user()
                $user = Auth::user();
                HistoryManager::create([
                    'actions' => 'pending',
                    'description' => 'Buyurtma qabul qilindi | | | vaqdi : '.$order->date.' address : '.$order->address.' tartib raqami : '.$order->id.' qabul qilgan foydalanuvchi :'.$user->fullname,
                ]);
            } else {
                // Возвращаем сообщение, если пользователь не авторизован
                return response()->json(['message' => 'User is not authenticated'], 403);
            }
            // Возвращаем успешный ответ
            return response()->json(['message' => 'Order status changed to pending'], 200);
        } catch (\Exception $e) {
            // Возвращаем сообщение об ошибке
            return response()->json(['error' => 'Failed to change order status'], 500);
        }
    }

    public function holdOrder(Request $request)
    {

        try {


            // Получаем ID заказа из запроса
            $orderId = $request->input('order_id');

            // Находим заказ по его ID
            $order = Order::findOrFail($orderId);

            // Меняем статус заказа на "pending"
            $order->status = 'on hold';
            $order->save();
            if (auth()->check()) {
                // Получаем аутентифицированного пользователя через auth()->user() или JWTAuth::user()
                $user = auth()->user();
                HistoryManager::create([
                    'actions' => 'hold',
                    'description' => 'Buyurtma kutish vaqtiga o`zgardi  | | | vaqdi : '.$order->date.' address : '.$order->address.' tartib raqami : '.$order->id.' o`zgartigan foydalanuvchi :'.$user->fullname,
                ]);
            } else {
                // Возвращаем сообщение, если пользователь не авторизован
                return response()->json(['message' => 'User is not authenticated'], 403);
            }
            // Возвращаем успешный ответ
            return response()->json(['message' => 'Order status changed to hold'], 200);
        } catch (\Exception $e) {
            // Возвращаем сообщение об ошибке
            return response()->json(['error' => 'Failed to change order status'], 500);
        }
    }

    public function completedOrder(Request $request)
    {
        try {

            // Получаем ID заказа из запроса
            $orderId = $request->input('order_id');

            // Находим заказ по его ID
            $order = Order::findOrFail($orderId);

            // Меняем статус заказа на "pending"
            $order->status = 'completed';
            $order->save();
            if (Auth::check()) {
                // Получаем аутентифицированного пользователя через Auth::user()
                $user = Auth::user();
                HistoryManager::create([
                    'actions' => 'completed',
                    'description' => 'Buyurtma yetkazib berildi | | | vaqdi : '.$order->date.' address : '.$order->address.' tartib raqami : '.$order->id.' yetkazib bergan foydalanuvchi :'.$user->fullname,
                ]);
            } else {
                // Возвращаем сообщение, если пользователь не авторизован
                return response()->json(['message' => 'User is not authenticated'], 403);
            }
            // Возвращаем успешный ответ
            return response()->json(['message' => 'Order status changed to completed'], 200);
        } catch (\Exception $e) {
            // Возвращаем сообщение об ошибке
            return response()->json(['error' => 'Failed to change order status'], 500);
        }
    }

    public function getTotalCompletedOrdersLastWeek()
    {
        // Определение даты начала и конца предыдущих 7 дней
        $startOfLast7Days = Carbon::now()->subDays(7)->startOfDay();
        $endOfLast7Days = Carbon::now()->endOfDay();

        // Выборка всех заказов, завершенных за предыдущие 7 дней и с суммированием total
        $totalCompleted = Order::whereBetween('created_at', [$startOfLast7Days, $endOfLast7Days])
            ->where('status', 'completed')
            ->sum('total');

        // Возврат общей суммы в формате JSON
        return response()->json(['total_earnings_last_7_days' => $totalCompleted]);
    }

    public function getTotalpendingOrdersLastWeek()
    {
        // Определение даты начала и конца предыдущих 7 дней
        $startOfLast7Days = Carbon::now()->subDays(7)->startOfDay();
        $endOfLast7Days = Carbon::now()->endOfDay();

        // Выборка всех заказов, завершенных за предыдущие 7 дней и с суммированием total
        $totalCompleted = Order::whereBetween('created_at', [$startOfLast7Days, $endOfLast7Days])
            ->where('status', 'pending')
            ->sum('total');

        // Возврат общей суммы в формате JSON
        return response()->json(['total_earnings_last_7_days' => $totalCompleted]);
    }

    public function getTotalHoldOrdersLastWeek()
    {
        // Определение даты начала и конца предыдущих 7 дней
        $startOfLast7Days = Carbon::now()->subDays(7)->startOfDay();
        $endOfLast7Days = Carbon::now()->endOfDay();

        // Выборка всех заказов, завершенных за предыдущие 7 дней и с суммированием total
        $totalCompleted = Order::whereBetween('created_at', [$startOfLast7Days, $endOfLast7Days])
            ->where('status', 'on hold')
            ->sum('total');

        // Возврат общей суммы в формате JSON
        return response()->json(['total_earnings_last_7_days' => $totalCompleted]);
    }
    private function generateSerial()
    {
        // Генерация уникального серийного номера (можете изменить по своему усмотрению)
        return strtoupper(substr(md5(uniqid()), 0, 8));
    }

}
