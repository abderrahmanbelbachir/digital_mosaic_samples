<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Mark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MarkController extends Controller
{
    public function index() {
        $marks = Mark::get();
        return $marks;
    }

    public function store(Request $request) {
        $mark = Mark::where('category' , $request->category)->first();
        if ($mark && $mark->id) {
            $mark->marks = json_decode($mark->marks);
            $mark->marks = array_merge($mark->marks , [$request->mark]);
            $mark->update(['marks' => json_encode($mark->marks)]);
        } else {
            $newMarks = [];
            array_push($newMarks, $request->mark);
            Mark::create([
               'category' => $request->category,
               'key' => $request->category,
               'marks' => json_encode($newMarks)
            ]);
        }

        return 'mark updated successfully !!';
    }

    public function migrate(Request $request) {
        $inputs = [];
        foreach ($request->marks as $mark) {
            Mark::create([
                'category' => $mark['category'],
                'key' => $mark['key'],
                'marks' => json_encode($mark['marks'])
            ]);
        }
        return 'mark created successfully !!';
    }

    public function show(Request $request , $id) {
        $mark = Mark::findOrFail($id);
        return $mark;
    }

    public function update(Request $request , $id) {
        Mark::where('id' , $id)->update($request);
        return 'product updated successfully !!';
    }

    public function destroy(Request $request , $id) {
        Mark::destroy($id);
        return 'product destroyed successfully';
    }
}
