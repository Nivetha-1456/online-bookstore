<?php
$pageTitle = 'Book Catalog';
require 'includes/config.php';
require 'includes/header.php';

// Handle search and filter
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$query = "SELECT books.*, categories.name AS category_name 
          FROM books 
          LEFT JOIN categories ON books.category_id = categories.id 
          WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (books.title LIKE ? OR books.author LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}
if (!empty($category)) {
    $query .= " AND books.category_id = ?";
    $params[] = $category;
    $types .= "i";
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$books = $stmt->get_result();

// Get all categories for filter dropdown
$categories = $conn->query("SELECT * FROM categories");
?>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4">Welcome to Book Haven</h1>
        <p class="lead">Discover your next favourite book</p>
    </div>
</div>

<div class="container">
    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search by title or author" value="<?= htmlspecialchars($search) ?>">
                <select name="category" class="form-select w-auto">
                    <option value="">All Categories</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>

    <!-- Books Grid -->
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        <?php if ($books->num_rows === 0): ?>
            <div class="col-12 text-center">
                <p class="text-muted">No books found.</p>
            </div>
        <?php endif; ?>
        <?php while ($book = $books->fetch_assoc()): ?>
            <div class="col">
                <div class="card h-100">
                    <img src="<?= htmlspecialchars($book['image'] ?? 'https://via.placeholder.com/300x200?text=No+Cover') ?>" 
                         class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">by <?= htmlspecialchars($book['author']) ?></h6>
                        <p class="card-text small"><?= htmlspecialchars(substr($book['description'] ?? '', 0, 100)) ?>...</p>
                        <div class="mt-auto">
                            <p class="fw-bold text-primary mb-2">₹<?= number_format($book['price'], 2) ?></p>
                            <?php if (isLoggedIn()): ?>
                                <a href="order.php?book_id=<?= $book['id'] ?>" class="btn btn-primary w-100">Order Now</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-outline-primary w-100">Login to Order</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require 'includes/footer.php'; ?>