<?php
// Assuming you have already established the database connection
// and fetched required data such as categories, brands, colors, types, styles, etc.

// Fetch categories, brands, colors, types, and styles for the filter options
$categoriesResult = $mysqliC->query("SELECT DISTINCT category FROM products");
$brandsResult = $mysqliC->query("SELECT DISTINCT brand FROM products");
$colorsResult = $mysqliC->query("SELECT DISTINCT color FROM products");
$typesResult = $mysqliC->query("SELECT DISTINCT type FROM products");
$stylesResult = $mysqliC->query("SELECT DISTINCT style FROM products");

// These values will be set based on any existing filter
$categoryFilter = $_POST['category'] ?? '';
$brandFilter = $_POST['brand'] ?? '';
$colorFilter = $_POST['color'] ?? '';
$typeFilter = $_POST['type'] ?? '';
$styleFilter = $_POST['style'] ?? '';
$minPrice = $_POST['min_price'] ?? '';
$maxPrice = $_POST['max_price'] ?? '';
$sortOption = $_POST['sort'] ?? '';
$currentPageNum = $_POST['page_num'] ?? 1;
$itemsPerPage = 12;
$offset = ($currentPageNum - 1) * $itemsPerPage;

include 'filterProductsChristian.php';
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function fetchProducts() {
    const productsContainer = $('.product-items');
    productsContainer.html('<p class="loading">Loading...</p>');

    const data = {
        category: $('#category').val(),
        brand: $('#brand').val(),
        color: $('#color').val(),
        type: $('#type').val(),
        style: $('#style').val(),
        min_price: $('input[name="min_price"]').val(),
        max_price: $('input[name="max_price"]').val(),
        search: $('#search').val(),
        sort: $('#sort').val(),
        page_num: $('input[name="page_num"]').val() || 1
    };

    console.log("Sending POST request with data:", data); // Log the data being sent

    $.post('filterProductsChristian.php', data, function(response) {
        console.log("Received response:", response); // Log the response

        if (response.products && response.total_pages) {
            productsContainer.empty();  // Clear existing content

            // Display products
            if (response.products.length > 0) {
                response.products.forEach(function(product) {
                    productsContainer.append(`
                        <div class="product-item">
                            <img src="${product.image_path}" alt="${product.name}" class="product-image">
                            <h2>${product.name}</h2>
                            <p>Price: $${product.price.toFixed(2)}</p>
                            <a href="index.php?page=product&id=${product.id}" class="view-details">View Details</a>
                        </div>
                    `);
                });
            } else {
                productsContainer.html('<p>No products found for the selected filters.</p>');
            }

            // Ensure that the products container is visible after appending content
            productsContainer.show();

            // Pagination
            const pagination = $('.pagination');
            pagination.empty();  // Clear previous pagination
            for (let i = 1; i <= response.total_pages; i++) {
                pagination.append(`<button onclick="changePage(${i})">${i}</button>`);
            }
        } else {
            productsContainer.html('<p>Error loading products. Please try again.</p>');
        }
    }).fail(function() {
        productsContainer.html('<p>Error loading products. Please check your connection.</p>');
    });
}
</script>

<section class="products">
    <div class="products-container">
        <!-- Filter Sidebar -->
        <div class="filter-sidebar">
            <h3>Filter By</h3>
            <form method="POST" action="index.php?page=products">

                <!-- Search Filter -->
                <div class="filter-search">
                    <label for="search">Search:</label>
                    <input type="text" id="search" placeholder="Search products">
                </div><br>

                <!-- Category Filter -->
                <div class="filter-category">
                    <label for="category">Category:</label>
                    <select name="category" id="category">
                        <option value="">All Categories</option>
                        <?php while ($categoryRow = $categoriesResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($categoryRow['category']); ?>" <?php echo ($categoryFilter == $categoryRow['category']) ? 'selected' : ''; ?>>
                                <?php echo strtoupper(htmlspecialchars($categoryRow['category'])); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div><br>

                <!-- Brand Filter -->
                <div class="filter-brand">
                    <label for="brand">Brand:</label>
                    <select name="brand" id="brand">
                        <option value="">All Brands</option>
                        <?php while ($brandRow = $brandsResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($brandRow['brand']); ?>" <?php echo ($brandFilter == $brandRow['brand']) ? 'selected' : ''; ?>>
                                <?php echo strtoupper(htmlspecialchars($brandRow['brand'])); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div><br>

                <!-- Color Filter -->
                <div class="filter-color">
                    <label for="color">Color:</label>
                    <select name="color" id="color">
                        <option value="">All Colors</option>
                        <?php while ($colorRow = $colorsResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($colorRow['color']); ?>" <?php echo ($colorFilter == $colorRow['color']) ? 'selected' : ''; ?>>
                                <?php echo strtoupper(htmlspecialchars($colorRow['color'])); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div><br>

                <!-- Type Filter -->
                <div class="filter-type">
                    <label for="type">Type:</label>
                    <select name="type" id="type">
                        <option value="">All Types</option>
                        <?php while ($typeRow = $typesResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($typeRow['type']); ?>" <?php echo ($typeFilter == $typeRow['type']) ? 'selected' : ''; ?>>
                                <?php echo strtoupper(htmlspecialchars($typeRow['type'])); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div><br>

                <!-- Style Filter -->
                <div class="filter-style">
                    <label for="style">Style:</label>
                    <select name="style" id="style">
                        <option value="">All Styles</option>
                        <?php while ($styleRow = $stylesResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($styleRow['style']); ?>" <?php echo ($styleFilter == $styleRow['style']) ? 'selected' : ''; ?>>
                                <?php echo strtoupper(htmlspecialchars($styleRow['style'])); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div><br>

                <!-- Price Filter -->
                <div class="filter-price">
                    <label for="min_price">Price Range:</label>
                    <input type="number" name="min_price" placeholder="Min Price" value="<?php echo htmlspecialchars($minPrice); ?>">
                    <input type="number" name="max_price" placeholder="Max Price" value="<?php echo htmlspecialchars($maxPrice); ?>">
                </div><br>

                <!-- Sorting Filter -->
                <div class="filter-sort">
                    <label for="sort">Sort By:</label>
                    <select name="sort" id="sort">
                        <option value="">Select Sorting</option>
                        <option value="price_asc" <?php echo ($sortOption == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo ($sortOption == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="name_asc" <?php echo ($sortOption == 'name_asc') ? 'selected' : ''; ?>>Name: A to Z</option>
                        <option value="name_desc" <?php echo ($sortOption == 'name_desc') ? 'selected' : ''; ?>>Name: Z to A</option>
                    </select>
                </div><br>

                <!-- Submit Button -->
                <button type="submit" id="submit-filters">Apply Filters</button>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="product-list">
            <h1>Our Products</h1>
            <div class="product-items">
                <!-- Initially, products will be loaded dynamically via AJAX -->
                <script>
                    fetchProducts();
                </script>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <!-- Pagination buttons will be generated dynamically in the fetchProducts() callback -->
            </div>
        </div>
    </div>
</section>
