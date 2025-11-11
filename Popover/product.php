<?php
// Simulate product data from database
$products = [
    ['id' => 1, 'name' => 'Laptop Pro', 'category' => 'Electronics', 'price' => 1299.99, 'stock' => 15, 'status' => 'active'],
    ['id' => 2, 'name' => 'Wireless Mouse', 'category' => 'Accessories', 'price' => 29.99, 'stock' => 42, 'status' => 'active'],
    ['id' => 3, 'name' => 'Mechanical Keyboard', 'category' => 'Accessories', 'price' => 89.99, 'stock' => 23, 'status' => 'active'],
    ['id' => 4, 'name' => 'Gaming Monitor', 'category' => 'Electronics', 'price' => 349.99, 'stock' => 8, 'status' => 'active'],
    ['id' => 5, 'name' => 'USB-C Cable', 'category' => 'Accessories', 'price' => 19.99, 'stock' => 0, 'status' => 'out-of-stock']
];
?>

<div class="api-info">
    <h4 class="font-semibold text-teal-900 mb-2">Products Catalog</h4>
    <p class="text-teal-800 text-sm">
        Live product data loaded from PHP backend. Last updated:
        <?php echo date('Y-m-d H:i:s'); ?>
    </p>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <?php
                    $statusClass = $product['status'] === 'active' ? 'status-active' : 'status-pending';
                    $statusText = $product['status'] === 'active' ? 'In Stock' : 'Out of Stock';
                ?>
                <tr>
                    <td class="font-mono">#<?php echo $product['id']; ?></td>
                    <td class="font-semibold"><?php echo $product['name']; ?></td>
                    <td><?php echo $product['category']; ?></td>
                    <td class="font-semibold">$<?php echo number_format($product['price'], 2); ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="mt-4 text-sm text-gray-600">
    <i class="fas fa-info-circle mr-1"></i>
    Showing <?php echo count($products); ?> products from database
</div>
