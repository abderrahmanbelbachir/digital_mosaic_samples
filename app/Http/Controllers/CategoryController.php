<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    public function index(Request $request)
    {
        $categories = Category::first();
        if (isset($request->forFreeDelivery)) {
            $list = [];
            $categories->value = json_decode($categories->value, true);
            foreach ($categories->value as $category) {
                if (isset($request->isFreeDelivery)) {
                    if ($request->isFreeDelivery == 1) {
                        if($category['0']['isFreeDelivery']==1){
                            $row['category'] = $category['0']['name'];
                            $row['products'] = Product::where('isPublished', 1)
                            ->whereNotNull('pictures')->whereNotNull('validatedAt')
                            ->where('category', $category)->count();
                            $row['isFreeDelivery'] = $category['0']['isFreeDelivery'];
                            array_push($list, $row);
                        }
                    }
                    else{
                        if($category['0']['isFreeDelivery']==0){
                            $row['category'] = $category['0']['name'];
                            $row['products'] = Product::where('isPublished', 1)
                            ->whereNotNull('pictures')->whereNotNull('validatedAt')
                            ->where('category', $category)->count();
                            $row['isFreeDelivery'] = $category['0']['isFreeDelivery'];
                            array_push($list, $row);
                        }        
                    }      
                }
            }
            return $list;
        } else {
            return $categories;
        }
    }

    public function fixCategories()
    {
        $categories = Category::first();
        Category::where('id', $categories->id)->update([
            'value' => str_replace(' "', '"', $categories->value),
            'english' => str_replace(' "', '"', $categories->english),
            'french' => str_replace(' "', '"', $categories->french),
            'arabic' => str_replace(' "', '"', $categories->arabic),
        ]);
        return 'categories fixed !';
    }

    public function updateCategoryStatus(Request $request)
    {
        $categories = Category::first();
        $categoryValues = json_decode($categories->value, true);
        foreach ($categoryValues as $key => $category) {
            if (array_search($category['0']['name'], $request->categories)) {
                $category['0']['isFreeDelivery'] = $request->value;
                $categoryValues[$key] = $category;
            }
        }
        $categories->update(['value' => json_encode($categoryValues)]);
        return 'end of function';
    }

    public function getCategoriesValue()
    {
        $categories = Category::first();
        return $categories->value;
    }

    public function getCategoriesEnglish()
    {
        $categories = Category::first();
        return $categories->english;
    }

    public function getCategoriesFrench()
    {
        $categories = Category::first();
        return $categories->french;
    }

    public function getCategoriesArabic()
    {
        $categories = Category::first();
        return $categories->arabic;
    }

    public function getCategoriesList(Request $request)
    {
        $categories = Category::first();
            $list = [];
            $categories->value = json_decode($categories->value, true);
            foreach ($categories->value as $category) {
                    $row = $category['0']['name'];
                    array_push($list, $row);
            }
            return $list;
        
    }

    public function store(Request $request)
    {
        $category = Category::create($request);
        return 'product created successfully !!';
    }

    public function migrate(Request $request)
    {
        $inputs = $request->only(['value', 'english', 'french', 'arabic']);
        $inputs['value'] = json_encode($inputs['value']);
        $inputs['english'] = json_encode($inputs['english']);
        $inputs['french'] = json_encode($inputs['french']);
        $inputs['arabic'] = json_encode($inputs['arabic']);
        $category = Category::create([
            'value' => $inputs['value'],
            'english' => $inputs['english'],
            'french' => $inputs['french'],
            'arabic' => $inputs['arabic']
        ]);
        return 'categories migrated successfully !!';
    }

    public function show(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        return $category;
    }

    public function update(Request $request, $id)
    {
        $inputs = $request->only(['value']);
        Category::where('id', $id)->update($inputs);
        return 'product updated successfully !!';
    }

    public function destroy(Request $request, $id)
    {
        Category::destroy($id);
        return 'product destroyed successfully';
    }
}
