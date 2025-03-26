<?php

namespace App\Http\Controllers;

use App\Models\consultations;
use App\Http\Requests\StoreconsultationsRequest;
use App\Http\Requests\UpdateconsultationsRequest;

class ConsultationsController extends Controller
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
     * @param  \App\Http\Requests\StoreconsultationsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreconsultationsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\consultations  $consultations
     * @return \Illuminate\Http\Response
     */
    public function show(consultations $consultations)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\consultations  $consultations
     * @return \Illuminate\Http\Response
     */
    public function edit(consultations $consultations)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateconsultationsRequest  $request
     * @param  \App\Models\consultations  $consultations
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateconsultationsRequest $request, consultations $consultations)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\consultations  $consultations
     * @return \Illuminate\Http\Response
     */
    public function destroy(consultations $consultations)
    {
        //
    }
}
