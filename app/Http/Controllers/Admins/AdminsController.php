<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Models\Product\Order;
use App\Models\Product\Booking;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;



class AdminsController extends Controller
{

    public function viewLogin()
    {
        return view('admins.login');
    }

    public function checkLogin(Request $request)
    {
        $remember_me = $request->has('remember_me') ? true : false;

        if (auth()->guard('admin')->attempt(['email' => $request->input("email"), 'password' => $request->input("password")], $remember_me)) {

            return redirect()->route('admins.dashboard');
        }
        return redirect()->back()->with(['error' => 'error logging in']);

        // $request->validate([
        //     'email' => 'required|email',
        //     'password' => 'required',
        // ]);
        // $credentials = $request->only(['email', 'password']);
        // if (!auth()->attempt($credentials)) {
        //     return back()->withErrors(['Invalid email or password']);
        // }
        // return redirect()->route('admin.dashboard');
    }

    public function index()
    {
        $productsCount = Product::select()->count();
        $ordersCount = Order::select()->count();
        $bookingsCount = Booking::select()->count();
        $adminsCount = Admin::select()->count();
        return view('admins.index', compact('productsCount', 'ordersCount', 'bookingsCount', 'adminsCount'));
    }

    // displayAllAdmins
    public function displayAllAdmins()
    {
        $allAdmins = Admin::select()->orderBy('id', 'desc')->get();
        return view('admins.alladmins', compact('allAdmins'));
    }

    //createAdmins
    public function createAdmins()
    {
        return view('admins.createadmins');
    }

    // storeAdmins
    public function storeAdmins(Request $request)
    {
        $request->validate([
            'name' => 'required|max:40',
            'email' => 'required|max:40',
            'password' => 'required|max:40',
            // 'confirm_password' => 'required|same:password',
        ]);


        $storeAdmins = Admin::Create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if ($storeAdmins) {
            return redirect::route('all.admins')->with(['success' => 'Admin created successfully']);
        }
    }

    //displayAllOrders
    public function displayAllOrders()
    {
        $allOrders = Order::select()->orderBy('id', 'desc')->get();

        return view('admins.allorders', compact('allOrders'));
    }

    //editOrder
    public function editOrders($id)
    {
        $editOrder = Order::find($id);

        return view('admins.editorders', compact('editOrder'));
    }
    //updateOrders
    public function updateOrders(Request $request, $id)
    {
        $order = Order::find($id);

        $order->update($request->all());

        if ($order) {

            return redirect::route('all.orders')->with(['update' => 'Order status updated successfully']);
        }
    }

    //deleteOrders
    public function deleteOrders($id)
    {
        $deleteOrder = Order::find($id);
        $deleteOrder->delete();
        return redirect::route('all.orders')->with(['delete' => 'Order deleted successfully']);
    }

    //displayAllProducts
    public function displayAllProducts()
    {
        $products = Product::select()->orderBy('id', 'desc')->get();
        return view('admins.allproducts', compact('products'));
    }

    //createProducts
    public function createProducts()
    {
        return view('admins.createproducts');
    }
    //storeProducts
    public function storeProducts(Request $request)
    {
        $request->validate([
            'name' => 'required|max:40',
            'price' => 'required|max:40',
            'image' => 'required|mimes:jpg,jpeg,png,gif,svg|max
            :2048',
            'description' => 'required|max:40',
            'type' => 'required|max:40',

        ]);

        $destinationPath = 'assets/images/';
        $myimage = $request->image->getClientOriginalName();
        $request->image->move(public_path($destinationPath), $myimage);

        $storeProducts = Product::Create([
            'name' => $request->name,
            'price' => $request->price,
            'image' => $myimage,
            'description' => $request->description,
            'type' => $request->type,
        ]);

        if ($storeProducts) {
            return redirect::route('all.products')->with(['success' => 'Product created successfully']);
        }
    }


    //deleteProducts
    public function deleteProducts($id)
    {
        $product = Product::find($id);

        if (File::exists(public_path('assets/images/' . $product->image))) {
            File::delete(public_path('assets/images/' . $product->image));
        } else {
            //dd('File does not exists.');
        }

        $product->delete();

        if ($product) {

            return redirect::route('all.products')->with(['delete' => 'Product deleted successfully']);
        }
    }

    //displayAllBookings
    public function displayAllBookings()
    {
        $bookings = Booking::select()->orderBy('id', 'desc')->get();
        return view('admins.allbookings', compact('bookings'));
    }

    //editBookings
    public function editBookings($id)
    {
        $booking = Booking::find($id);
        return view('admins.editbookings', compact('booking'));
    }

    //updateBookings
    public function updateBookings(Request $request, $id)
    {
        $booking = Booking::find($id);
        $booking->update($request->all());
        if ($booking) {
            return redirect::route('all.bookings')->with(['update' => 'Booking status updated successfully']);
        }
    }

    //deleteBookings
    public function deleteBookings($id)
    {
        $booking = Booking::find($id);
        $booking->delete();
        if ($booking) {
            return redirect::route('all.bookings')->with(['delete' => 'Booking deleted successfully']);
        }
    }
}
