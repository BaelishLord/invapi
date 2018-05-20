<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductMaster;

class ProductMasterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProductMaster $product_master)
    {
        $this->middleware('auth');
        $this->product_master = $product_master;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ProductMaster::get();
        return $data;
    }

    public function store(Request $request)
    {
        $data = $request->all();

        beginTransaction();
            $response = create($this->product_master, $data);
        commit();

        return 'success';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = ProductMaster::find($id);
        return $data;
    }

    public function update(Request $request, $id) 
    {
        $data = $request->all();
        beginTransaction();
            fillUpdate($this->product_master, $data, $id, ProductMaster::getKeyField());
        commit();

        return 'success';
    }

    public function show($id)
    {
        $data = ProductMaster::find($id);

        return $data;
    }

    public function destroy($id)
    {
        beginTransaction();
            $res = delete($this->product_master, $id, ProductMaster::getKeyField());
        commit();

        return "success";
    }
}
