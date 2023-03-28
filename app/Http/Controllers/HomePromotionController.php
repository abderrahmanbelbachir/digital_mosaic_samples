<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HomePromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomePromotionController extends Controller
{
    public function index() {
        $promotions = HomePromotion::get();
        return $promotions;
    }

    public function store(Request $request) {
        $inputs = $request->only(['url' , 'href' , 'language']);
        $promotion = HomePromotion::create($inputs);
        return 'promotion picture created successfully !!';
    }

    public function show(Request $request , $id) {
        $promotion = HomePromotion::findOrFail($id);
        return $promotion;
    }

    public function update(Request $request , $id) {
        HomePromotion::where('id' , $id)->update($request);
        return 'promotion picture updated successfully !!';
    }

    public function destroy(Request $request , $id) {
        HomePromotion::destroy($id);
        return 'promotion picture destroyed successfully';
    }
}
