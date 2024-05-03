<?php

namespace App\services;

use App\Models\CourierCard;

class CourierCardController
{

    /**
     * Создать карту курьера для указанного пользователя.
     *
     * @param int $userId
     * @return CourierCard
     */
    public static function createCourierCard($userId)
    {
        return CourierCard::create([
            'user_id' => $userId,
        ]);
    }

    /**
     * Добавить order_id в массив current_orders курьера.
     *
     * @param int $userId
     * @param int $orderId
     * @return bool
     */
    public static function addOrderToCurrentOrders($userId, $orderId)
    {
        $courierCard = self::findCourierCardByUserId($userId);

        if ($courierCard) {
            $currentOrders = $courierCard->current_orders ?? [];
            $currentOrders[] = $orderId;
            $courierCard->current_orders = $currentOrders;
            $courierCard->save();
            return true;
        }

        return false;
    }

    /**
     * Добавить информацию о завершенном заказе и рейтинге курьера.
     *
     * @param int $userId
     * @param int $orderId
     * @param string $feedback
     * @param int $stars
     * @return bool
     */
    public static function addCompletedOrder($userId, $orderId, $feedback, $stars)
    {
        $courierCard = self::findCourierCardByUserId($userId);

        if ($courierCard) {
            // Добавление информации о завершенном заказе
            $completedOrders = $courierCard->completed_orders ?? [];
            $completedOrders[] = ['order_id' => $orderId, 'feedback' => $feedback];
            $courierCard->completed_orders = $completedOrders;

            // Добавление рейтинга
            $rating = $courierCard->rating ?? [];
            $rating[] = ['order_id' => $orderId, 'stars' => $stars];
            $courierCard->rating = $rating;

            $courierCard->save();
            return true;
        }

        return false;
    }

    /**
     * Увеличить сумму cash в карте курьера.
     *
     * @param int $userId
     * @param float $amount
     * @return bool
     */
    public static function increaseCash($userId, $amount)
    {
        $courierCard = self::findCourierCardByUserId($userId);

        if ($courierCard) {
            $courierCard->cash += $amount;
            $courierCard->save();
            return true;
        }

        return false;
    }

    /**
     * Уменьшить сумму cash в карте курьера.
     *
     * @param int $userId
     * @param float $amount
     * @return bool
     */
    public static function decreaseCash($userId, $amount)
    {
        $courierCard = self::findCourierCardByUserId($userId);

        if ($courierCard && $courierCard->cash >= $amount) {
            $courierCard->cash -= $amount;
            $courierCard->save();
            return true;
        }

        return false;
    }

    /**
     * Найти карту курьера по user_id.
     *
     * @param int $userId
     * @return CourierCard|null
     */
    public static function findCourierCardByUserId($userId)
    {
        return CourierCard::where('user_id', $userId)->first();
    }

}
