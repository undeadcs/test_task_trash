<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Car;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Car::all());
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
        $input = $request->all();

        $validator = Validator::make($input, [
            'model' => 'required',
            'number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid request'])
                ->setStatusCode(400);
        }

        return response()->json(Car::create($input));
    }

    /**
     * Display the specified resource.
     *
     * @param  Car  $car
     * @return \Illuminate\Http\Response
     */
    public function show(Car $car)
    {
        return response()->json($car);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Car  $car
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Car $car)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'model' => 'required',
            'number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid request'])
                ->setStatusCode(400);
        }

        $car->model  = $input['model'];
        $car->number = $input['number'];
        $car->save();

        return response()->json($car);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Car  $car
     * @return \Illuminate\Http\Response
     */
    public function destroy(Car $car)
    {
        $car->delete();

        return response()->json($car);
    }

    /**
     * Показ пользователя
     *
     * @param Car $car
     * @return \Illuminate\Http\Response
     */
    public function showCustomer(Car $car)
    {
        return response()->json($car->customer);
    }
}
