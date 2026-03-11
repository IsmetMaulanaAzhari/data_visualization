@extends('layouts.app')

@section('title', 'Customer Detail')

@section('content')
<div class="mb-6">
    <a href="{{ route('customers.index') }}" class="text-blue-500 hover:underline">
        <i class="fas fa-arrow-left mr-2"></i>Back to Customers
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Customer Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h2>
        <div class="space-y-3">
            <div>
                <p class="text-sm text-gray-500">Name</p>
                <p class="font-medium">{{ $customer->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-medium">{{ $customer->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Phone</p>
                <p class="font-medium">{{ $customer->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">City</p>
                <p class="font-medium">{{ $customer->city ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Address</p>
                <p class="font-medium">{{ $customer->address ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Orders</h2>
        <table class="w-full">
            <thead>
                <tr class="text-left text-gray-500 text-sm border-b">
                    <th class="pb-3">Order Number</th>
                    <th class="pb-3">Date</th>
                    <th class="pb-3">Amount</th>
                    <th class="pb-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customer->orders as $order)
                <tr class="border-b">
                    <td class="py-3">
                        <a href="{{ route('orders.show', $order) }}" class="text-blue-500 hover:underline">{{ $order->order_number }}</a>
                    </td>
                    <td class="py-3 text-gray-500">{{ $order->order_date->format('d M Y') }}</td>
                    <td class="py-3">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td class="py-3">
                        @switch($order->status)
                            @case('completed')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Completed</span>
                                @break
                            @case('pending')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Pending</span>
                                @break
                            @case('processing')
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Processing</span>
                                @break
                            @case('cancelled')
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Cancelled</span>
                                @break
                        @endswitch
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-3 text-center text-gray-500">No orders yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection