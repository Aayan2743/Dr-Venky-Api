<?php

namespace App\Http\Controllers;

use App\Models\labreports;
use App\Http\Requests\StorelabreportsRequest;
use App\Http\Requests\UpdatelabreportsRequest;

class LabreportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StorelabreportsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorelabreportsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\labreports  $labreports
     * @return \Illuminate\Http\Response
     */
    public function show(labreports $labreports)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\labreports  $labreports
     * @return \Illuminate\Http\Response
     */
    public function edit(labreports $labreports)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatelabreportsRequest  $request
     * @param  \App\Models\labreports  $labreports
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatelabreportsRequest $request, labreports $labreports)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\labreports  $labreports
     * @return \Illuminate\Http\Response
     */
    public function destroy(labreports $labreports)
    {
        //
    }
}
