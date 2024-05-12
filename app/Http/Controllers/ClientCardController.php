<?php

namespace App\Http\Controllers;

use App\Models\ClientCard;
use Illuminate\Http\Request;

class ClientCardController extends Controller
{
    /**
     * Display a listing of the client cards.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $clientCards = ClientCard::all();
        return response()->json($clientCards);
    }

    /**
     * Store a newly created client card in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $clientCard = ClientCard::create($request->all());
        return response()->json($clientCard, 201);
    }

    /**
     * Display the specified client card.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $clientCard = ClientCard::findOrFail($id);
        return response()->json($clientCard);
    }

    /**
     * Update the specified client card in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $clientCard = ClientCard::findOrFail($id);
        $clientCard->update($request->all());
        return response()->json($clientCard, 200);
    }

    /**
     * Remove the specified client card from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $clientCard = ClientCard::findOrFail($id);
        $clientCard->delete();
        return response()->json(null, 204);
    }
}
