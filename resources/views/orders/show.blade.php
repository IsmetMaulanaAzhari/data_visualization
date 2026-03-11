@extends('layouts.app')

@section('title', 'Order Detail')

@section('content')
<div class="mb-6">
    <a href="{{ route('orders.index') }}" class="text-blue-500 hover:underline">
        <i class="fas fa-arrow-left mr-2"></i>Back to Orders
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Order Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Information</h2>
        <div class="space-y-3">
            <div>
                <p class="text-sm text-gray-500">Order Number</p>
                <p class="font-medium">{{ $order->order_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Order Date</p>
                <p class="font-medium">{{ $order->order_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <form action="{{ route('orders.updateStatus', $order) }}" method="POST" class="mt-1">
                    @csrf
                    @method('PATCH')
                    <select name="status" onchange="this.form.submit()" class="border rounded-lg px-3 py-1 text-sm">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </form>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Amount</p>
                <p class="text-xl font-bold text-green-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            </div>
        </div>

        <hr class="my-4">

        <h3 class="font-semibold text-gray-800 mb-2">Customer</h3>
        <div class="space-y-2">
            <p class="font-medium">{{ $order->customer->name }}</p>
            <p class="text-sm text-gray-500">{{ $order->customer->email }}</p>
            <p class="text-sm text-gray-500">{{ $order->customer->phone }}</p>
            <p class="text-sm text-gray-500">{{ $order->customer->address }}, {{ $order->customer->city }}</p>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Items</h2>
        <table class="w-full">
            <thead>
                <tr class="text-left text-gray-500 text-sm border-b">
                    <th class="pb-3">Product</th>
                    <th class="pb-3 text-right">Price</th>
                    <th class="pb-3 text-center">Qty</th>
                    <th class="pb-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $item)
                <tr class="border-b">
                    <td class="py-3">
                        <div class="font-medium">{{ $item->product->name }}</div>
                        <div class="text-sm text-gray-500">{{ $item->product->category->name }}</div>
                    </td>
                    <td class="py-3 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="py-3 text-center">{{ $item->quantity }}</td>
                    <td class="py-3 text-right font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="py-3 text-right font-semibold">Total:</td>
                    <td class="py-3 text-right font-bold text-lg text-green-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection