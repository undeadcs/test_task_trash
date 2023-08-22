<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Customer;
use App\Models\Car;

class CustomerController extends Controller
{
    /**
     * Получение валидных данных из запроса
     *
     * @param Request $request запрос
     *
     * @return false|array
     */
    protected function getValidInput(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'phone' => 'required'
        ]);

        return $validator->fails() ? false : $input;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // @todo при больших объемах надо разбивать на фрэймы данных и возвращать totalCount
        return response()->json(Customer::all());
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
        $input = $this->getValidInput($request);
        if ($input === false) {
            return response()->json(['message' => 'Invalid request'])
                ->setStatusCode(400);
        }

        return response()->json(Customer::create($input));
    }

    /**
     * Display the specified resource.
     *
     * @param  Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return response()->json($customer);
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
     * @param  Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $input = $this->getValidInput($request);
        if ($input === false) {
            return response()->json(['message' => 'Invalid request'])
                ->setStatusCode(400);
        }

        $customer->name  = $input['name'];
        $customer->phone = $input['phone'];
        $customer->save();

        return response()->json($customer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json($customer);
    }

    /**
     * Показ привязанного авто
     *
     * @param Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function showCar(Customer $customer)
    {
        return response()->json($customer->car);
    }
    
    /**
     * Привязка авто к пользователю
     * 
     * @param Customer $customer пользователь
     * @param Car $car авто
     * @return \Illuminate\Http\Response
     */
    public function assignCar(Customer $customer, Car $car)
    {
		$car->customer_id = $customer->id;
		$car->save();
		
		return response()->json();
	}
	
	/**
     * Отвязка авто от пользователя
     * 
     * @param Customer $customer пользователь
     * @return \Illuminate\Http\Response
     */
	public function unassignCar(Customer $customer)
	{
		$car = $customer->car;
		if (!$car) {
			return response()->json(['message' => 'Car is not assigned'])
                ->setStatusCode(404);
		}
		
		$car->customer_id = 0;
		$car->save();
		
		return response()->json();
	}
}
