<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Models\Edition;
use Illuminate\Http\Request;

class editionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $editions = Edition::whereNotNull('maisonEdition');
        if ($request->name) {
            $editions = $editions->where('name', 'like', '%' . $request->name . '%');
        }
        if(isset($request->sortColumn)){
            if(isset($request->sortType)){
                $editions = $editions->orderBy($request->sortColumn , $request->sortType);
            }
            else{
                $editions = $editions->orderBy($request->sortColumn);
            }
        }
        else{
            $editions = $editions->orderBy('created_at' , 'desc');
        }
        return $editions->paginate(10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $edition = Edition::create([
            'maisonEdition' => $request->maisonEdition,
            'password' => md5($request->maisonEdition.$request->createdAt)
        ]);
    return 'edition line created successfully !!';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $edition = Edition::findOrFail($id);
        return $edition;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $inputs = $request->only(['name']);
    Edition::where('id' , $id)->update($inputs);
    return 'edition line updated successfully !!';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Edition::destroy($id);
        return 'edition line destroyed successfully';
    }
}
