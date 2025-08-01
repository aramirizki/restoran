<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
Use Illuminate\Support\Facades\Session;
Use App\Models\Order;
Use App\Models\OrderItem;
Use Illuminate\Support\Facades\Validator;
use App\Models\User;

class MenuController extends Controller
{

    // tampilkan halaman menu
    public function index(Request $request)
    {
        $tableNumber = $request->query('meja');
        if ($tableNumber) {
            Session::put('tableNumber', $tableNumber);
        }

        $items = Item::where('is_available', 1)->orderBy('name', 'asc')->get();

        return view('menu', compact('items', 'tableNumber'));
    }

    // tampilkan halaman keranjang
    public function cart()
    {
        $cart = Session::get('cart', []);
        return view('cart', compact('cart'));
    }




    // tambah barang ke keranjang
    public function addToCart(Request $request)
    {
        $menuId = $request->input('id');
        $menu = Item::find($menuId);

        if (!$menu) {
            return response()->json(['success' => 'Menu tidak ditemukan'], 404);
        }

        $cart = Session::get('cart', []);

        if (isset($cart[$menuId])) {
            $cart[$menuId]['qty'] += 1;
        } else {
            $cart[$menuId] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'image' => $menu->img,
                'qty' => 1
            ];
        }

        Session::put('cart', $cart);

        return response()->json(['success' => 'Berhasil ditambahkan ke keranjang!', 'cart' => $cart]);
    }

    // hapus barang dari keranjang
    public function removeFromCart(Request $request)
    {
        $itemId = $request->input('itemId');

        $cart = session()->get('cart', []);
        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            session()->put('cart', $cart);

            session()->flash('success', 'Item berhasil dihapus dari keranjang!');
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    // update jumlah barang di keranjang
    public function update(Request $request)
    {
        $itemId = $request->input('itemId');
        $newQty = $request->input('qty');

        if ($newQty < 1) {
            return response()->json(['success' => false]);
        }

        $cart = session()->get('cart', []);
        if (isset($cart[$itemId])) {
            $cart[$itemId]['qty'] = $newQty;
            session()->put('cart', $cart);
            session()->flash('success', 'Jumlah item berhasil diperbarui!');
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    // hapus semua barang di keranjang
    public function clearCart()
    {
        Session::forget('cart');
        return redirect()->route('cart')->with('success', 'Keranjang berhasil dikosongkan!');
    }

    // checkout
    public function checkout()
    {
        $cart = session::get('cart');
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang Anda kosong.');
        }

        $tableNumber = Session::get('tableNumber');

        return view('checkout', compact('cart','tableNumber'));
    }

    // proses checkout dan simpan order
    public function store(Request $request)
    {
        $cart = session::get('cart');
        $tableNumber = Session::get('tableNumber');

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang Anda kosong.');
        }

        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:15'
        ]);

        if ($validator->fails()) {
            return redirect()->route('checkout')->withErrors($validator);
        }

        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['qty'];

            $itemDetails[] = [
                'id'       => $item['id'],
                'price'    => (int) ($item['price'] + ($item['price'] * 0.1)),
                'quantity' => $item['qty'],
                'name'     => substr($item['name'], 0, 50),
            ];
        }



        $user = User::firstOrCreate([
            'username' => $request->username ?? $request->phone,
            'fullname' => $request->fullname, 
            'phone' => $request->phone,
            'role_id' => 4
            ]);

        $order = Order::create([
            'order_code' => 'ORD-' . strtoupper(uniqid()),
            'user_id' => $user->id,
            'subtotal' => $totalAmount,
            'tax' => $totalAmount * 0.1,
            'grand_total' => $totalAmount + ($totalAmount * 0.1),
            'status' => 'pending',
            'table_number' => $tableNumber,
            'payment_method' => $request->payment_method === 'tunai' ? 'cash' : $request->payment_method,
            'notes' => $request->notes,
        ]);

        foreach ($cart as $itemId => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $item['id'],
                'quantity' => $item['qty'],
                'price' => $item['price'] * $item['qty'],
                'tax' => ($item['price'] * $item['qty']) * 0.1,
                'total_price' => ($item['price'] * $item['qty'] + (($item['price'] * $item['qty']) * 0.1))
            ]);
        }

        Session::forget('cart');

        // return redirect()->route('menu')->with('success', 'Order berhasil dibuat!');

        
        return redirect()->route('checkout.success', ['orderId' => $order->order_code]);
        
    }


    // tampilkan halaman sukses order
    
    public function orderSuccess($orderId)
    {
        $order = Order::where('order_code', $orderId)->first();

        if (!$order) {
            return redirect()->route('menu')->with('error', 'Order tidak ditemukan.');
        }

        
        $orderItems = OrderItem::where('order_id', $order->id)->get();

        return view('success', compact('order', 'orderItems'));
    }


}
