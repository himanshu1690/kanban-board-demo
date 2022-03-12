<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CardRequest;
use App\Http\Requests\ColumnRequest;
use App\Models\Card;
use App\Models\Column;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ColumnController extends Controller
{

    /**
     * Get all columns
     */
    public function index(){
        $columns = Column::with('cards')->get();
        return response()->json($columns);
    }

    /**
     * Post - Create new column
     * @param ColumnRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ColumnRequest $request){
        $data = $request->validated();
        $column = Column::create($data);

        return response()->json(['status'=>true, 'data'=>$column]);
    }

    /**
     * Delete - delete column, also deletes all it's cards
     * @param Column $column
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Column $column){
        $column->delete();

        return response()->json(['status'=>true, 'msg'=>'Column deleted!']);
    }

    /**
     * POST - Create new card under given column
     * @param CardRequest $request
     * @param Column $column
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCard(CardRequest $request, Column $column){
        $data = $request->validated();
        $card = $column->addCard($data);

        return response()->json(['status'=>true, 'data'=>$card]);
    }

    /**
     * PUT - update card
     * @param CardRequest $request
     * @param Card $card
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCard(CardRequest $request, Card $card){
        $data = $request->validated();
        $card->update($data);

        return response()->json(['status'=>true, 'msg'=>'Card updated!']);
    }

    /**
     * PUT - update card index
     * @param Request $request
     * @param Card $card
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCardIndex(Request $request, Card $card){

        $total = Card::where('column_id', $request->to_column)->count();
        if($total > $request->to_index){

        }
        $cards = Card::where('column_id', $request->to_column)->where('card_index', '>=', $request->to_index)->get();
        Log::debug('[CARDS]', [$cards]);
        $i = $request->to_index + 1;
        Log::debug('[CARDS]', [$i]);
        foreach ($cards as $c){
            $c->card_index = $i;
            $c->save();
            $i++;
        }
        Log::debug('[CARDS]', [Card::where('column_id', $request->to_column)->get()]);
        $card->card_index = $request->to_index;
        $card->column_id = $request->to_column;
        $card->save();
        return response()->json(['status'=>true, 'msg'=>'Card updated!']);
    }

    /**
     * Delete - delete card
     * @param Card $card
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCard(Card $card){
        $card->delete();

        return response()->json(['status'=>true, 'msg'=>'Card deleted!']);
    }
}
