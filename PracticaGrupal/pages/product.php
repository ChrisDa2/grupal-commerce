<?php

// Fetch the specific product based on the ID passed in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$product_id = intval($_GET['id']);
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
?>

<section class="product-details">
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
    <div class="product-details-container">
        <!-- Left Side: Product Image and Info -->
        <div class="left-side">
            <!-- Display product image -->
            <div class="product-image-container">
                <img src="images/<?php echo htmlspecialchars($product['name']); ?>.png" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
            </div>

            <!-- Product Info Below the Image -->
            <div class="product-info">
                <p><strong>Brand:</strong> <?php echo htmlspecialchars($product['brand']); ?></p>
                <p><strong>Price:</strong> <?php echo number_format($product['price'], 2); ?> €</p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
            </div>
        </div>

        <!-- Right Side: Stock and Cart Section -->
        <div class="right-side">
            <p><strong>Stock Available:</strong> <?php echo $product['stock']; ?></p>
            <!-- Form to add product to cart -->
            <form method="post" action="index.php?page=cart_action">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" min="1" max="<?php echo $product['stock']; ?>" required>
                <button type="submit">Add to Cart</button>
            </form>
        </div>
    </div>
</section>
