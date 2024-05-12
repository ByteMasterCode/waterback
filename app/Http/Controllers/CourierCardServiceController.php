<?php

namespace App\Http\Controllers;

use App\Models\CourierCard;
use Illuminate\Http\Request;

class CourierCardServiceController extends Controller
{
    /**
     * Display the specified courier card by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $courierCard = CourierCard::findOrFail($id);
        return response()->json($courierCard);
    }

    /**
     * Increase the count of specified product on the courier card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function increaseCount(Request $request, $id)
    {
        $request->validate([
            'count' => 'required|integer|min:0',
        ]);

        $courierCard = CourierCard::findOrFail($id);
        $courierCard->count += $request->count;
        $courierCard->save();

        return response()->json($courierCard, 200);
    }

    /**
     * Decrease the count of specified product on the courier card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function decreaseCount(Request $request, $id)
    {
        $request->validate([
            'count' => 'required|integer|min:0',
        ]);

        $courierCard = CourierCard::findOrFail($id);
        $courierCard->count -= $request->count;
        $courierCard->save();

        return response()->json($courierCard, 200);
    }
}
