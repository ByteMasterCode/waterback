<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\CourierCard;
use App\Models\HistoryManager;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrderStatusController extends Controller
{
    public function makeOrder(Request $request)
    {
        try {
            // Валидация входных данных
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'address' => 'nullable|string',
                'location' => 'nullable|json',
                'courier_id' => 'nullable|exists:users,id',
                'description' => 'nullable|string',
                'status' => 'nullable|string|in:on hold,pending,completed',
                'products' => 'required|array',
                'products.*.product_id' => 'required|exists:products,id', // Проверяем, что каждый идентификатор продукта существует
                'products.*.count' => 'required|integer|min:1', // Проверяем, что количество продукта целое положительное число
            ]);


            // Создание нового заказа
            $order = new Order();
            $order->date = Carbon::now();
            $order->user_id = $request->user_id;
            $order->address = $request->address;
            $order->location = $request->location;
            $order->courier_id = $request->courier_id ?? null;
            $order->is_denied = false;
            $order->total = 0;
            $order->description = $request->description;
            $order->status = $request->status ?? 'on hold';
            $order->save();
            $total_price = 0;
            $total_count= 0;
            $order_decription ='Maxsulotlar ro`yxati : \n';

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
                $order_decription.= 'nomi : '.$product->name.' narxi : '.$product->price.' soni : '.$productData['count'].' summa = '.($product->price * $productData['count']).' so`m';
                // Добавляем количество продукта к общему количеству
                $total_count += $productData['count'];
            }
            $order->total = $total_price;
            $order->description.= $order_decription;
            $order->save();

            HistoryManager::create([
                'actions' => 'created',
                'description' => 'Buyurtma yaratildi | | | vaqdi : '.$order->date.' address : '.$order->address.' tartib raqami : '.$order->id,
            ]);

            DB::commit();

            // Возвращаем успешный ответ
            return response()->json(['message' => 'Order created successfully',$order], 201);
        } catch (\Exception $e) {
            // В случае ошибки откатываем транзакцию
            DB::rollBack();

            // Возвращаем сообщение об ошибке
            return response()->json(['error' => 'Failed to create order'.$e], 500);
        }
    }


    public function pendingOrder(Request $request)
    {
        try {
            // Получаем ID заказа из запроса
            $orderId = $request->input('order_id');

            // Находим заказ по его ID
            $order = Order::findOrFail($orderId);
            $order->accept_date = Carbon::now();

            // Меняем статус заказа на "pending"
            $order->status = 'pending';

            // Получаем аутентифицированного пользователя через Auth::user()
            if ($request->has('telegram_id')) {
                $user = User::where('telegram_id', $request->input('telegram_id'))->first();
                if ($user && $user->role == 'courier') {
                    $order->courier_id = $user->id;
                } else {
                    return response()->json(['message' => 'User is not authorized as courier'], 403);
                }
            } else {
                if (Auth::check()) {
                    $currentUser = Auth::user();
                    if ($currentUser->role == 'courier') {
                        $order->courier_id = $currentUser->id;
                    }
                } else {
                    // Возвращаем сообщение, если пользователь не авторизован
                    return response()->json(['message' => 'User is not authenticated'], 403);
                }
            }

            $order->save();

            // Создаем запись в истории
            HistoryManager::create([
                'actions' => 'pending',
                'description' => 'Buyurtma qabul qilindi | | | vaqdi : '.$order->date.' address : '.$order->address.' tartib raqami : '.$order->id.' qabul qilgan foydalanuvchi :'.$currentUser->fullname,
            ]);

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

            // Меняем статус заказа на "completed"
            $order->status = 'completed';

            // Получаем аутентифицированного пользователя через Auth::user()
            if ($request->has('telegram_id')) {
                $user = User::where('telegram_id', $request->input('telegram_id'))->first();
                if ($user && $user->role == 'courier') {
                    $order->courier_id = $user->id;
                } else {
                    return response()->json(['message' => 'User is not authorized as courier'], 403);
                }
            } else {
                if (Auth::check()) {
                    $user = Auth::user();
                    if ($user->role == 'courier') {
                        $order->courier_id = $user->id;
                    }
                } else {
                    // Возвращаем сообщение, если пользователь не авторизован
                    return response()->json(['message' => 'User is not authenticated'], 403);
                }
            }

            $order->save();

            // Создаем запись в истории
            HistoryManager::create([
                'actions' => 'completed',
                'description' => 'Buyurtma yetkazib berildi | | | vaqdi : '.$order->date.' address : '.$order->address.' tartib raqami : '.$order->id.' yetkazib bergan foydalanuvchi :'.$user->fullname,
            ]);

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


    public function acceptOrder(Request $request, $orderId)
    {

        // Находим заказ по переданному id
        $order = Order::find($orderId);

        // Проверяем, был ли найден заказ
        if (!$order) {
            // Если заказ не найден, возвращаем ошибку 404
            return response()->json(['error' => 'Order not found'], 404);
        }
        if ($order->courier_id !== null) {
            return response()->json(['message'=>'Заказ уже был принят'],400);
        }
        // Проверяем, авторизован ли пользователь и имеет ли он роль курьера
        $user = Auth::user();
        if (!$user->hasRole('courier') || !$user) {
            // Если пользователь не авторизован или не является курьером, возвращаем ошибку 403 (Forbidden)
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $card = CourierCard::find($user->id);

        if ($card->status !== 'active'){
            return  response()->json(['message'=>'У вас статус не активирован!'],403);
        }
        // Получаем информацию о курьере
        $courierName = $user->fullname;
        $courierPhoneNumber = $user->phoneNumber;

        // Обновляем описание заказа
        $order->description = "Ваш заказ принят курьером. Курьер: $courierName, Телефон: $courierPhoneNumber.  \n".$order->description;

        // Получаем id курьера
        $courierId = $user->id;

        // Обновляем заказ, устанавливая courier_id в id курьера
        $order->courier_id = $courierId;
        $order->save();

        // Возвращаем успешный ответ и обновленные данные заказа
        return response()->json(['message' => 'Order accepted successfully', 'order' => $order], 200);
    }

    public function changeOrderCourier(Request $request, $orderId,$courierId)
    {

        // Находим заказ по переданному id
        $order = Order::find($orderId);

        // Проверяем, был ли найден заказ
        if (!$order) {
            // Если заказ не найден, возвращаем ошибку 404
            return response()->json(['error' => 'Order not found'], 404);
        }

//        $user = Auth::user();
//        if (!$user->hasRole('admin')) {
//            // Если пользователь не авторизован или не является курьером, возвращаем ошибку 403 (Forbidden)
//            return response()->json(['error' => 'Unauthorized'], 403);
//        }
        $courier = User::find($courierId);
        // Получаем информацию о курьере
        $courierName = $courier->fullname;
        $courierPhoneNumber = $courier->phoneNumber;

        // Обновляем описание заказа
        $order->description = "Ваш заказ принят курьером. Курьер: $courierName, Телефон: $courierPhoneNumber.  ".$order->description;
        $order->status = 'pending';
        // Получаем id курьера
        $courierId =$courier->id;

        // Обновляем заказ, устанавливая courier_id в id курьера
        $order->courier_id = $courierId;
        $order->save();

        // Возвращаем успешный ответ и обновленные данные заказа
        return response()->json(['message' => 'Order courier changed successfully', 'order' => $order], 200);
    }



    private function generateSerial()
    {
        // Генерация уникального серийного номера (можете изменить по своему усмотрению)
        return strtoupper(substr(md5(uniqid()), 0, 8));
    }



}
