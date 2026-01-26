<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class SubCategorySiteMapController extends Controller
{
    public function __construct()
    {

//        $this->middleware(function ($request, $next){
//            if(!session()->get('admin')){
//                return redirect('admin/login');
//            }
//            return $next($request);
//        });
    }

    public function index()
    {
        $getsubcat = DB::table('sub_categories')->orderby('id', 'desc')->get();
        return view('admins.sitemapSubcategory', ['subcat' => $getsubcat]);
    }

    

}
