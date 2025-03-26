<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>SKU</th>
            <th>HSN CODE</th>
            <th>Buying Price</th>
            <th>Selling Price</th>
            <th>Discount</th>
            <th>Margin</th>
            <th>Subtotal</th>
            <th>Tax</th>
            <th>Final Price</th>
            <th>Order Serial No</th>
            <th>Order Datetime</th>
            <th>Order Type</th>
            <th>City</th>
            <th>State</th>
            <th>Postal Code</th>
            <th>CGST Amount</th>
            <th>SGST Amount</th>
            <th>IGST Amount</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orderProducts as $product)
        
            //dd($product);
            <tr>
                <td>{{ $product['id'] }}</td>
                <td>{{ $product['product_id'] }}</td>
                <td>{{ $product['product_name'] }}</td>
                <td>{{ $product['sku'] }}</td>
                <td>{{ $product['hsnCode'] }}</td>
                <td>{{ $product['buying_price'] }}</td>
                <td>{{ $product['selling_price'] }}</td>
                <td>{{ $product['discount'] }}</td>
                <td>{{ $product['margin'] }}</td>
                <td>{{ $product['subtotal'] }}</td>
                <td>{{ $product['tax'] }}</td>
                <td>{{ $product['final_price'] }}</td>
                <td>{{ $product['order_serial_no'] }}</td>
                <td>{{ $product['order_datetime'] }}</td>
                <td>{{ $product['order_type'] }}</td>
                <td>{{ $product['order_address']['city'] ?? '' }}</td>
                <td>{{ $product['order_address']['state'] ?? '' }}</td>
                <td>{{ $product['order_address']['postal_code'] ?? '' }}</td>
                <td>{{ $product['cgst_amount'] }}</td>
                <td>{{ $product['sgst_amount'] }}</td>
                <td>{{ $product['igst_amount'] }}</td>
                <td>{{ $product['total_amount'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
