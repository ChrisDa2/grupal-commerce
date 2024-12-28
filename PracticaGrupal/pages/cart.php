<?php

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the active cart for the logged-in user
$query = "SELECT id FROM cart WHERE user_id = ? AND is_active = TRUE";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();

if ($cart_result->num_rows == 0) {
    echo "Your cart is empty.";
    exit();
}

$cart = $cart_result->fetch_assoc();
$cart_id = $cart['id'];

// Fetch the items in the cart
$query = "SELECT ci.*, p.name, p.price FROM cart_item ci
          JOIN products p ON ci.product_id = p.id
          WHERE ci.cart_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $cart_id);
$stmt->execute();
$cart_items = $stmt->get_result();

$total = 0;

?>

<h1>Your cart</h1>
<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($item = $cart_items->fetch_assoc()): 
            $item_total = $item['price'] * $item['quantity'];
            $total += $item_total;
        ?>
        <tr>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td><?php echo number_format($item['price'], 2); ?> €</td>
            <td><?php echo number_format($item_total, 2); ?> €</td>
            <td>
                <!-- Option to remove item from cart -->
                <form method="get" action="index.php">
                    <input type="hidden" name="page" value="cart_remove">
                    <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                    <button type="submit">Remove</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<p><strong>Total: <?php echo number_format($total, 2); ?> €</strong></p>

<!-- Proceed to Checkout -->
<a href="index.php?page=checkout">Proceed to Checkout</a>