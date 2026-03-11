@extends('layouts.app')

@section('title', 'New Order')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Create New Order</h1>
    <p class="text-gray-600">Create a new order</p>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Customer</label>
                <select name="customer_id" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Order Date</label>
                <input type="date" name="order_date" value="{{ date('Y-m-d') }}" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
        </div>

        <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Items</h3>
        
        <div id="orderItems">
            <div class="order-item grid grid-cols-12 gap-4 mb-4 items-end">
                <div class="col-span-6">
                    <label class="block text-gray-700 font-medium mb-2">Product</label>
                    <select name="items[0][product_id]" class="product-select w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }} (Stock: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-gray-700 font-medium mb-2">Quantity</label>
                    <input type="number" name="items[0][quantity]" min="1" value="1" class="quantity-input w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="col-span-3">
                    <label class="block text-gray-700 font-medium mb-2">Subtotal</label>
                    <input type="text" class="subtotal w-full border rounded-lg px-4 py-2 bg-gray-100" readonly>
                </div>
                <div class="col-span-1">
                    <button type="button" class="remove-item bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg hidden">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <button type="button" id="addItem" class="mb-6 text-blue-500 hover:text-blue-700">
            <i class="fas fa-plus mr-2"></i>Add Another Item
        </button>

        <div class="border-t pt-4 mb-6">
            <div class="flex justify-end">
                <div class="text-right">
                    <p class="text-gray-600">Total Amount</p>
                    <p class="text-2xl font-bold text-gray-800" id="totalAmount">Rp 0</p>
                </div>
            </div>
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i>Create Order
            </button>
            <a href="{{ route('orders.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let itemIndex = 1;
    const products = @json($products);

    function calculateSubtotal(item) {
        const select = item.querySelector('.product-select');
        const quantity = item.querySelector('.quantity-input');
        const subtotal = item.querySelector('.subtotal');
        
        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption ? parseFloat(selectedOption.dataset.price) || 0 : 0;
        const qty = parseInt(quantity.value) || 0;
        const total = price * qty;
        
        subtotal.value = 'Rp ' + total.toLocaleString('id-ID');
        return total;
    }

    function updateTotal() {
        const items = document.querySelectorAll('.order-item');
        let total = 0;
        items.forEach(item => {
            total += calculateSubtotal(item);
        });
        document.getElementById('totalAmount').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    function attachEvents(item) {
        item.querySelector('.product-select').addEventListener('change', updateTotal);
        item.querySelector('.quantity-input').addEventListener('input', updateTotal);
        item.querySelector('.remove-item').addEventListener('click', function() {
            item.remove();
            updateTotal();
            updateRemoveButtons();
        });
    }

    function updateRemoveButtons() {
        const items = document.querySelectorAll('.order-item');
        items.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-item');
            removeBtn.classList.toggle('hidden', items.length === 1);
        });
    }

    document.getElementById('addItem').addEventListener('click', function() {
        const container = document.getElementById('orderItems');
        const template = container.querySelector('.order-item').cloneNode(true);
        
        template.querySelector('.product-select').name = `items[${itemIndex}][product_id]`;
        template.querySelector('.product-select').value = '';
        template.querySelector('.quantity-input').name = `items[${itemIndex}][quantity]`;
        template.querySelector('.quantity-input').value = 1;
        template.querySelector('.subtotal').value = '';
        
        container.appendChild(template);
        attachEvents(template);
        updateRemoveButtons();
        itemIndex++;
    });

    // Initialize
    document.querySelectorAll('.order-item').forEach(item => {
        attachEvents(item);
    });
    updateRemoveButtons();
</script>
@endpush