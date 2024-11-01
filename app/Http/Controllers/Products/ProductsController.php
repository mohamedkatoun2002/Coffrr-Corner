<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Models\Product\Cart;
use App\Models\Product\Order;
use App\Models\Product\Booking;
// use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
// use Auth;
// use Redirect;
use Session;

class ProductsController extends Controller
{

    public function singleProduct($id)
    {
        $product = Product::find($id);

        $relatedProducts = Product::where('type', $product->type)
            ->where('id', '!=', $id)
            ->take('4')
            ->orderBy('id', 'desc')
            ->get();

        if (isset(Auth::user()->id)) {

            //checking for products in cart

            $checkingInCart = Cart::where('pro_id', $id)
                ->where('user_id', Auth::user()->id)
                ->count();
            return view('products.productsingle', compact('product', 'relatedProducts', 'checkingInCart'));
        } else {

            return view('products.productsingle', compact('product', 'relatedProducts'));
        }
    }

    public function addCart(Request $request, $id)
    {

        $addCart = Cart::create([
            "pro_id" => $request->pro_id,
            "name" => $request->name,
            "image" => $request->image,
            "price" => $request->price,
            "user_id" => Auth::user()->id,
        ]);

        return Redirect::route('product.single', $id)->with(['success' => "Product added to cart successfully"]);
        // return response()->json(['message' => 'Product added to cart successfully']);
    }

    public function cart()
    {
        $cartProducts = Cart::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get();

        $totalPrice = Cart::where('user_id', Auth::user()->id)
            ->sum('price');

        return view('products.cart', compact('cartProducts', 'totalPrice'));
    }

    public function deleteProductCart($id)
    {
        $deleteProductCart = Cart::where('pro_id', $id)->where('user_id', Auth::user()->id);

        $deleteProductCart->delete();

        if ($deleteProductCart) {

            return Redirect::route('cart')->with(['delete' => "Product deleted from cart successfully"]);
        }
    }

    public function prepareCheckout(Request $request)
    {
        $value = $request->price;

        $price = Session::put('price', $value);

        $newPrice = Session::get($price);


        if ($newPrice > 0) {

            return Redirect::route('checkout');
        }
    }
    public function checkout()
    {
        return view('products.checkout');
    }

    public function storeCheckout(Request $request)
    {

        $checkout = Order::create($request->all());


        return redirect::route('products.pay');
    }

    public function payWithPaypal()
    {
        return view('products.pay');
    }

    public function success()
    {
        $deleteItems = Cart::where('user_id', Auth::user()->id);
        $deleteItems->delete();

        if ($deleteItems) {

            Session::forget('price');

            return view('products.success');
        }
    }

    public function BookTables(Request $request)
    {

        Request()->validate([
            "first_name" => "required|max:40",
            "last_name" => "required|max:40",
            "date" => "required",
            "time" => "required",
            "phone" => "required|max:40",
            "message" => "required",
        ]);

        if ($request->date > date('n/j/Y')) {

            $bookTables = Booking::create($request->all());

            if ($bookTables) {

                return redirect::route('home')->with(['booking' => "You booked a table successfully"]);
            }
        } else {
            return Redirect::route('home')->with(['date' => "Invalid date, Choose a date in the future"]);
        }
    }

    public function menu()
    {
        $desserts = Product::select()->where("type", "desserts")->orderBy('id', 'desc')->take(4)->get();

        $drinks = Product::select()->where("type", "drinks")->orderBy('id', 'desc')->take(4)->get();

        return view('products.menu', compact('desserts', 'drinks'));
    }
}
