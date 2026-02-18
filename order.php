<?php
$pageTitle = 'Place Order';
require 'includes/config.php';
require 'includes/header.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$selected_book_id = $_GET['book_id'] ?? '';
$books = $conn->query("SELECT id, title, stock FROM books");
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Order a Book</h4>
                </div>
                <div class="card-body">
                    <form action="process_order.php" method="POST" id="orderForm">
                        <div class="mb-3">
                            <label for="book" class="form-label">Select Book</label>
                            <select class="form-select" id="book" name="book_id" required>
                                <option value="">Choose...</option>
                                <?php while ($row = $books->fetch_assoc()): ?>
                                    <option value="<?= $row['id'] ?>" 
                                        data-stock="<?= $row['stock'] ?>"
                                        <?= ($row['id'] == $selected_book_id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($row['title']) ?> (Stock: <?= $row['stock'] ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                            <div id="stockWarning" class="form-text text-danger" style="display: none;"></div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const bookSelect = document.getElementById('book');
const quantityInput = document.getElementById('quantity');
const stockWarning = document.getElementById('stockWarning');

function checkStock() {
    const selectedOption = bookSelect.options[bookSelect.selectedIndex];
    if (selectedOption && selectedOption.value) {
        const stock = parseInt(selectedOption.dataset.stock);
        const qty = parseInt(quantityInput.value);
        if (qty > stock) {
            stockWarning.style.display = 'block';
            stockWarning.textContent = `Only ${stock} copies available.`;
            return false;
        } else {
            stockWarning.style.display = 'none';
        }
    }
    return true;
}

bookSelect.addEventListener('change', checkStock);
quantityInput.addEventListener('input', checkStock);

document.getElementById('orderForm').addEventListener('submit', function(e) {
    if (!checkStock()) {
        e.preventDefault();
        alert('Please adjust quantity to available stock.');
    }
});
</script>

<?php require 'includes/footer.php'; ?>