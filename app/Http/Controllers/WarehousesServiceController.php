<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehousesServiceController extends Controller
{
    /**
     * Increase the count of specified product in storage.
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

        $warehouse = Warehouse::findOrFail($id);
        $warehouse->count += $request->count;
        $warehouse->save();

        return response()->json($warehouse, 200);
    }

    /**
     * Decrease the count of specified product in storage.
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

        $warehouse = Warehouse::findOrFail($id);
        $warehouse->count -= $request->count;
        $warehouse->save();

        return response()->json($warehouse, 200);
    }
}
