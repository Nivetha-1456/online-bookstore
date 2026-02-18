<?php
require '../includes/config.php';
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}
$pageTitle = 'Admin Dashboard';
require '../includes/header.php';
?>

<div class="container">
    <h2>Admin Dashboard</h2>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Books</h5>
                    <?php
                    $result = $conn->query("SELECT COUNT(*) AS count FROM books");
                    $count = $result->fetch_assoc()['count'];
                    ?>
                    <p class="card-text display-4"><?= $count ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <?php
                    $result = $conn->query("SELECT COUNT(*) AS count FROM orders");
                    $count = $result->fetch_assoc()['count'];
                    ?>
                    <p class="card-text display-4"><?= $count ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <?php
                    $result = $conn->query("SELECT COUNT(*) AS count FROM users");
                    $count = $result->fetch_assoc()['count'];
                    ?>
                    <p class="card-text display-4"><?= $count ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <a href="books.php" class="btn btn-primary">Manage Books</a>
        <a href="orders.php" class="btn btn-success">View Orders</a>
    </div>
</div>

<?php require '../includes/footer.php'; ?>