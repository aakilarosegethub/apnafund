<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class DirectOrderController extends Controller
{
    public function index()
    {
        return view('admins.addform');
    }
    public function add(request $request)
    {

        $data = array(
            'product_name' => $request->get('product_name'),
            'customer_name' => $request->get('customer_name'),
            'price' => $request->get('price'),
            'mobile_number' => $request->get('mobile_number'),
            'quantity' => $request->get('quantity'),
            'address' => $request->get('address')
        );

        session([
            'customeorder' => $data
        ]);
        $this->validate(request(),[
            'product_name' => 'required',
            'customer_name' => 'required',
            'mobile_number' => 'required|numeric',
            'quantity' => 'required|numeric',
            'price' => 'required|numeric',
            'address' => 'required'
        ]);


        if (DB::table('custom_order')->insert($data)){
            return redirect(route('add-form'))->with([
                'msg'=>'Order Added Successfully',
                'msg_type'=>'success',
            ]);
        }else{
          return redirect(route('add-form'))->with([
                'msg'=>'Something went wrong',
                'msg_type'=>'error',
            ]);
        }
    }

    public function clear_man()
    {
        session()->forget('customeorder');
            return back()->with('message', 'New Form Generated Successfully');

    }
}