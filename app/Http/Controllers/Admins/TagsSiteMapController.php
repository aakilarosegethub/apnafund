<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class TagsSiteMapController extends Controller
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

    // public function index()
    // {
    //     $getproduct = DB::table('oldproducts')->orderby('id', 'desc')->get();
    //     return view('admin.tagssitemapcreate', ['products' => $getproduct]);
    // }
    
    public function index()
{
    $products = DB::table('products')->orderby('id', 'desc')->get();
    $chunkSize = 50000;
    $currentChunk = 1;
    $tagIndex = 0;

    while ($tagIndex < count($products)) {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $linksCount = 0;

        for ($i = 0; $i < $chunkSize && $tagIndex < count($products); $i++) {
            $product = $products[$tagIndex];
            $tags = explode(",", $product->tags);

            foreach ($tags as $tag) {
                $tag = str_replace("&", "and", $tag);
                $tag =strtolower(trim(preg_replace('/[^A-Za-z0-9-%#]+/', '-', $tag)));
                //  urlencode(trim($tag)); // Ensure valid URL format
                $url = url('tags/') . '/' . $tag;

                $xml .= '<url>
                    <loc>' . $url . '</loc>
                    <priority>1.0</priority>
                    <changefreq>daily</changefreq>
                    </url>';

                $linksCount++;
                if ($linksCount >= $chunkSize) {
                    break 2; // Break both inner and outer loops
                }
            }

            $tagIndex++;
        }

        $xml .= '</urlset>';
        $filename = "public/sitemap/tagssitemap{$currentChunk}.xml";
        $filepath = $filename;

        file_put_contents($filepath, $xml);
        $currentChunk++;
    }

    return response()->json(['message' => 'Sitemaps generated successfully']);
}


    

}
