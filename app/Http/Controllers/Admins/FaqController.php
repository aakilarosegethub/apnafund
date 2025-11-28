<?php

namespace App\Http\Controllers\Admins;

use App\Models\Admins\Category;
use App\Models\Admins\Box;
use App\Models\Admins\Brand;
use App\Models\Admins\Colors;
use App\Models\Admins\Size;
use App\Models\Admins\Shap;
use App\Models\Admins\Media;
use App\Models\Admins\Blog_Post;
use App\Models\Admins\Slider;
use App\Models\Admins\Contact;
use App\Models\Admins\Faq;
use App\Models\Admins\Gallerie;
use App\Models\Admins\SubCategory;
use App\Models\Admins\Admin;
use App\Models\Admins\Blog_Category;
use App\Models\Admins\Product;
use App\Models\Admins\Order;
use App\Models\Admins\Pages;
use App\Models\Admins\Setting;
use App\Models\Admins\Learn_setting;
use App\Models\Admins\Rating;
use App\Models\Admins\ProductsToMeta;
use App\Models\Admins\CategoriesToMeta;
use App\Models\Admins\Coupon;
use App\Models\Newsletter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Mail;
use Intervention\Image\Facades\Image as ImageFacade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Session;

class AdminController extends Controller
{
    public function __construct()
    {
        // session_start(); // Laravel handles sessions automatically

    }

    public function adminloginpage()
    {
        if(isset($_SESSION['admin']))
        {
        $this->dashboard();
        }
        return view('admins.login');
    }

    function admin_login_submit(Request $req)
    {   
        $user = Admin::where(['email'=>$req->email])->first();
        
        $data = Admin::all();
        if ($user) {
            Session::flush();
            

            if (Hash::check($req->password, $user->password)) {
                
                // $req->session()->flash('invalid','Success');
                Session::put('admin',$user);
                $_SESSION['admin'] = $user;
                return redirect('admin/dashboard');

            }else{

                // return "password not matched";
                $req->session()->flash('invalid','Enter Your Correct Password');
                return view('admins.login');
            }

        }else{
            $req->session()->flash('invalid','Invalid Email & Password');
            return view('admins.login');
        }

    }
    
    function admin(){
        $edit=Admin::all();
        return view('admins.admin',compact('edit'));
    }
    
    function update_admin(Request $req)
    {
       $email = $req->email;
       $pass = Hash::make($req->pass);
       $data = array(
           'email'=>$email,
           'password'=>$pass
           );
          $up =  DB::table('admins')->where('id', $req->id)->update($data);
          if($up){
               return redirect(route('admins.admin'))->with([
                'msg'=>'Admin updated Successfully',
                'msg_type'=>'success',
            ]);
          }else{
              return redirect(route('admins.admin'))->with([
                'msg'=>'Please Try Again!',
                'msg_type'=>'success',
            ]);
          }
          
    }


    function logout(Request $req)
    {
        session_destroy();
        return redirect('admin/login'); 
    }

    public function dashboard(){
        
        $categories=Category::all();
        $products=Product::all();
        $rating=Rating::all();
        $urreviews = $r = DB::table('rating')->where('is_read',0)->get();
        $corders = Order::where('dstatus',2)->get();
        $unrorders = Order::where('is_read',0)->get();
        return view('admins.dashboard',compact('categories','products','rating','corders','unrorders','urreviews'));
    }
    
