<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class MainCategorySiteMapController extends Controller
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
        $getmcat = DB::table('categories')->orderby('id', 'desc')->get();
        return view('admins.sitemapMaincategory', ['mcat' => $getmcat]);
    }

    

}
