<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Item;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $tableNumber= $request->query('meja');

        if ($tableNumber) {
            Session::put('tableNumber', $tableNumber);
        } 
        
        $items = Item::where('is_available', 1)->orderBy('name','asc')->get();

        return view('customer.menu', compact('items', 'tableNumber'));
    }

    public function cart()
    {
        $cart = Session::get('cart');
        // $tableNumber = Session::get('tableNumber');
        return view('customer.cart', compact('cart'));
    }

    public function addToCart(Request $request){

        $menuId = $request->input('id');
        $menu = Item::find($menuId);

        if (!$menu) {
            return response()->json(['success' => 'menu tidak ditemukan'], 404);
        }

        $cart = Session::get('cart',[]);

        if(isset($cart[$menuId])){
            $cart[$menuId]['quantity'] += 1;
        } else {
            $cart[$menuId] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'image' => $menu->image,
                'quantity' => 1
            ];
        }
        
        Session::put('cart', $cart);

        return response()->json(['success' => 'Menu berhasil ditambahkan ke keranjang', 'cart' => $cart]);

    }


}
