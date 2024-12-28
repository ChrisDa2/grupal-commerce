<?php

// Check if the admin is logged in (use session or database to verify)
if (!isset($_SESSION['user_id']) || $_SESSION['email'] !== 'admin@example.es') {
    // Redirect to login page if not logged in or not an admin
    header('Location: login.php');
    exit();
}

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    // Collect form data
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $color = $_POST['color'];
    $type = $_POST['type'];
    $style = $_POST['style'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $image_path = 'uploads/' . basename($image['name']);
        move_uploaded_file($image['tmp_name'], $image_path);
    } else {
        $image_path = ''; // Default to empty if no image
    }

    // Insert product into database
    $sql = "INSERT INTO products (name, brand, price, color, type, style, description, category, stock, image_path) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssdsssssis', $name, $brand, $price, $color, $type, $style, $description, $category, $stock, $image_path);
    $stmt->execute();
    $stmt->close();
    $message = "Product added successfully!";
}

// Handle Edit Product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $color = $_POST['color'];
    $type = $_POST['type'];
    $style = $_POST['style'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $image_path = 'uploads/' . basename($image['name']);
        move_uploaded_file($image['tmp_name'], $image_path);
    } else {
        $image_path = $_POST['current_image']; // Retain existing image if no new image is uploaded
    }

    // Update product in the database
    $sql = "UPDATE products SET name=?, brand=?, price=?, color=?, type=?, style=?, description=?, category=?, stock=?, image_path=? WHERE id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssdsdsssssi', $name, $brand, $price, $color, $type, $style, $description, $category, $stock, $image_path, $id);
    $stmt->execute();
    $stmt->close();
    $message = "Product updated successfully!";
}

// Handle Delete Product
if (isset($_GET['delete_product_id'])) {
    $product_id = $_GET['delete_product_id'];

    // Delete product from the database
    $sql = "DELETE FROM products WHERE id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $stmt->close();
    $message = "Product deleted successfully!";
}

// Handle Delete User
if (isset($_GET['delete_user_id'])) {
    $user_id = $_GET['delete_user_id'];

    // Delete user from the database
    $sql = "DELETE FROM users WHERE id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();
    $message = "User deleted successfully!";
}

// Handle Mark Order as Shipped or Canceled
if (isset($_GET['order_action']) && isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $action = $_GET['order_action'];

    // Update order status in the database
    if ($action == 'shipped' || $action == 'cancelled') {
        $sql = "UPDATE orders SET status=? WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('si', $action, $order_id);
        $stmt->execute();
        $stmt->close();
        $message = "Order status updated to $action.";
    }
}

// Fetch all products
$sql = "SELECT * FROM products";
$products_result = $mysqli->query($sql);

// Fetch all users
$sql = "SELECT * FROM users";
$users_result = $mysqli->query($sql);

// Fetch all orders
$sql = "SELECT * FROM orders";
$orders_result = $mysqli->query($sql);
?>


    <h1>Admin Panel</h1>
    
    <?php if (isset($message)) { echo "<p>$message</p>"; } ?>

    <!-- Add Product Form -->
    <h2>Add New Product</h2>
    <form action="index.php?page=admin" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="add_product">
        <label for="name">Product Name:</label>
        <input type="text" name="name" required><br>

        <label for="brand">Brand:</label>
        <input type="text" name="brand" required><br>

        <label for="price">Price:</label>
        <input type="number" name="price" step="0.01" required><br>

        <label for="color">Color:</label>
        <input type="text" name="color"><br>

        <label for="type">Type:</label>
        <input type="text" name="type"><br>

        <label for="style">Style:</label>
        <input type="text" name="style"><br>

        <label for="description">Description:</label>
        <textarea name="description"></textarea><br>

        <label for="category">Category:</label>
        <input type="text" name="category" required><br>

        <label for="stock">Stock:</label>
        <input type="number" name="stock" required><br>

        <label for="image">Product Image:</label>
        <input type="file" name="image"><br>

        <button type="submit">Add Product</button>
    </form>

    <!-- Display Products -->
    <h2>Manage Products</h2>
    <?php while ($row = $products_result->fetch_assoc()) { ?>
        <div>
            <h3><?php echo $row['name']; ?> (ID: <?php echo $row['id']; ?>)</h3>
            <p>Brand: <?php echo $row['brand']; ?></p>
            <p>Price: <?php echo $row['price']; ?> €</p>
            <p>Stock: <?php echo $row['stock']; ?></p>
            <p>Category: <?php echo $row['category']; ?></p>
            <p><img src="<?php echo $row['image_path']; ?>" width="100" alt="Product Image"></p>

            <!-- Edit Product -->
            <a href="index.php?page=edit_product&edit_id=<?php echo $row['id']; ?>">Edit</a> | 

            <!-- Delete Product -->
            <a href="index.php?page=admin&delete_product_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
        </div>
    <?php } ?>

    <!-- Manage Users -->
    <h2>Manage Users</h2>
    <?php while ($row = $users_result->fetch_assoc()) { ?>
        <div>
            <p>User: <?php echo $row['email']; ?> (ID: <?php echo $row['id']; ?>)</p>
            <a href="index.php?page=admin&delete_user_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete User</a>
        </div>
    <?php } ?>

    <!-- Manage Orders -->
    <h2>Manage Orders</h2>
    <?php while ($row = $orders_result->fetch_assoc()) { ?>
        <div>
            <p>Order ID: <?php echo $row['id']; ?> - Status: <?php echo $row['status']; ?></p>
            <a href="index.php?page=admin&order_action=shipped&order_id=<?php echo $row['id']; ?>">Mark as Shipped</a> |
            <a href="index.php?page=admin&order_action=cancelled&order_id=<?php echo $row['id']; ?>">Cancel Order</a> |
            <a href="index.php?page=admin&view_order_details=<?php echo $row['id']; ?>">View Details</a>
        </div>
    <?php } ?>

    <?php
    // Handle view order details if the link is clicked
    if (isset($_GET['view_order_details'])) {
        $order_id = $_GET['view_order_details'];

        // Fetch order details
        $sql = "SELECT * FROM orders WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $order_result = $stmt->get_result();
        $order = $order_result->fetch_assoc();

        // Display order details
        echo "<h2>Order Details</h2>";
        echo "<p>Order ID: " . $order['id'] . "</p>";
        echo "<p>User ID: " . $order['user_id'] . "</p>";
        echo "<p>Status: " . $order['status'] . "</p>";
        // List items bought (assuming a separate table for order_items)
        $sql = "SELECT * FROM order_item WHERE order_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $items_result = $stmt->get_result();

        while ($item = $items_result->fetch_assoc()) {
            echo "<p>Product Name: " . $item['product_name'] . "</p>";
            echo "<p>Quantity: " . $item['quantity'] . "</p>";
            echo "<p>Price: " . $item['price'] . " €</p>";
        }
    }
    ?>