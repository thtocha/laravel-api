<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CardStoreRequest;
use App\Http\Resources\CardResource;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CardStoreRequest $request)
    {
        $created_card = Card::create($request->validated());

        return new CardResource($created_card);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Card $card)
    {
        $card->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