    public function boxs(Request $request,$id=0,$delete=null){
        $edit=null;
        $seo = null;
        if(isset($delete) && $id>0){
            Box::find($id)->delete();
            return redirect(route('admins.boxs'))->with([
                'msg'=>'Box Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $edit=Box::find($id);
        }
        if ($request->isMethod('post')) {

            if($request->has('hidden_id')){
                
                $category=Box::find($request->hidden_id);
                $category->icon=$request->icon;
                $category->txt=$request->txt;
                
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
                
            }else{
                
                $category=new Box();
                $category->created_at=Date('Y-m-d h:i:s');
                $category->icon=$request->icon;
                $category->txt=$request->txt;
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
                
            }
            
            
           
            return redirect(route('admins.boxs'))->with([
                'msg'=>'Category Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $categories=Box::all();
        return view('admins.box',compact('categories','edit','seo'));
    }
    public function brand(Request $request,$id=0,$delete=null){
        $edit=null;
        $seo = null;
        if(isset($delete) && $id>0){
            Brand::find($id)->delete();
            return redirect(route('admins.brand'))->with([
                'msg'=>'Brand Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $edit=Brand::find($id);
        }
        if ($request->isMethod('post')) {
            $request->validate([
                'name'=>'required|unique:categories,name,'.$request->hidden_id,
            ]);

            if($request->has('hidden_id')){
                
                $category=Brand::find($request->hidden_id);
                $category->name=$request->name;
                $category->status=$request->status;
                $category->slug= strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));
                /*if(isset($request->image_one)){
                    $imageone = $request->image_one;
                    $pimage_name = time().$imageone->getClientOriginalName();
                    $imageone->move(public_path('/images/category/'),$pimage_name);
                    $category->image= 'public/images/category/'.$pimage_name;
                }*/
                
                
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
                
            }else{
                
                $category=new Brand();
                $category->created_at=Date('Y-m-d h:i:s');
                $category->name=$request->name;
                $category->status=$request->status;
                // if(isset($request->image_one)){
                //     $imageone = $request->image_one;
                //     $pimage_name = time().$imageone->getClientOriginalName();
                //     $imageone->move(public_path('/images/category/'),$pimage_name);
                //     $category->image= 'public/images/category/'.$pimage_name;
                // }
                $category->slug= strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
                
                
                
            }
            
            
           
            return redirect(route('admins.brand'))->with([
                'msg'=>'Brand Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $categories=Brand::all();
        return view('admins.brand',compact('categories','edit','seo'));
    }
    public function category(Request $request,$id=0,$delete=null){
        $edit=null;
        $seo = null;
        if(isset($delete) && $id>0){
            Category::find($id)->delete();
            return redirect(route('admins.category'))->with([
                'msg'=>'Category Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $seo = CategoriesToMeta::where('cid', '=',  $id)->first();
            $edit=Category::find($id);
        }
        if ($request->isMethod('post')) {
            $request->validate([
                'name'=>'required|unique:categories,name,'.$request->hidden_id,
            ]);

            if($request->has('hidden_id')){
                
                $category=Category::find($request->hidden_id);
                $category->name=$request->name;
                $category->status=$request->status;
                $category->slug= strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));
                /*if(isset($request->image_one)){
                    $imageone = $request->image_one;
                    $pimage_name = time().$imageone->getClientOriginalName();
                    $imageone->move(public_path('/images/category/'),$pimage_name);
                    $category->image= 'public/images/category/'.$pimage_name;
                }*/
                
                if ($request->hasFile('image_one')) {
                    $file = $request->file('image_one');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'webp';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/category/'), $name);
                
                    $category['image'] = 'public/images/category/'.$name;
                }
                
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
                $seo = CategoriesToMeta::where('cid', '=',  $request->hidden_id)->first();
            
                if($seo){
                    $seo->title = $request->stitle;
                    $seo->description = $request->sdescription;
                    $seo->keywords = $request->skeywords;
                    $seo->save();
                }else{
                    $categoriesmeta = new CategoriesToMeta();
                
                    $categoriesmeta->cid = $category->id;
                    $categoriesmeta->title = $request->stitle;
                    $categoriesmeta->description = $request->sdescription;
                    $categoriesmeta->keywords = $request->skeywords;
                    $categoriesmeta->save();
                }
                
                
            }else{
                
                $category=new Category();
                $category->created_at=Date('Y-m-d h:i:s');
                $category->name=$request->name;
                $category->status=$request->status;
                // if(isset($request->image_one)){
                //     $imageone = $request->image_one;
                //     $pimage_name = time().$imageone->getClientOriginalName();
                //     $imageone->move(public_path('/images/category/'),$pimage_name);
                //     $category->image= 'public/images/category/'.$pimage_name;
                // }
                if ($request->hasFile('image_one')) {
                    $file = $request->file('image_one');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'webp';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/category/'), $name);
                
                    $category['image'] = 'public/images/category/'.$name;
                }
                $category->slug= strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
                $categoriesmeta = new CategoriesToMeta();
            
                $categoriesmeta->cid = $category->id;
                $categoriesmeta->title = $request->stitle;
                $categoriesmeta->description = $request->sdescription;
                $categoriesmeta->keywords = $request->skeywords;
                $categoriesmeta->save();
                
                
            }
            
            
           
            return redirect(route('admins.category'))->with([
                'msg'=>'Category Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $categories=Category::all();
        return view('admins.category',compact('categories','edit','seo'));
    }
    
    public function colors(Request $request,$id=0,$delete=null){
        $edit=null;
        if(isset($delete) && $id>0){
            Colors::find($id)->delete();
            return redirect(route('admins.colors'))->with([
                'msg'=>'Colors Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $edit=Colors::find($id);
        }
        if ($request->isMethod('post')) {

            $request->validate([
                'name'=>'required|unique:categories,name,'.$request->hidden_id,
            ]);

            if($request->has('hidden_id')){
                $category=Colors::find($request->hidden_id);$category->name=$request->name;
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
            }else{
                
                $category=new Colors();
                $category->created_at=Date('Y-m-d h:i:s');
                $category->name=$request->name;
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
            }
            
            
           
            return redirect(route('admins.colors'))->with([
                'msg'=>'colors Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $categories=Colors::all();
        return view('admins.colors',compact('categories','edit'));
    }
    
    public function shap(Request $request,$id=0,$delete=null){
        $edit=null;
        if(isset($delete) && $id>0){
            Shap::find($id)->delete();
            return redirect(route('admins.shap'))->with([
                'msg'=>'Shap Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $edit=Shap::find($id);
        }
        if ($request->isMethod('post')) {

            $request->validate([
                'name'=>'required|unique:categories,name,'.$request->hidden_id,
            ]);

            if($request->has('hidden_id')){
                $category=Shap::find($request->hidden_id);$category->name=$request->name;
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
            }else{
                
                $category=new Shap();
                $category->created_at=Date('Y-m-d h:i:s');
                $category->name=$request->name;
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
            }
            
            
           
            return redirect(route('admins.shap'))->with([
                'msg'=>'Shap Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $categories=Shap::all();
        return view('admins.shap',compact('categories','edit'));
    }
    
    public function home_cats(Request $request,$id=0,$delete=null){
        $edit=null;
        if(isset($delete) && $id>0){
            DB::table('home_cats')->delete($id);
            return redirect(route('admins.home_cats'))->with([
                'msg'=>'Category Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $edit=DB::table('home_cats')->where('home_cats.id', $id)->first();
            // dd($edit);
        }
        if ($request->isMethod('post')) {

            $request->validate([
                'name'=>'required|unique:categories,name,'.$request->hidden_id,
            ]);

            if($request->has('hidden_id')){
                $in = array(
                        'updated_at' => Date('Y-m-d h:i:s'),
                        'name' => $request->name,
                        'status' => $request->status,
                        'sort' => $request->sort,
                        );
                $id = DB::table('home_cats')->where('id', $request->hidden_id)->update($in);
                
            }else{
                $in = array(
                        'updated_at' => Date('Y-m-d h:i:s'),
                        'created_at' => Date('Y-m-d h:i:s'),
                        'name' => $request->name,
                        'status' => $request->status,
                        'sort' => $request->sort,
                        );
                $id = DB::table('home_cats')->insert($in);;
                
            }
            
            
           
            return redirect(route('admins.home_cats'))->with([
                'msg'=>'Category Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $categories=DB::table('home_cats')->get();;
        return view('admins.home_cats',compact('categories','edit'));
    }
    
    public function clarity(Request $request,$id=0,$delete=null){
        $edit=null;
        if(isset($delete) && $id>0){
            DB::table('calarity')->delete($id);
            return redirect(route('admins.clarity'))->with([
                'msg'=>'Clarity Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $edit=DB::table('calarity')->where('calarity.id', $id)->first();
            // dd($edit);
        }
        if ($request->isMethod('post')) {

            $request->validate([
                'name'=>'required|unique:categories,name,'.$request->hidden_id,
            ]);

            if($request->has('hidden_id')){
                $in = array(
                        'updated_at' => Date('Y-m-d h:i:s'),
                        'name' => $request->name,
                        );
                $id = DB::table('calarity')->where('id', $request->hidden_id)->update($in);
                
            }else{
                $in = array(
                        'updated_at' => Date('Y-m-d h:i:s'),
                        'created_at' => Date('Y-m-d h:i:s'),
                        'name' => $request->name,
                        );
                $id = DB::table('calarity')->insert($in);;
                
            }
            
            
           
            return redirect(route('admins.clarity'))->with([
                'msg'=>'Size Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $categories=DB::table('calarity')->get();;
        return view('admins.calarity',compact('categories','edit'));
    }
    public function size(Request $request,$id=0,$delete=null){
        $edit=null;
        if(isset($delete) && $id>0){
            Size::find($id)->delete();
            return redirect(route('admins.size'))->with([
                'msg'=>'Size Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $edit=Size::find($id);
        }
        if ($request->isMethod('post')) {

            $request->validate([
                'name'=>'required|unique:categories,name,'.$request->hidden_id,
            ]);

            if($request->has('hidden_id')){
                $category=Size::find($request->hidden_id);$category->name=$request->name;
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
            }else{
                
                $category=new Size();
                $category->created_at=Date('Y-m-d h:i:s');
                $category->name=$request->name;
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
            }
            
            
           
            return redirect(route('admins.size'))->with([
                'msg'=>'Size Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $categories=Size::all();
        return view('admins.size',compact('categories','edit'));
    }
    
    public function subcategory(Request $request,$id=0,$delete=null)
    {
        $edit=null;
        if(isset($delete) && $id>0){
            SubCategory::find($id)->delete();
            return redirect(route('admins.subcategory'))->with([
                'msg'=>'SubCategory Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $edit=SubCategory::find($id);
        }
        if ($request->isMethod('post')) {

            $request->validate([
                'name'=>'required|unique:sub_categories,name,' . $request->hidden_id,
                'category_id'=>'required',
            ],['category_id.required'=> 'Category Must be Required']);

            $request->validate([
                'name'=>'required',
                'category_id'=>'required',
            ]);

            if($request->has('hidden_id')){
                $category=SubCategory::find($request->hidden_id);
            }else{
                $category=new SubCategory();
                $category->created_at=Date('Y-m-d h:i:s');
            }
            $category->name=$request->name;
            $category->slug=strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));
            $category->category_id=$request->category_id;
            $category->updated_at=Date('Y-m-d h:i:s');
            $category->save();
            return redirect(route('admins.subcategory'))->with([
                'msg'=>'SubCategory Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $categories=Category::all();
        $sub_categories=$users = SubCategory::leftJoin('categories', 'categories.id', '=', 'sub_categories.category_id')
        ->select('sub_categories.*', 'categories.name AS parent_category')
        ->get();
        return view('admins.subcategory',compact('categories','sub_categories','edit'));
    }
    
    public function news_letters(Request $request,$id=0,$delete=null)
    {
        if(isset($delete) && $id>0){
            Newsletter::find($id)->delete();
            return redirect(route('admins.news_letters'))->with([
                'msg'=>'Subcriber Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        $news_letters=Newsletter::get();
        return view('admins.news_letters',compact('news_letters'));
    }
    
    public function coupon(Request $request,$id=0,$delete=null){
        $edit=null;
        if(isset($delete) && $id>0){
            Coupon::find($id)->delete();
            return redirect(route('admins.coupon'))->with([
                'msg'=>'Coupon Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $edit=Coupon::find($id);
        }
        if ($request->isMethod('post')) {

            $request->validate([
                'code'=>'required|unique:coupons,code,'.$request->hidden_id,
                'discount'=>'required',
            ]);

            if($request->has('hidden_id')){
                $coupon=Coupon::find($request->hidden_id);
            }else{
                $coupon=new Coupon();
                $coupon->created_at=Date('Y-m-d h:i:s');
            }
            $coupon->code=$request->code;
            $coupon->discount=$request->discount;
            $coupon->updated_at=Date('Y-m-d h:i:s');
            $coupon->save();
            return redirect(route('admins.coupon'))->with([
                'msg'=>'Coupon Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $coupons=Coupon::all();
        return view('admins.coupons',compact('coupons','edit'));
    }
    
    public function products(Request $request,$id=0,$delete=null){
        
        $products=Product::select('products.*')->orderBy('products.id','DESC')->get();
        return view('admins.products',compact('products'));
    }
    
    public function review(Request $request,$id=0,$delete=null){
        $reviews=Rating::all();
        $r = DB::table('rating')->update(array('is_read'=>1));
        return view('admins.review',compact('reviews'));
    }
    
    public function pages(Request $request,$id=0,$delete=null){
        $pages = Pages::all();
        return view('admins.pages',compact('pages'));
    }
    public function msections(Request $request,$id=0,$delete=null){
        $pages = DB::table('sections')->get();
        foreach($pages as $k => $v)
        {
            $page = DB::table('pages')->where('pages.id', $v->menu)->first();
            if(isset($page->name))
            {
            $v->menu = $page->name;
            }
            else
            {
                $v->menu = ' ';
            }
        }
        return view('admins.msections',compact('pages'));
    }
    
    public function setting(Request $request,$id=0,$delete=null){
        if ($request->isMethod('post')){
            // dd($request);
            if($request->hidden_id){
                
                $setting=Setting::find($request->hidden_id);
                
                // if(isset($request->logo1)){
                //     $imageone = $request->logo1;
                //     $pimage_name = time().$imageone->getClientOriginalName();
                //     $imageone->move(public_path('/images/'),$pimage_name);
                //     $setting->logo1 = 'public/images/'.$pimage_name;
                // }
                
                if ($request->hasFile('logo1')) {
                    $file = $request->file('logo1');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'webp';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/'), $name);
                
                    $setting['logo1'] = 'public/images/'.$name;
                }
                
                
                // if(isset($request->logo)){
                //     $imageone = $request->logo;
                //     $pimage_name = time().$imageone->getClientOriginalName();
                //     $imageone->move(public_path('/images/'),$pimage_name);
                //     $setting->logo = 'public/images/'.$pimage_name;
                // }
                
               if ($request->hasFile('wlogo')) {
                    $file = $request->file('wlogo');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'webp';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/'), $name);
                
                    $setting['wlogo'] = 'public/images/'.$name;
                }
                
               if ($request->hasFile('logo')) {
                    $file = $request->file('logo');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'webp';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/'), $name);
                
                    $setting['logo'] = 'public/images/'.$name;
                }
                
                // if(isset($request->homepage_image_one)){
                //     $imageone = $request->homepage_image_one;
                //     $pimage_name = time().$imageone->getClientOriginalName();
                //     $imageone->move(public_path('/images/'),$pimage_name);
                //     $setting->homepage_image_one = 'public/images/'.$pimage_name;
                // }
                
                if ($request->hasFile('homepage_image_one')) {
                    $file = $request->file('homepage_image_one');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'webp';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/'), $name);
                
                    $setting['homepage_image_one'] = 'public/images/'.$name;
                }
                
                // if(isset($request->homepage_image_two)){
                //     $imageone = $request->homepage_image_two;
                //     $pimage_name = time().$imageone->getClientOriginalName();
                //     $imageone->move(public_path('/images/'),$pimage_name);
                //     $setting->homepage_image_two = 'public/images/'.$pimage_name;
                // }
                
                if ($request->hasFile('homepage_image_two')) {
                    $file = $request->file('homepage_image_two');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'webp';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/'), $name);
                
                    $setting['homepage_image_two'] = 'public/images/'.$name;
                }
                
                // if(isset($request->homepage_image_3)){
                //     $imageone = $request->homepage_image_3;
                //     $pimage_name = time().$imageone->getClientOriginalName();
                //     $imageone->move(public_path('/images/'),$pimage_name);
                //     $setting->homepage_image_3 = 'public/images/'.$pimage_name;
                // } 
                
                if ($request->hasFile('homepage_image_3')) {
                    $file = $request->file('homepage_image_3');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'webp';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/'), $name);
                
                    $setting['homepage_image_3'] = 'public/images/'.$name;
                }
                
                // if(isset($request->homepage_image_4)){
                //     $imageone = $request->homepage_image_4;
                //     $pimage_name = time().$imageone->getClientOriginalName();
                //     $imageone->move(public_path('/images/'),$pimage_name);
                //     $setting->homepage_image_4 = 'public/images/'.$pimage_name;
                // }
                
                if ($request->hasFile('homepage_image_4')) {
                    $file = $request->file('homepage_image_4');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'webp';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/'), $name);
                
                    $setting['homepage_image_4'] = 'public/images/'.$name;
                }
                
                if ($request->hasFile('homepage_image_5')) {
                    $file = $request->file('homepage_image_5');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'webp';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/'), $name);
                
                    $setting['homepage_image_5'] = 'public/images/'.$name;
                }
                
                if ($request->hasFile('homepage_image_6')) {
                    $file = $request->file('homepage_image_6');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'webp';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/'), $name);
                
                    $setting['homepage_image_6'] = 'public/images/'.$name;
                }
                
                
               
                $setting->email=$request->email;
                $setting->phone=$request->phone;
                $setting->site_title=$request->site_title;
                $setting->title=$request->title;
                $setting->description=$request->description;
                $setting->keywords=$request->keywords;
                 
                
               
                $setting->phonetwo=$request->phonetwo;
                $setting->instagram=$request->instagram;
                
                $setting->homepage_footer=$request->homepage_footer;
                $setting->homepage_img1d=$request->homepage_img1d;
                $setting->homepage_img2d=$request->homepage_img2d;
                $setting->homepage_img3d=$request->homepage_img3d;
                $setting->homepage_img4d=$request->homepage_img4d;
                $setting->homepage_img5d=$request->homepage_img5d;
                $setting->homepage_img6d=$request->homepage_img6d;
                $setting->shipping_charges=$request->shipping_charges;
               $setting->footer_text=$request->footer;
               $setting->news_text=$request->news_text;
                $setting->save();
            }
        }
        
        $edit = Setting::where('id', '=',  '1')->first();
        return view('admins.setting',compact('edit'));
    }
    
     public function learn_setting(Request $request,$id=0,$delete=null){
        if ($request->isMethod('post')){
            // dd($request);
            if($request->hidden_id){
                
                $setting=Learn_setting::find($request->hidden_id);
                
                
                
                if(isset($request->learn_img_1)){
                    $imageone = $request->learn_img_1;
                    $pimage_name = time().$imageone->getClientOriginalName();
                    $imageone->move(public_path('/images/'),$pimage_name);
                    $setting->learn_img_1 = 'public/images/'.$pimage_name;
                }
                
                if(isset($request->learn_img_2)){
                    $imageone = $request->learn_img_2;
                    $pimage_name = time().$imageone->getClientOriginalName();
                    $imageone->move(public_path('/images/'),$pimage_name);
                    $setting->learn_img_2 = 'public/images/'.$pimage_name;
                }
                
                if(isset($request->learn_img_3)){
                    $imageone = $request->learn_img_3;
                    $pimage_name = time().$imageone->getClientOriginalName();
                    $imageone->move(public_path('/images/'),$pimage_name);
                    $setting->learn_img_3 = 'public/images/'.$pimage_name;
                }
                
              
             
                $setting->p_1=$request->p_1;
                $setting->p2=$request->p2;
                $setting->p3=$request->p3;
                $setting->p4=$request->p4;
                $setting->p5=$request->p5;
                $setting->p6=$request->p6;
               
                $setting->save();
            }
        }
        
        $edit = Learn_setting::where('id', '=',  '1')->first();
        return view('admins.learn_setting',compact('edit'));
    }
    
    public function orders(Request $request){
        
        $orders=Order::where('status','1')->orderBy('id','DESC')->get();
        $r = DB::table('orders')->update(array('is_read'=>1));
        return view('admins.orders',compact('orders'));
        
    }
    public function delete_order(Request $request){
        $ids = $request->input('id', []);

        Order::whereIn('id', $ids)->delete();
        return redirect()->back()->with([
            'msg'=>'Selected records deleted successfully.',
            'msg_type'=>'success',
        ]);

        
    }
     public function contact(Request $request){
        $orders=Contact::orderBy('id','DESC')->get();
        return view('admins.contact',compact('orders'));
    }
    public function complete_orders(Request $request){
        $orders=Order::where('status','2')->orderBy('id','DESC')->get();
        return view('admins.corder',compact('orders'));
    }
    
    public function product_form(Request $request,$id=0)
    {
        //  dd($request);
        $edit=null;
        if ($request->isMethod('post')){
            
            if($request->colors !== null){
                if(count($request->colors) > 1){
                    $allcolors = implode(",",$request->colors);   
                }else{
                    // $allcolors = $request->colors;    
                    $allcolors = implode(",",$request->colors);   
                }
                
            }else{
                $allcolors = '';
            }
            $home_cats = '';
            if($request->home_cats !== null){
                if(count($request->home_cats) > 1){
                    $home_cats = implode(",",$request->home_cats);   
                }else{
                    // $allcolors = $request->colors;    
                    $home_cats = implode(",",$request->home_cats);   
                }
                
            }else{
                $allcolors = '';
            }
            
            if($request->category_id !== null){
                
                $cats = $request->category_id;
                
            }else{
                $cats = '';
            }
            
            
            if($request->hidden_id){
                // dd($_REQUEST);
                $product=Product::find($request->hidden_id);
                
                // if(isset($request->image_one)){
                //     $imageone = $request->image_one;
                //     $pimage_name = time().$imageone->getClientOriginalName();
                //     $imageone->move(public_path('/images/products/'),$pimage_name);
                //     $product->image_one = 'public/images/products/'.$pimage_name;
                // }
                
                if ($request->hasFile('image_one')) {
                    $file = $request->file('image_one');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'avif';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/products/'), $name);
                
                    $product['image_one'] = 'public/images/products/'.$name;
                }
                
                $product->category_id=$cats;
                $product->product_name=$request->product_name;
                $product->product_details=$request->product_details;
                
                $product->short_discriiption=$request->short_discriiption;
                $tags = preg_replace('/\s+/', '-', $request->tags);
                $product->tags=$tags;
                $product->shipping_price=$request->shipping_price;
                $product->slug=$request->slug;
                $product->brand=$request->brand;
                $product->ptype=$request->ptype;
                $product->subcategory_id=$request->subcategory_id;
                if(isset($home_cats))
                $product->home_cats=$home_cats;
                else
                $product->home_cats='';
                $product->selling_price=$request->selling_price;
                $product->discount_price=$request->discount_price;
                $product->product_quantity=$request->product_quantity;
                $product->size=$request->size;
                $product->made_in=$request->made_in;
                $product->main_slider=isset($request->main_slider) ? 1 : 0;
                $product->hot_deal=isset($request->hot_deal) ? 1 : 0;
                $product->New_Arrival=isset($request->New_Arrival) ? 1 : 0;
                $product->Featured=isset($request->Featured) ? 1 : 0;
                $product->best_rated=isset($request->best_rated) ? 1 : 0;
                $product->mid_slider=isset($request->mid_slider) ? 1 : 0;
                $product->hot_new=isset($request->hot_new) ? 1 : 0;
                $product->Sale=isset($request->Sale) ? 1 : 0;
                $product->status=1;
                
                // dd($request->short_discriiption);
                $product->save();
                
                // dd($request->gallary_images);
                
                 if ($request->hasFile('gallary_images')) {
                foreach ($request->file('gallary_images') as $image) {
                    $extension = $image->getClientOriginalExtension();
                    $extension = 'avif';
                    
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    
                    
                    $image->move(public_path('gallery/'), $filename);
                    
                Gallerie::create([
                    'product_id' => $request->hidden_id,
                    'photo' =>url('/').'/public/gallery/'. $filename,
                ]);
            }
                 }

                
                
                
    
                
                $seo = ProductsToMeta::where('pid', '=',  $request->hidden_id)->first();
                if($seo !== null){
                    $seo->title = $request->stitle;
                    $seo->description = $request->sdescription;
                    $seo->keywords = $request->skeywords;
                    $seo->save();
                }else{
                    $seo = new ProductsToMeta;
                    $seo['title'] = $request->stitle;
                    $seo['description'] = $request->sdescription;
                    $seo['keywords'] = $request->skeywords;
                    $seo['pid'] = $request->hidden_id;
                    $seo->save();
                }
                
                
            }else{
                // dd($request);
                $product=new Product();
                
                // if(isset($request->image_one)){
                //     $imageone = $request->image_one;
                //     $pimage_name = time().$imageone->getClientOriginalName();
                //     $imageone->move(public_path('/images/products/'),$pimage_name);
                //     $product->image_one = 'public/images/products/'.$pimage_name;
                // }
                if ($request->hasFile('image_one')) {
                    $file = $request->file('image_one');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'webp';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/products/'), $name);
                
                    $product['image_one'] = 'public/images/products/'.$name;
                }
                if($request->category_id !== null){
                
                $cats = $request->category_id;
                
            }else{
                $cats = '';
            }
                
                $product->slug= strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->product_name)));
                
                $product->category_id=$cats;
                $product->product_name=$request->product_name;
                $product->product_details=$request->product_details;
                $product->short_discriiption=$request->short_discriiption;
                $tags = preg_replace('/\s+/', '-', $request->tags);
                $product->tags=$tags;
                $product->shipping_price=$request->shipping_price;
                $product->slug=$request->slug;
                $product->brand=$request->brand;
                $product->ptype=$request->ptype;
                $product->subcategory_id=$request->subcategory_id;
                if(isset($home_cats))
                $product->home_cats=$home_cats;
                else
                $product->home_cats='';
                $product->selling_price=$request->selling_price;
                $product->discount_price=$request->discount_price;
                $product->product_quantity=$request->product_quantity;
                $product->size=$request->size;
                $product->made_in=$request->made_in;
                $product->main_slider=isset($request->main_slider) ? 1 : 0;
                $product->hot_deal=isset($request->hot_deal) ? 1 : 0;
                $product->New_Arrival=isset($request->New_Arrival) ? 1 : 0;
                $product->Featured=isset($request->Featured) ? 1 : 0;
                $product->best_rated=isset($request->best_rated) ? 1 : 0;
                $product->mid_slider=isset($request->mid_slider) ? 1 : 0;
                $product->hot_new=isset($request->hot_new) ? 1 : 0;
                $product->Sale=isset($request->Sale) ? 1 : 0;
                $product->status=1;
                
                
                $product->save();
    
                $lastid = $product->id;
                if ($request->hasFile('gallary_images')) {
                foreach ($request->file('gallary_images') as $image) {
                    $extension = $image->getClientOriginalExtension();
                    $extension = 'avif';
                    
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    
                    
                    $image->move(public_path('gallery/'), $filename);
                    
                Gallerie::create([
                    'product_id' => $lastid,
                    'photo' =>url('/').'/public/gallery/'. $filename,
                ]);
            }
                }
                
               
                $seo = new ProductsToMeta;
                $seo['title'] = $request->stitle;
                $seo['description'] = $request->sdescription;
                $seo['keywords'] = $request->skeywords;
                $seo['pid'] = $lastid;
                $seo->save();
                
            }
            
            
            
            
            
            

            return redirect(route('admins.products'))->with([
                'msg'=>'Product Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $categories=Category::all();
        $scategories=SubCategory::all();
        
        $colors = Colors::all();
        $brands = Brand::all();
        $size = Size::all();
        $calarity = DB::table('calarity')->get();
        $home_cats = DB::table('home_cats')->where('status','=',1)->get();
        // dd($home_cats);
        $shap = Shap::all();
        if($id>0)
        $edit=Product::findorFail($id);
        $seo=ProductsToMeta::where('pid',$id)->get();
        // dd($seo);
        
        
        
        return view('admins.product_form',compact('categories','scategories','brands','edit','seo','colors','size','calarity','shap','home_cats'));
    }
    private function convertToWebp($sourcePath)
    {
        $image = imagecreatefromstring(file_get_contents($sourcePath));
        imagewebp($image, $sourcePath . '.webp', 80);
        imagedestroy($image);
    }
    
    public function page_form(Request $request,$id=0)
    {
         
         
        $edit=null;
        if ($request->isMethod('post')){
            if(isset($_REQUEST['section']))
            {
                if(empty($request->name)){
                return back()->with([
                    'msg'=>'section name is Required!',
                    'msg_type'=>'danger',
                ]);
                if(empty($request->menu)){
                return back()->with([
                    'msg'=>'section Parent menu is Required!',
                    'msg_type'=>'danger',
                ]);
            }
                
            }
            }
            else
            {
            if(empty($_REQUEST['name'])){
                
                return back()->with([
                    'msg'=>'name field Required!',
                    'msg_type'=>'danger',
                ]);
            }
            }
            
            if($request->hidden_id){
                if(isset($_REQUEST['section']))
                {
                    $in = array(
                        'name' => $request->name,
                        'position' => $request->position,
                        'status' => $request->status,
                        'menu' => $request->menu,
                        'content'=>utf8_encode($request->content),
                        'menu_type' => $request->menu_type,
                        );
                        $id = DB::table('sections')->where('id', $request->hidden_id)->update($in);
                        if($id)
                        {
                            return redirect(route('admins.msections'))->with([
                'msg'=>'Section update Successfully',
                'msg_type'=>'success',
            ]);
                        }
                }
                else
                {
                    $page = Pages::find($request->hidden_id);
                    $page->name=$request->name;
                    $page->menu_type=$request->menu_type;
                    $page->slug=$request->slug;
                    $page->content=utf8_encode($request->content);
                    $page->status=$request->status;
                    $page->position=$request->position;
                    $page->section=$request->sections;
                    $page->page_image_status=$request->image_status;
                    if($request->page_image_one)
                    {
                        $file = $request->page_image_one;
                        
                        $name = time().$file->getClientOriginalName();
                        $file->move(public_path('img/slider'),$name);
                        $page['page_image'] = $name;
                    }
                    
                    $page->parent=$request->parent;
                     $page->route=$request->route;
                    $page->save();
                }
                
            }else{
                
                if(isset($_REQUEST['section']))
                {
                    $in = array(
                        'name' => $request->name,
                        'position' => $request->position,
                        'status' => $request->status,
                        'menu' => $request->menu,
                        );
                        $id = DB::table('sections')->insert($in);
                        if($id)
                        {
                            return redirect(route('admins.msections'))->with([
                'msg'=>'Section add Successfully',
                'msg_type'=>'success',
            ]);
                        }
                    
                }
                else
                {
                if(isset($_REQUEST['section']))
                {
                    return back()->with([
                        'msg'=>'name field is Required!',
                        'msg_type'=>'danger',
                    ]);
                }
                
                $page = new Pages();
                $page->name=$request->name;
                $page->slug=$request->slug;
                $page->content=$request->content;
                $page->position=$request->position;
                $page->status=$request->status;
                $page->parent=$request->parent;
                $page->section=$request->sections;
                 $page->page_image_status=1;
                    if($request->page_image_one)
                    {
                        $file = $request->page_image_one;
                        
                        $name = time().$file->getClientOriginalName();
                        $file->move(public_path('img/slider'),$name);
                        $page['page_image'] = $name;
                    }
                    
                $page->route=$request->route;
                $page->save();
            }   
            }
            
            return redirect(route('admins.pages'))->with([
                'msg'=>'pages Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $page = Pages::all();
        $sections = DB::table('sections')->get();
        
        
        if(isset($_REQUEST['section']))
        {
            if($id > 0)
            {
                $edit = DB::table('sections')->where('sections.id', $id)->first();
            }
            return view('admins.section_form',compact('page','edit'));
        }
        else{
            if($id>0){
            $edit=Pages::findorFail($id);
        }
        $categories=Category::all();
        // dd($categories);
        $products=Product::all();
            return view('admins.page_form',compact('page','edit','sections','categories','products'));
        }
        $categories=Category::all();
        $products=Product::all();
        return view('admins.page_form',compact('page','edit','categories','products'));
    }
    public function get_subCategory_html(Request $request)
    {
        $options="";
        $sub_categories=SubCategory::where('category_id',$request->category_id)->get();
        $sub_cat_id=$request->sub_category_id;
        if(count($sub_categories)>0){
            foreach($sub_categories as $sub_category)
            {
                $selected=$sub_cat_id>0 && $sub_category->id==$sub_cat_id ? "selected" : null;
                $options.='<option '.$selected.' value="'.$sub_category->id.'">'.$sub_category->name.'</option>';
            }
        }
        echo $options;
    }
    public function product_delete($id)
    {
        $product=Product::find($id);
        $img_one=public_path().'/'.$product->image_one;
        if(\File::exists($img_one)){
            \File::delete($img_one);
        } 
        $img_two=public_path().'/'.$product->image_two;
        if(\File::exists($img_two)){
            \File::delete($img_two);
        } 
        $img_three=public_path().'/'.$product->image_three;
        if(\File::exists($img_three)){
            \File::delete($img_three);
        } 
        $product->delete();
        return redirect(route('admins.products'))->with([
            'msg'=>'Product Deleted Successfully',
            'msg_type'=>'success',
        ]);
    }
    public function gallery_delete($id)
    {
        $gallery=Gallerie::find($id);
        $img_one=public_path().'/'.$gallery->photo;
        if(\File::exists($img_one)){
            \File::delete($img_one);
        } 
        $gallery->delete();
        return redirect()->back()->with([
            'msg'=>'Gallery Image Deleted Successfully',
            'msg_type'=>'success',
        ]);
    }
    public function review_delete($id)
    {
        $product=Rating::find($id);
        $product->delete();
        return redirect(route('admins.review'))->with([
            'msg'=>'Review Deleted Successfully',
            'msg_type'=>'success',
        ]);
    }
    public function order_delete($id)
    {
        $product=Order::find($id);
        $product->delete();
        return redirect(route('admins.orders'))->with([
            'msg'=>'Order Deleted Successfully',
            'msg_type'=>'success',
        ]);
    }
    public function meg_delete($id)
    {
        $product=Contact::find($id);
        $product->delete();
        return redirect(route('admins.contact'))->with([
            'msg'=>'Message Deleted Successfully',
            'msg_type'=>'success',
        ]);
    }
    
    public function order_edit($id)
    {
        $edit=Order::findorFail($id);
        // $pro = Product::findorFail($edit->pid);
        
        
        return view('admins.edit_order',compact('edit'));
    }
    private function send_grid($to,$subj, $html,$from = 'info@live-x-martplace.com')
    {
        $arrayEmails = [$to];
$emailSubject = $subj;
$emailBody = $html;
$_SESSION['email_msg'] = $html ;

Mail::send('emails.normal',
	['html' => $emailBody],
	function($message) use ($arrayEmails, $emailSubject) {
		$message->to($arrayEmails)
        ->subject($emailSubject)->setBody($_SESSION['email_msg'], 'text/html');
        unset($_SESSION['email_msg']);
	}
);
        return true;


    }
    
    public function up_delivery_status(Request $request)
    {
        $order=Order::find($request->id);
        if($request->dstatus == 1){
           $order->status = 2;
           $order->dstatus = $request->dstatus;
           $order->shipping_company = $request->company;
           $order->track_url = $request->track_url;
           $order->track_no = $request->track_no;
           $order->note = $request->note;
           $order->save();
           
           $order_data = Order::where('id',$order->id)->get();
            if($order_data){
               $data = [
                    'order' =>$order_data ,
                    
                ];
                $to_email = $order->email;
                $html = view('emails.p_order', $data)->render();
                $this->send_grid($to_email,'Proceed Order', $html,'orders@live-x-martplace.com');
            }
           return redirect(route('admins.complete_orders'))->with([
            'msg'=>'Order Updated Successfully',
            'msg_type'=>'success',
        ]);
        }
        elseif($request->dstatus == 2){
           $order->status = 2;
           $order->dstatus = $request->dstatus;
            $order->shipping_company = $request->company;
           $order->track_url = $request->track_url;
           $order->track_no = $request->track_no;
           $order->note = $request->note;
           $order->save();
           $order_data = Order::where('id',$order->id)->get();
            if($order_data){
                $to_name = $order->name;
            $to_email = $order->email;
            $data = [
                    'order' =>$order_data ,
                    
                ];
                $to_email = $order->email;
                $html = view('emails.d_order', $data)->render();
                $this->send_grid($to_email,'Order Delivery Email', $html,'orders@live-x-martplace.com');
                   return redirect(route('admins.orders'))->with([
            'msg'=>'Order Updated Successfully',
            'msg_type'=>'success',
        ]); 
            }
        
        }elseif($request->dstatus == 3){
            $order->status = 1;
           $order->dstatus = $request->dstatus;
            $order->shipping_company = $request->company;
           $order->track_url = $request->track_url;
           $order->track_no = $request->track_no;
           $order->note = $request->note;
           $order->save();
           
           return redirect(route('admins.orders'))->with([
            'msg'=>'Order Updated Successfully',
            'msg_type'=>'success',
        ]); 
        }elseif($request->dstatus == 4){
            $order->status = 2;
           $order->dstatus = $request->dstatus;
            $order->shipping_company = $request->company;
           $order->track_url = $request->track_url;
           $order->track_no = $request->track_no;
           $order->note = $request->note;
           $order->save();
           $order_data = Order::where('id',$order->id)->get();
            if($order_data){
                $to_name = $order->name;
            $to_email = $order->email;
            $data = [
                    'order' =>$order_data ,
                    
                ];
                $html = view('emails.c_order', $data)->render();
                $this->send_grid($to_email,'Order Delivery Email', $html,'orders@live-x-martplace.com');
            
            }
           return redirect(route('admins.complete_orders'))->with([
            'msg'=>'Order Updated Successfully',
            'msg_type'=>'success',
        ]);
        }else{
            $order->status = 1;
           $order->dstatus = $request->dstatus;
            $order->shipping_company = $request->company;
           $order->track_url = $request->track_url;
           $order->track_no = $request->track_no;
           $order->note = $request->note;
           $order->save();
           return redirect(route('admins.orders'))->with([
            'msg'=>'Order Updated Successfully',
            'msg_type'=>'success',
        ]);
        }
        
        
       
    }
    
    public function page_delete($id)
    {
        $product=Pages::find($id);
        $product->delete();
        return redirect(route('admins.pages'))->with([
            'msg'=>'Page Deleted Successfully',
            'msg_type'=>'success',
        ]);
    }
    public function section_delete($id)
    {
        $product=DB::table('sections')->delete($id);
        // $product->delete();
        return redirect(route('admins.msections'))->with([
            'msg'=>'Section Deleted Successfully',
            'msg_type'=>'success',
        ]);
    }
    public function update_product_status(Request $request)
    {
        $product=Product::find($request->product_id);
        $product->status=$request->Status;
        $product->save();
        return response()->json([
            'msg'=>'Product Status Updated',
            'msg_type'=>'success',
        ]);
    }
    public function update_review_status(Request $request)
    {
        $product=Rating::find($request->review_id);
        $product->status=$request->Status;
        $product->save();
        return response()->json([
            'msg'=>'Review Status Updated',
            'msg_type'=>'success',
        ]);
    }
    public function media(Request $request,$id=0,$delete=null){
        $edit=null;
        if(isset($delete) && $id>0){
            Media::find($id)->delete();
            return redirect(route('admins.media'))->with([
                'msg'=>'Media Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $edit=Media::find($id);
        }
        if ($request->isMethod('post')) {


            if($request->has('hidden_id')){
                $category=Media::find($request->hidden_id);
                $category['name'] = $request->name;                
                $category['icon'] = $request->icon;                
                $category['link'] = $request->link; 
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
                
            }else{
                
                $category= new Media();
                $category->name = $request->name;                
                $category->icon = $request->icon;                
                $category->link = $request->link;
                $category->created_at=Date('Y-m-d h:i:s');
                $category->save();
                
            }
            
            return redirect(route('admins.media'))->with([
                'msg'=>'Media Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $categories=Media::all();
        return view('admins.media',compact('categories','edit'));
    }
    public function blog_category(Request $request,$id=0,$delete=null){
        $edit=null;
        if(isset($delete) && $id>0){
            Blog_Category::find($id)->delete();
            return redirect(route('admins.blog_category'))->with([
                'msg'=>'Blog Category Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $edit=Blog_Category::find($id);
        }
        if ($request->isMethod('post')) {

            // $request->validate([
            //     'title_english'=>'required|unique:post_categories,title_english,'.$request->hidden_id,
            //     'title_urdu'=>'required|unique:post_categories,title_urdu,'.$request->hidden_id,
            // ]);

            if($request->has('hidden_id')){
                $category=Blog_Category::find($request->hidden_id);
                
                $category->title_english=$request->title_english;
                $category->slug=$request->slug;
                $category->title_urdu=$request->title_urdu;
                $category->title=$request->title;
                $category->description=$request->seo_des;
                $category->keywords=$request->seo_key;
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
                
            }else{
                $category=new Blog_Category();
                $category->created_at=Date('Y-m-d h:i:s');
                $category->title_english=$request->title_english;
                $category->slug=$request->slug;
                $category->title_urdu=$request->title_urdu;
                $category->title=$request->title;
                $category->description=$request->seo_des;
                $category->keywords=$request->seo_key;
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
            }
            
            return redirect(route('admins.blog_category'))->with([
                'msg'=>'Blog Category Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $categories=Blog_Category::all();
        return view('admins.blog_category',compact('categories','edit'));
    }
    public function blog(Request $request,$id=0,$delete=null){
        
        $edit=null;
        if(isset($delete) && $id>0){
            Blog_Post::find($id)->delete();
            return redirect(route('admins.blog'))->with([
                'msg'=>'Blog  Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $edit=Blog_Post::find($id);
        }
        if ($request->isMethod('post')) {

            if($request->has('hidden_id')){
                $category=Blog_Post::find($request->hidden_id);
                
                $category->title_english=$request->title_english;
                $category->slug= strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->title_english)));
                 $category->description_english=$request->description;
                 $category->title=$request->title;
                 $category->description=$request->seo_des;
                 $category->keywords=$request->seo_key;
                $category->category_id=$request->category;
                // if(isset($request->image)){
                //     $imageone = $request->image;
                //     $pimage_name = time().$imageone->getClientOriginalName();
                //     $imageone->move(public_path('/images/blogs/'),$pimage_name);
                //     $category->image= 'public/images/blogs/'.$pimage_name;
                // }
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'webp';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/blogs/'), $name);
                
                    $category['image'] = 'public/images/blogs/'.$name;
                }
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
                
            }else{
                // dd($request);
                $category=new Blog_Post();
                $category->created_at=Date('Y-m-d h:i:s');
                $category->title_english=$request->title_english;
                $category->slug= strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->title_english)));
                $category->description_english=$request->description;
                $category->category_id=$request->category;
                $category->title=$request->title;
                 $category->description=$request->seo_des;
                 $category->keywords=$request->seo_key;
                // if(isset($request->image)){
                //     $imageone = $request->image;
                //     $pimage_name = time().$imageone->getClientOriginalName();
                //     $imageone->move(public_path('/images/blogs/'),$pimage_name);
                //     $category->image= 'public/images/blogs/'.$pimage_name;
                // }
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $extension = 'webp';
                    // Rename the file with a new extension
                    $name = time() . '.' . $extension;
                
                    // Move the uploaded file to the desired directory
                    $file->move(public_path('/images/blogs/'), $name);
                
                    $category['image'] = 'public/images/blogs/'.$name;
                }
                $category->updated_at=Date('Y-m-d h:i:s');
                $category->save();
                
            }
            
            return redirect(route('admins.blog'))->with([
                'msg'=>'Blog Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $categories=Blog_Post::all();
        return view('admins.blog_posts',compact('categories','edit'));
    }
    public function post_form(Request $request,$id=0)
    {

    }
    public function post_delete(){
        
    }

    public function slider(Request $request,$id=0,$delete=null)
    {
        $edit=null;
        if(isset($delete) && $id>0){
            $slider=Slider::find($id);
            $file_path=public_path().'/'.$slider->slider_image;
            if(\File::exists($file_path)){
                \File::delete($file_path);
            }           
            $slider->delete();
            return redirect(route('admins.slider'))->with([
                'msg'=>'Slider Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $edit=Slider::find($id);
        }
        if ($request->isMethod('post')) {
            if($request->has('hidden_id')){
                $slider=Slider::find($request->hidden_id);
                $slider->p = $request->p;
            }else{
                $slider=new Slider();
                $slider->created_at=Date('Y-m-d h:i:s');
                $slider->p = $request->p;
            }
            $slider->updated_at=Date('Y-m-d h:i:s');
            
           if ($request->hasFile('slider_image')) {
            $file = $request->file('slider_image');
            $extension = $file->getClientOriginalExtension();
            $extension = 'webp';
            // Rename the file with a new extension
            $name = time() . '.' . $extension;
        
            // Move the uploaded file to the desired directory
            $file->move(public_path('img/slider'), $name);
        
            $slider['slider_image'] = $name;
        }



            // if($request->slider_image)
            // {
            //     $file = $request->slider_image;
                
            //     $name = time().$file->getClientOriginalName();
            //     $file->move(public_path('img/slider'),$name);
            //     $slider['slider_image'] = $name;
                
                
                
            //     // if($request->hidden_id){
            //     //     $image=public_path().'/'.$slider->slider_image;
            //     //     if(\File::exists($image)){
            //     //         \File::delete($image);
            //     //     } 
            //     // }
            //     // $img_name=hexdec(uniqid()).'.'.$request->slider_image->getClientOriginalExtension();
            //     // $img_path='/images/sliders/'.$img_name;
            //     // \Image::make($request->slider_image)->save(public_path().'/images/sliders/'.$img_name);
            //     // $slider->slider_image=$img_path;
            // }
            
            $slider->save();
            return redirect(route('admins.slider'))->with([
                'msg'=>'Slider Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $sliders=Slider::all();
        $categories=Category::all();
        return view('admins.slider',compact('sliders','categories','edit'));
    }
    public function faq(Request $request,$id=0,$delete=null)
    {
      
        $edit=null;
        if(isset($delete) && $id>0){
            $slider=Faq::find($id);
           
            $slider->delete(); 
            return redirect(route('admins.faq'))->with([
                'msg'=>'Faq Deleted Successfully',
                'msg_type'=>'success',
            ]);
        }
        if($id>0 && !isset($delete)){
            $edit=DB::table('faqs')->where('id',$id)->first();
            return view('admins.faq',compact('edit'));
        }
        if ($request->isMethod('post')) {
            
            
            if($request->has('hidden_id')){
                DB::table('faqs')->where('id',$request->hidden_id)->update([
            'answer' =>  $request->answer,
            'question' =>  $request->question,
        ]);
            }else{
                $in = DB::table('faqs')->insert([
            'answer' =>  $request->answer,
            'question' =>  $request->question,
        ]);
            }
            
            return redirect(route('admins.faq'))->with([
                'msg'=>'Faq Saved Successfully',
                'msg_type'=>'success',
            ]);
        }
        $sliders=DB::table('faqs')->get();
        
        return view('admins.faq',compact('sliders','edit'));
    }
}
