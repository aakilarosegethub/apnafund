<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class BrandtagsSiteMapController extends Controller
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
        $getproduct = DB::table('brands')->orderby('id', 'desc')->get();
        $per_page = 1000;
        $all = count($getproduct);
        $tpage = ceil($all /$per_page);
        for($i=0;$i<$tpage ;$i++)
        {
            $off = $i * $per_page;
            $getproduct = DB::table('brands')->offset($off)
                ->limit($per_page)->orderby('id', 'desc')->get();
                if($i)
                {
        $file = 'brandtagssitemap'.$i.'.xml';
        
                }
                else
                {
                    $file = 'brandtagssitemap.xml';
                }
        echo view('admins.brandtagssitemapcreate', ['products' => $getproduct,'fname'=>$file]);
        echo $file.'<br>';
        }
    }

    

}
