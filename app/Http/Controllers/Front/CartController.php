<?php

namespace App\Http\Controllers\Front;

use App\Helpers\Api;
use App\Helpers\Cart;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function add(Request $request)
    {
        if (Cart::add($request->id, $request->qty ?? 1)) {
            return response()->json([
                'msg'=>'Product Added to Cart',
                'msg_type'=>'success',
                'qty'=> Cart::qty()
            ]);
        } else {
            return response()->json([
                'msg'=>'Item out of stock!',
                'msg_type'=>'danger',
            ]);
        }
    }

    public function remove($id)
    {
        Cart::remove($id);
        
        // Return JSON response for AJAX requests
        if (request()->wantsJson() || request()->ajax()) {
            return Api::setResponse('cart', Session::get('cart', []));
        }
        
        return redirect('/checkout')->with([
            'msg'=>'Cart item removed successfully',
            'msg_type'=>'success',
        ]);
    }

    public function increment(Request $request)
    {
        if (Cart::increase($request->id)) {
            return Api::setResponse('cart', Session::get('cart'));
        } else {
            return Api::setError('Item out of stock!');
        }
    }

    public function decrement(Request $request)
    {
        Cart::decrease($request->id);
        return Api::setResponse('cart', Session::get('cart'));
    }

    public function clear()
    {
        Session::forget('cart');
        Session::forget('coupen');
        Session::forget('check');
        return redirect()->back();
    }

    public function getCartData()
    {
        $cart = Session::get('cart', []);
        $cartData = [
            'qty' => 0,
            'amount' => 0,
            'items' => [],
            'shipping_charges' => 0
        ];

        // Get shipping charges from settings
        $setting = \DB::table('setting')->where('id', 1)->first();
        $shippingCharges = $setting ? ($setting->shipping_charges ?? 0) : 0;

        if (!empty($cart) && isset($cart['items'])) {
            $cartData['qty'] = $cart['qty'] ?? 0;
            $cartData['amount'] = $cart['amount'] ?? 0;
            $cartData['shipping_charges'] = $shippingCharges;
            
            // Re-index items array to remove gaps
            $cart['items'] = array_values($cart['items']);
            
            foreach ($cart['items'] as $item) {
                $product = \App\Models\Admins\Product::find($item['id']);
                if ($product) {
                    $cartData['items'][] = [
                        'id' => $item['id'],
                        'name' => $product->product_name,
                        'price' => $product->discount_price,
                        'qty' => $item['qty'],
                        'image' => !empty($product->image_one) ? custom_assets($product->image_one) : '/theme2/img/solo.webp'
                    ];
                }
            }
        }

        return response()->json(['cart' => $cartData]);
    }
}
