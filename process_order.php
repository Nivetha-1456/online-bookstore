<?php
require 'includes/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = intval($_POST['book_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);

if ($book_id <= 0 || $quantity <= 0) {
    die("Invalid request.");
}

// Use transaction
$conn->begin_transaction();

try {
    // Lock the book row
    $stmt = $conn->prepare("SELECT price, stock FROM books WHERE id = ? FOR UPDATE");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();

    if (!$book) {
        throw new Exception("Book not found.");
    }

    if ($quantity > $book['stock']) {
        throw new Exception("Insufficient stock.");
    }

    $total = $book['price'] * $quantity;

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, book_id, quantity, total_price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $user_id, $book_id, $quantity, $total);
    $stmt->execute();

    // Update stock
    $new_stock = $book['stock'] - $quantity;
    $stmt = $conn->prepare("UPDATE books SET stock = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_stock, $book_id);
    $stmt->execute();

    $conn->commit();

    // Success page
    $pageTitle = 'Order Success';
    require 'includes/header.php';
    echo '<div class="container mt-5"><div class="alert alert-success text-center"><h4>Order placed successfully!</h4><p>Total: ₹' . number_format($total, 2) . '</p><a href="index.php" class="btn btn-primary">Continue Shopping</a></div></div>';
    require 'includes/footer.php';

} catch (Exception $e) {
    $conn->rollback();
    $pageTitle = 'Order Error';
    require 'includes/header.php';
    echo '<div class="container mt-5"><div class="alert alert-danger text-center"><h4>Error</h4><p>' . $e->getMessage() . '</p><a href="order.php" class="btn btn-warning">Try Again</a></div></div>';
    require 'includes/footer.php';
}
?>