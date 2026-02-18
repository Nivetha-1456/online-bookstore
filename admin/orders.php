<?php
require '../includes/config.php';
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$orders = $conn->query("
    SELECT orders.*, users.username, books.title AS book_title 
    FROM orders 
    JOIN users ON orders.user_id = users.id 
    JOIN books ON orders.book_id = books.id 
    ORDER BY order_date DESC
");
$pageTitle = 'View Orders';
require '../includes/header.php';
?>

<div class="container">
    <h2>All Orders</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Book</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Order Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['username']) ?></td>
                    <td><?= htmlspecialchars($order['book_title']) ?></td>
                    <td><?= $order['quantity'] ?></td>
                    <td>₹<?= number_format($order['total_price'], 2) ?></td>
                    <td><?= $order['order_date'] ?></td>
                    <td><?= ucfirst($order['status']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>