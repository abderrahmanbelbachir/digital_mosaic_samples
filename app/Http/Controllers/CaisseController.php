<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use Illuminate\Http\Request;

class CaisseController extends Controller
{
    public function index(Request $request) {
        $caisse = Caisse::with(['user' => function ($query) {
            $query->select('id', 'fullName');
        }]);
        if(isset($request->sortColumn)){
            if(isset($request->sortType)){
                $caisse = $caisse->orderBy($request->sortColumn , $request->sortType);
            }
            else{
                $caisse = $caisse->orderBy($request->sortColumn);
            }
        }
        else{
            $caisse = $caisse->orderBy('created_at' , 'desc');
        }
        $caisse = $caisse->paginate(10);
        $income = Caisse::whereNotNull('amount')->where('type','income')
            ->selectRaw('sum(amount) as incomes')->get();
        $outcome = Caisse::whereNotNull('amount')->where('type','outcome')
            ->selectRaw('sum(amount) as outcomes')->get();
        return [$caisse , $income, $outcome];
    }

    public function store(Request $request) {
        $inputs = $request->only(['user_id', 'amount', 'label', 'type' , 'currency' ,
            'currency_base' ,'note']);
        $caisse = Caisse::create($inputs);
        return 'caisse line created successfully !!';
    }

    public function show($id) {
        $caisse = Caisse::findOrFail($id);
        return $caisse;
    }

    public function update(Request $request , $id) {
        $inputs = $request->only(['user_id', 'amount', 'label', 'type' , 'currency' ,
            'currency_base' ,'note']);
        Caisse::where('id' , $id)->update($inputs);
        return 'caisse line updated successfully !!';
    }

    public function destroy($id) {
        Caisse::destroy($id);
        return 'caisse line destroyed successfully';
    }
}
