<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UtilitiesController extends Controller
{
    public function logMessage(Request $request) {
        Log::info('message : ' . $request->message);
    }

    public function unAuthorizedRequest(Request $request) {
        return response()->json([
            'message' => 'unAuthorized request'], 404);
    }

    public function fillHomePlaceForStores(Request $request) {
        $stores = Store::whereNotNull('validatedAt')
            ->whereNull('homePlace')
            ->update(['homePlace' => 99]);
        return 'updated home place for validated stores !!!';
    }
}
