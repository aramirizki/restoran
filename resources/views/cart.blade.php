@extends('layouts.master')

@section('title', 'Keranjang')

@section('content')
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Keranjang</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item active text-primary">Silakan periksa pesanan anda</li>
    </ol>
</div>
<div class="container-fluid py-5">

    <div class="container py-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (empty($cart))
            <h4 class="text-center">Keranjang anda kosong</h4>
        @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Menu</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $subtotal = 0;
                    @endphp


                        @foreach ($cart as $item)
                            @php
                                $itemTotal = $item['price'] * $item['qty'];
                                $subtotal += $itemTotal;
                            @endphp
                            <tr class="align-middle">
                                <td>
                                    <img src="{{ asset('img_item_upload/' . $item['image']) }}" class="img-fluid rounded-circle" style="width: 80px; height: 80px;" alt="{{ $item['name'] }}" onerror="this.onerror=null;this.src='{{ $item['image'] }}';">
                                </td>
                                <td>{{ $item['name'] }}</td>
                                <td>Rp{{ number_format($item['price'], 0, ',', '.') }}</td>
                                {{-- <td>{{ $item['qty'] }}</td> --}}
                                <td>
                                    <div class="input-group quantity mt-4" style="width: 100px;">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-minus rounded-circle bg-light border" onclick="updateQuantity({{ $item['id'] }}, -1)">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <input type="text" class="form-control form-control-sm text-center border-0 bg-transparent" id="qty-{{ $item['id'] }}" value="{{ $item['qty'] }}" readonly>
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-plus rounded-circle bg-light border" onclick="updateQuantity({{ $item['id'] }}, 1)">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>Rp{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="if(confirm('Apakah Anda yakin ingin menghapus item ini?')) { removeItemFromCart({{ $item['id'] }}) }">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach

                </tbody>
            </table>
        </div>

        @php
            $tax = $subtotal * 0.1;
            $total = $subtotal + $tax;
        @endphp

        <div class="d-flex justify-content-end">
            <a href="{{ route('cart.clear') }}" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin mengosongkan keranjang?')">Kosongkan Keranjang</a>
        </div>
        <div class="row g-4 justify-content-end mt-1">
            <div class="col-8"></div>
            <div class="col-sm-8 col-md-7 col-lg-6 col-xl-4">
                <div class="bg-light rounded">
                    <div class="p-4">
                        <h3 class="fw-bold mb-4">Total <span class="fw-normal">Pesanan</span></h3>
                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="mb-0 me-4">Subtotal</h5>
                            <p class="mb-0">Rp{{ number_format($subtotal, 0, ',', '.') }}</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p class="mb-0 me-4">PPN (10%)</p>
                            <div class="">
                                <p class="mb-0">Rp{{ number_format($tax, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="py-4 mb-4 border-top d-flex justify-content-between">
                        <h4 class="mb-0 ps-4 me-4">Total</h4>
                        <h5 class="mb-0 pe-4">Rp{{ number_format($total, 0, ',', '.') }}</h5>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <div class="mb-0">
                        <a href="{{ route('checkout') }}" class="btn border-secondary py-3 text-primary text-uppercase mb-4" type="button">Lanjut ke Pembayaran</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
    function updateQuantity(itemId, change) {
        var qtyInput = document.getElementById('qty-' + itemId);
        var currentQty = parseInt(qtyInput.value);
        var newQty = currentQty + change;

        // Jika jumlah kurang dari atau sama dengan 0, hapus item
        if (newQty <= 0) {
            if (confirm("Apakah Anda yakin ingin menghapus item ini?")) {
                removeItemFromCart(itemId);
            }
            return;
        }

        // Kirim permintaan AJAX untuk memperbarui jumlah
        fetch("{{ route('cart.update') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                itemId: itemId,
                qty: newQty
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                qtyInput.value = newQty;  // Update jumlah di input
                location.reload();  // Reload halaman untuk memperbarui total dan subtotal
            } else {
                alert('Gagal memperbarui keranjang');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }

    // Fungsi untuk menghapus item dari keranjang
    function removeItemFromCart(itemId) {
        fetch("{{ route('cart.remove') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                itemId: itemId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();  // Reload halaman untuk memperbarui keranjang
            } else {
                alert('Gagal menghapus item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus item');
        });
    }
</script>


@endsection
