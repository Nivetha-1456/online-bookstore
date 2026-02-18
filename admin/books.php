<?php
require '../includes/config.php';
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $image = trim($_POST['image']);
        $category_id = intval($_POST['category_id']);

        $stmt = $conn->prepare("INSERT INTO books (title, author, description, price, stock, image, category_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdisi", $title, $author, $description, $price, $stock, $image, $category_id);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $id = intval($_POST['id']);
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $image = trim($_POST['image']);
        $category_id = intval($_POST['category_id']);

        $stmt = $conn->prepare("UPDATE books SET title=?, author=?, description=?, price=?, stock=?, image=?, category_id=? WHERE id=?");
        $stmt->bind_param("sssdisii", $title, $author, $description, $price, $stock, $image, $category_id, $id);
        $stmt->execute();
    }
} elseif (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM books WHERE id=$id");
    header("Location: books.php");
    exit;
}

$books = $conn->query("SELECT books.*, categories.name AS category_name FROM books LEFT JOIN categories ON books.category_id = categories.id");
$categories = $conn->query("SELECT * FROM categories");
$pageTitle = 'Manage Books';
require '../includes/header.php';
?>

<div class="container">
    <h2>Manage Books</h2>

    <!-- Add Book Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Add New Book</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="title" class="form-control" placeholder="Title" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="author" class="form-control" placeholder="Author" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="Price" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="stock" class="form-control" placeholder="Stock" required>
                    </div>
                    <div class="col-md-2">
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php 
                            $categories->data_seek(0);
                            while ($cat = $categories->fetch_assoc()): ?>
                                <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <input type="text" name="image" class="form-control" placeholder="Image URL">
                    </div>
                    <div class="col-md-12">
                        <textarea name="description" class="form-control" placeholder="Description" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add" class="btn btn-success">Add Book</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Books List -->
    <div class="card">
        <div class="card-header bg-secondary text-white">Current Books</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Category</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($book = $books->fetch_assoc()): ?>
                        <tr>
                            <form method="POST">
                                <td><?= $book['id'] ?><input type="hidden" name="id" value="<?= $book['id'] ?>"></td>
                                <td><input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" class="form-control"></td>
                                <td><input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" class="form-control"></td>
                                <td><input type="number" step="0.01" name="price" value="<?= $book['price'] ?>" class="form-control"></td>
                                <td><input type="number" name="stock" value="<?= $book['stock'] ?>" class="form-control"></td>
                                <td>
                                    <select name="category_id" class="form-select">
                                        <?php 
                                        $categories->data_seek(0);
                                        while ($cat = $categories->fetch_assoc()): ?>
                                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $book['category_id'] ? 'selected' : '' ?>>
                                                <?= $cat['name'] ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>
                                <td><input type="text" name="image" value="<?= htmlspecialchars($book['image']) ?>" class="form-control"></td>
                                <td>
                                    <button type="submit" name="update" class="btn btn-sm btn-warning">Update</button>
                                    <a href="?delete=<?= $book['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this book?')">Delete</a>
                                </td>
                            </form>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>