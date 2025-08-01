@extends('layouts.master')

<!-- @section('title', 'Menu') -->

@section('content')

<div class="container-fluid py-5 d-flex justify-content-center">
    <div class="receipt border p-4 bg-white shadow" style="width: 450px;margin-top: 5rem">
        <h5 class="text-center mb-2">Pesanan Berhasil Dibuat! <br></h5>
        <hr>
        <h4 class="fw-bold text-center">Kode Bayar: <br><span class="text-primary">{{ $order->order_code }}</span></h4>
        <hr>
        <h5 class="mb-3 text-center">Detail Pesanan</h5>
        <table class="table table-borderless">
            <tbody>
                @foreach ($orderItems as $orderItem)
                <tr>
                    <td>{{ Str::limit($orderItem->item->name, 25) }} ({{ $orderItem->quantity }})</td>
                    <td class="text-end">Rp{{ number_format($orderItem->price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table class="table table-borderless">
            <tbody>
                <tr class="fw-bold border-top align-middle">
                    <td>Subtotal</td>
                    <td class="text-end">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr class="align-middle">
                    <td>PPN (10%)</td>
                    <td class="text-end">Rp{{ number_format($order->tax, 0, ',', '.') }}</td>
                </tr>
                <tr class="fw-bold border-bottom align-middle">
                    <td>Total</td>
                    <td class="text-end">Rp{{ number_format($order->grand_total, 0, ',', '.') }}</td>
                </tr>
            </tbody>

        </table>
        {{-- <hr> --}}
        <p class="small text-center px-5">Tunjukkan kode bayar ini ke kasir untuk menyelesaikan pembayaran. Jangan lupa senyum ya!</p>
        <hr>
        <a href="{{ route('menu') }}" class="btn btn-primary w-100">Kembali ke Menu</a>
    </div>
</div>

@endsection