<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class Orders extends Controller
{
    // public function __construct()
    // {

    //     $this->middleware(function ($request, $next){
    //         if(!session()->get('admin')){
    //             return redirect('admin/login');
    //         }else{
    //                 $data_info = session()->get('admin');
    //                 if($data_info[0]->type == '1'){
    //                     return redirect('admin/');
    //                 }
    //             }
    //         return $next($request);
    //     });
    // }
 public function custom_api()
{
    $status = $_GET['status'];
    $start = $_GET['start'];
    $length = $_GET['length'];
    $srch = $_GET['search']['value'];

$query = DB::table('custom_order')->orderBy('id', 'desc')->where('status', $status);

if (!empty($srch)) {
    $query->where(function($q) use ($srch) {
        $q->where('product_name', 'like', '%' . $srch . '%')
          ->orWhere('customer_name', 'like', '%' . $srch . '%')
          ->orWhere('mobile_number', 'like', '%' . $srch . '%')
          ->orWhere('address', 'like', '%' . $srch . '%');
    });
}


$tot = $query->get();

$ret = $query->offset($start)->limit($length)->get();

    $data = array();
    $i = 0;
    foreach($ret as $k => $v)
    {
        $i++;
         $checkbox = '<input type="checkbox" class="row-checkbox"  data-id="' . $v->id . '">';
        $st = '<a  class="btn btn-success btn-block">Completed</a>';
         $actions = '<a href="' . url('admin/order-detail/' . $v->id) . '" target="_blank" class="btn btn-info btn-block">View More</a>
                            <a href="' . url('admin/Order-delete/' . $v->id) . '" class="btn btn-danger btn-block">Delete</a>';

         

        $data[] = array($checkbox,$start + $i, $v->product_name, $v->customer_name, $v->mobile_number, $v->quantity, $v->address, $st,$actions);
    }
    
    $r = array('draw' => $_GET['draw'], 'recordsTotal' => count($tot), 'recordsFiltered' => count($tot), 'data' => $data);
    echo json_encode($r);
    exit();
}


 public function dispatch_api()
{
    $status = $_GET['status'];
    $start = $_GET['start'];
    $length = $_GET['length'];
    $srch = $_GET['search']['value'];

$query = DB::table('custom_order')->orderBy('id', 'desc')->where('status', $status);

if (!empty($srch)) {
    $query->where(function($q) use ($srch) {
        $q->where('product_name', 'like', '%' . $srch . '%')
          ->orWhere('customer_name', 'like', '%' . $srch . '%')
          ->orWhere('mobile_number', 'like', '%' . $srch . '%')
          ->orWhere('address', 'like', '%' . $srch . '%');
    });
}


$tot = $query->get();

$ret = $query->offset($start)->limit($length)->get();

    $data = array();
    $i = 0;
    foreach($ret as $k => $v)
    {
        $i++;
        $st = '<a href="' . url('admin/order-cod/' . $v->id) . '" class="btn btn-info  btn-block">Mark as Dispatch</a>';
         $actions = '<a href="' . url('admin/order-detail/' . $v->id) . '" target="_blank" class="btn btn-info btn-block">View More</a>
                            <a href="' . url('admin/Order-delete/' . $v->id) . '" class="btn btn-danger btn-block">Delete</a>';

         

        $data[] = array($start + $i, $v->product_name, $v->customer_name, $v->mobile_number, $v->quantity, $v->address, $st,$actions);
    }
    
    $r = array('draw' => $_GET['draw'], 'recordsTotal' => count($tot), 'recordsFiltered' => count($tot), 'data' => $data);
    echo json_encode($r);
    exit();
}




 public function read_complete_api()
{
    $status = $_GET['status'];
    $start = $_GET['start'];
    $length = $_GET['length'];
    $srch = $_GET['search']['value'];

$query = DB::table('custom_order')->orderBy('id', 'desc')->where('status', $status);

if (!empty($srch)) {
    $query->where(function($q) use ($srch) {
        $q->where('product_name', 'like', '%' . $srch . '%')
          ->orWhere('customer_name', 'like', '%' . $srch . '%')
          ->orWhere('mobile_number', 'like', '%' . $srch . '%')
          ->orWhere('address', 'like', '%' . $srch . '%');
    });
}


$tot = $query->get();

$ret = $query->offset($start)->limit($length)->get();

    $data = array();
    $i = 0;
    foreach($ret as $k => $v)
    {
        $i++;
        $st = '<a href="' . url('admin/marks-complete/' . $v->id) . '" class="btn btn-success btn-block">Mark as Complete</a>
               <a href="' . url('admin/marks-return/' . $v->id) . '" class="btn btn-info  btn-block">Mark as Return</a>
        ';
         $actions = '<a href="' . url('admin/order-detail/' . $v->id) . '" target="_blank" class="btn btn-info btn-block">View More</a>
                            <a href="' . url('admin/Order-delete/' . $v->id) . '" class="btn btn-danger btn-block">Delete</a>';

         

        $data[] = array($start + $i, $v->product_name, $v->customer_name, $v->mobile_number, $v->quantity, $v->address, $st,$actions);
    }
    
    $r = array('draw' => $_GET['draw'], 'recordsTotal' => count($tot), 'recordsFiltered' => count($tot), 'data' => $data);
    echo json_encode($r);
    exit();
}



 public function approve_api()
{
    $status = $_GET['status'];
    $start = $_GET['start'];
    $length = $_GET['length'];
    $srch = $_GET['search']['value'];

$query = DB::table('custom_order')->orderBy('id', 'desc')->where('status', $status);

if (!empty($srch)) {
    $query->where(function($q) use ($srch) {
        $q->where('product_name', 'like', '%' . $srch . '%')
          ->orWhere('customer_name', 'like', '%' . $srch . '%')
          ->orWhere('mobile_number', 'like', '%' . $srch . '%')
          ->orWhere('address', 'like', '%' . $srch . '%');
    });
}


$tot = $query->get();

$ret = $query->offset($start)->limit($length)->get();

    $data = array();
    $i = 0;
    foreach($ret as $k => $v)
    {
        $i++;
        
        $st= '';
        
        if($v->status == 0){
             $st = '<a href="' . url('admin/order-approve/' . $v->id) . '" target="_blank" class="btn btn-primary btn-block">Approve Now</a>';
        }else if($v->status == 1){
             $st = '<a href="' . url('admin/order-cod/' . $v->id) . '" class="btn btn-info  btn-block">Mark as Dispatch</a>';
        }else if($v->status == 2){
            $st = '<a href="' . url('admin/marks-complete/' . $v->id) . '" class="btn btn-success btn-block">Mark as Complete</a>
               <a href="' . url('admin/marks-return/' . $v->id) . '" class="btn btn-info  btn-block">Mark as Return</a>';
        }else if($v->status == 3){
             $st = '<a  class="btn btn-info btn-block">Completed</a>';
        }else if($v->status == 4){
             $st = '<a  class="btn btn-danger btn-block">Return</a>';
        }
         
       
        $actions = '<a href="' . url('admin/order-detail/' . $v->id) . '" target="_blank" class="btn btn-info btn-block">View More</a>
        <a href="' . url('admin/Order-delete/' . $v->id) . '" class="btn btn-danger btn-block">Delete</a>';

         

        $data[] = array($start + $i, $v->product_name, $v->customer_name, $v->mobile_number, $v->quantity, $v->address, $st,$actions);
    }
    
    $r = array('draw' => $_GET['draw'], 'recordsTotal' => count($tot), 'recordsFiltered' => count($tot), 'data' => $data);
    echo json_encode($r);
    exit();
}



 public function return_api()
{
    $status = $_GET['status'];
    $start = $_GET['start'];
    $length = $_GET['length'];
    $srch = $_GET['search']['value'];

$query = DB::table('custom_order')->orderBy('id', 'desc')->where('status', $status);

if (!empty($srch)) {
    $query->where(function($q) use ($srch) {
        $q->where('product_name', 'like', '%' . $srch . '%')
          ->orWhere('customer_name', 'like', '%' . $srch . '%')
          ->orWhere('mobile_number', 'like', '%' . $srch . '%')
          ->orWhere('address', 'like', '%' . $srch . '%');
    });
}


$tot = $query->get();

$ret = $query->offset($start)->limit($length)->get();

    $data = array();
    $i = 0;
    foreach($ret as $k => $v)
    {
        $i++;
        
        $st= '';
        
        if($v->status == 0){
             $st = '<a href="' . url('admin/order-approve/' . $v->id) . '" target="_blank" class="btn btn-primary btn-block">Approve Now</a>';
        }else if($v->status == 1){
             $st = '<a href="' . url('admin/order-cod/' . $v->id) . '" class="btn btn-info  btn-block">Mark as Dispatch</a>';
        }else if($v->status == 2){
            $st = '<a href="' . url('admin/marks-complete/' . $v->id) . '" class="btn btn-success btn-block">Mark as Complete</a>
               <a href="' . url('admin/marks-return/' . $v->id) . '" class="btn btn-info  btn-block">Mark as Return</a>';
        }else if($v->status == 3){
             $st = '<a  class="btn btn-info btn-block">Completed</a>';
        }else if($v->status == 4){
             $st = '<a  class="btn btn-danger btn-block">Return</a>';
        }
         
       
        $actions = '<a href="' . url('admin/order-detail/' . $v->id) . '" target="_blank" class="btn btn-info btn-block">View More</a>
        <a href="' . url('admin/Order-delete/' . $v->id) . '" class="btn btn-danger btn-block">Delete</a>';

         

        $data[] = array($start + $i, $v->product_name, $v->customer_name, $v->mobile_number, $v->quantity, $v->address, $st,$actions);
    }
    
    $r = array('draw' => $_GET['draw'], 'recordsTotal' => count($tot), 'recordsFiltered' => count($tot), 'data' => $data);
    echo json_encode($r);
    exit();
}



    public function custom_api1()
    {
        ?>
        {
  "draw": 1,
  "recordsTotal": 57,
  "recordsFiltered": 57,
  "data": [
    [
      "Airi",
      "Satou",
      "Accountant",
      "Tokyo",
      "28th Nov 08",
      "$162,700"
    ],
    [
      "Angelica",
      "Ramos",
      "Chief Executive Officer (CEO)",
      "London",
      "9th Oct 09",
      "$1,200,000"
    ],
    [
      "Ashton",
      "Cox",
      "Junior Technical Author",
      "San Francisco",
      "12th Jan 09",
      "$86,000"
    ],
    [
      "Bradley",
      "Greer",
      "Software Engineer",
      "London",
      "13th Oct 12",
      "$132,000"
    ],
    [
      "Brenden",
      "Wagner",
      "Software Engineer",
      "San Francisco",
      "7th Jun 11",
      "$206,850"
    ],
    [
      "Brielle",
      "Williamson",
      "Integration Specialist",
      "New York",
      "2nd Dec 12",
      "$372,000"
    ],
    [
      "Bruno",
      "Nash",
      "Software Engineer",
      "London",
      "3rd May 11",
      "$163,500"
    ],
    [
      "Caesar",
      "Vance",
      "Pre-Sales Support",
      "New York",
      "12th Dec 11",
      "$106,450"
    ],
    [
      "Cara",
      "Stevens",
      "Sales Assistant",
      "New York",
      "6th Dec 11",
      "$145,600"
    ],
    [
      "Cedric",
      "Kelly",
      "Senior Javascript Developer",
      "Edinburgh",
      "29th Mar 12",
      "$433,060"
    ]
  ]
}
        <?php
    }
    public function index()
    {
        $get = DB::table('custom_order')->orderby('id', 'desc')->where('status', '=', 0)->paginate(10);
        
         return view('admins.orders', ['orders' => $get]);
    }

    public function wfdispatch()
    {
        $get = DB::table('custom_order')->orderby('id', 'desc')->where('status', 1)->paginate(10);
        return view('admins.ReadyToDispatch', ['orders' => $get]);
    }
    
    public function rorders()
    {
        $get = DB::table('custom_order')->orderby('id', 'desc')->where('status', 4)->paginate(10);
        return view('admins.ReturnOrders', ['orders' => $get]);
    }
    
    public function rtcompleate()
    {
        $get = DB::table('custom_order')->orderby('id', 'desc')->where('status', 2)->paginate(10);
        return view('admins.ReadyToComplete', ['orders' => $get]);
    }
    public function ordercomplete()
    {
        $get = DB::table('custom_order')->orderby('id', 'desc')->where('status', 3)->paginate(10);
        return view('admins.OrderCompleted', ['orders' => $get]);
    }
    public function detail($id)
    {
        $get = DB::table('custom_order')->where('id', $id)->first();
      
        return view('admins.orders', ['Detail' => $get]);
    }

    public function cod($id)
    {
        return view('admins.orders', ['cod' => $id]);
    }

    public function addcod(request $request, $id)
    {
        $up = DB::table('custom_order')->where('id', $id)->update([
            'tid' => $request->get('tid'),
            'cod' => $request->get('cod'),
            'status' => '2'
        ]);
        if($up){
             return redirect(route('admins.waiting-for-dispatch'))->with([
                'msg'=>'Tracking Code Inserted Successfully',
                'msg_type'=>'success',
            ]);
        }else{
            return redirect()->to('admin/waiting-for-dispatch')->with('message1', 'something went wrong');

        }
    }

    public function approve($id)
    {
        $up = DB::table('custom_order')->where('id', $id)->update([
            'status' => '1'
        ]);
        if($up){
             return redirect(route('admins.orders'))->with([
                'msg'=>'Order Approved Successfully',
                'msg_type'=>'success',
            ]);
        }else{
            return redirect()->to('admin/Orders')->with('message1', 'something went wrong');
        }
    }

    public function complete($id)
    {
        $up = DB::table('custom_order')->where('id', $id)->update([
            'status' => '3'
        ]);
        if($up){
           return redirect(route('admins.ready-to-complete'))->with([
                'msg'=>'Order Completed Successfully',
                'msg_type'=>'success',
            ]);
        }else{
            return redirect()->to('admin/ready-to-complete')->with('message1', 'something went wrong');
        }
    }
    
    
    
    public function return($id)
    {
        $up = DB::table('custom_order')->where('id', $id)->update([
            'status' => '4'
        ]);
        if($up){
           return redirect(route('admins.ready-to-complete'))->with([
                'msg'=>'Order Return Successfully',
                'msg_type'=>'success',
            ]);
        }else{
            return redirect()->to('admin/ready-to-complete')->with('message1', 'something went wrong');
        }
    }

    public function delete($id)
    {
        $del = DB::table('custom_order')->delete($id);
        if($del){
           return redirect(route('admins.orders'))->with([
                'msg'=>'Order Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }else{
            return back()->with('message', "something went wrong");
        }
    }
    
    
    public function del_all_com(Request $request) {
    
    $selectedRows = $request->input('selectedRows');
    dd($selectedRows);
    //  $del = DB::table('custom_order')->delete($selectedRows);
        

   }

    public function countx()
    {
        $data1 = DB::table('orders')->where('status', '0')->where('notify', '0')->count();
        $dv = DB::table('orders')->where('status', '0')->where('notify', '0')->first();
        $data = array(
          'count' => $data1,
          'orders' => $dv
        );
        return $data;
    }

    public function markx(request $request)
    {
        $up = DB::table('orders')->where('id', $request->get('id'))->update([
            'notify' => 1
        ]);
    }
    
}