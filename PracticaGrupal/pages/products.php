<?php

// Get the filter parameters from the form submission
$categoryFilter = isset($_POST['category']) ? $_POST['category'] : '';
$brandFilter = isset($_POST['brand']) ? $_POST['brand'] : '';
$colorFilter = isset($_POST['color']) ? $_POST['color'] : '';
$typeFilter = isset($_POST['type']) ? $_POST['type'] : '';
$styleFilter = isset($_POST['style']) ? $_POST['style'] : '';
$minPrice = isset($_POST['min_price']) ? $_POST['min_price'] : '';
$maxPrice = isset($_POST['max_price']) ? $_POST['max_price'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : ''; // Get search term
$sortOption = isset($_POST['sort']) ? $_POST['sort'] : '';

// Number of products per page
$itemsPerPage = 12;

// Get the current page from the URL (default is 1)
$currentPageNum = isset($_POST['page_num']) ? (int)$_POST['page_num'] : 1;
$currentPageNum = max($currentPageNum, 1); // Ensure the page is at least 1

// Calculate the OFFSET for the SQL query
$offset = ($currentPageNum - 1) * $itemsPerPage;

// Initialize filter conditions for products
$filterParams = [];  // For bind_param usage

// Start the query for filtering products
$query = "SELECT * FROM products WHERE 1";

// Add search term condition if provided
if ($searchTerm) {
    $query .= " AND name LIKE ?";
    $filterParams[] = '%' . $searchTerm . '%';  // Add wildcards for partial match
}

// Add category filter if selected
if ($categoryFilter) {
    $query .= " AND category = ?";
    $filterParams[] = $categoryFilter;
}

// Add brand filter if selected
if ($brandFilter) {
    $query .= " AND brand = ?";
    $filterParams[] = $brandFilter;
}

// Add color filter if selected
if ($colorFilter) {
    $query .= " AND color = ?";
    $filterParams[] = $colorFilter;
}

// Add type filter if selected
if ($typeFilter) {
    $query .= " AND type = ?";
    $filterParams[] = $typeFilter;
}

// Add style filter if selected
if ($styleFilter) {
    $query .= " AND style = ?";
    $filterParams[] = $styleFilter;
}

// Add price range filters if provided
if ($minPrice) {
    $query .= " AND price >= ?";
    $filterParams[] = $minPrice;
}

if ($maxPrice) {
    $query .= " AND price <= ?";
    $filterParams[] = $maxPrice;
}

// Add sorting logic based on the selected sort option
if ($sortOption) {
    switch ($sortOption) {
        case 'price_asc':
            $query .= " ORDER BY price ASC";
            break;
        case 'price_desc':
            $query .= " ORDER BY price DESC";
            break;
        case 'name_asc':
            $query .= " ORDER BY name ASC";
            break;
        case 'name_desc':
            $query .= " ORDER BY name DESC";
            break;
    }
}

// Add LIMIT and OFFSET for pagination
$query .= " LIMIT ? OFFSET ?";
$filterParams[] = $itemsPerPage;
$filterParams[] = $offset;

// Prepare the SQL statement
$stmt = $mysqliC->prepare($query);

// Dynamically build the bind type string based on the filters
$bindTypes = '';
foreach ($filterParams as $param) {
    if (is_string($param)) {
        $bindTypes .= 's';  // String
    } elseif (is_numeric($param)) {
        $bindTypes .= 'd';  // Decimal/Number
    }
}

if ($bindTypes) {
    $stmt->bind_param($bindTypes, ...$filterParams);
}

$stmt->execute();
$result = $stmt->get_result();

// Fetch products
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Initialize the query for counting filtered products
$countQuery = "SELECT COUNT(*) as total FROM products WHERE 1";

// Add the same filter conditions that are applied to the main query
if ($searchTerm) {
    $countQuery .= " AND name LIKE ?";
}

if ($categoryFilter) {
    $countQuery .= " AND category = ?";
}

if ($brandFilter) {
    $countQuery .= " AND brand = ?";
}

if ($colorFilter) {
    $countQuery .= " AND color = ?";
}

if ($typeFilter) {
    $countQuery .= " AND type = ?";
}

if ($styleFilter) {
    $countQuery .= " AND style = ?";
}

if ($minPrice) {
    $countQuery .= " AND price >= ?";
}

if ($maxPrice) {
    $countQuery .= " AND price <= ?";
}

// Prepare the count query
$countStmt = $mysqliC->prepare($countQuery);

// Bind parameters dynamically
$countFilterParams = [];
$countBindTypes = '';

if ($searchTerm) {
    $countFilterParams[] = '%' . $searchTerm . '%';
    $countBindTypes .= 's';
}
if ($categoryFilter) {
    $countFilterParams[] = $categoryFilter;
    $countBindTypes .= 's';
}
if ($brandFilter) {
    $countFilterParams[] = $brandFilter;
    $countBindTypes .= 's';
}
if ($colorFilter) {
    $countFilterParams[] = $colorFilter;
    $countBindTypes .= 's';
}
if ($typeFilter) {
    $countFilterParams[] = $typeFilter;
    $countBindTypes .= 's';
}
if ($styleFilter) {
    $countFilterParams[] = $styleFilter;
    $countBindTypes .= 's';
}
if ($minPrice) {
    $countFilterParams[] = $minPrice;
    $countBindTypes .= 'd';
}
if ($maxPrice) {
    $countFilterParams[] = $maxPrice;
    $countBindTypes .= 'd';
}

// Bind the parameters
if ($countBindTypes) {
    $countStmt->bind_param($countBindTypes, ...$countFilterParams);
}

// Execute the count query
$countStmt->execute();

// Fetch the result
$countResult = $countStmt->get_result();
$countRow = $countResult->fetch_assoc();

// Get the total number of filtered products
$totalFilteredProducts = $countRow['total'];

// Calculate the total number of pages
$totalPages = ceil($totalFilteredProducts / $itemsPerPage);

// Fetch filter options (same as before)
$filteredIdsCondition = '';
$filterConditions = [];

if ($categoryFilter) {
    $filterConditions[] = "category = '$categoryFilter'";
}
if ($brandFilter) {
    $filterConditions[] = "brand = '$brandFilter'";
}
if ($colorFilter) {
    $filterConditions[] = "color = '$colorFilter'";
}
if ($typeFilter) {
    $filterConditions[] = "type = '$typeFilter'";
}
if ($styleFilter) {
    $filterConditions[] = "style = '$styleFilter'";
}

if (!empty($filterConditions)) {
    $filteredIdsCondition = 'WHERE ' . implode(' AND ', $filterConditions);
}

// Fetch filter options based on the filtered products
$brandsQuery = "SELECT DISTINCT brand FROM products $filteredIdsCondition";
$colorsQuery = "SELECT DISTINCT color FROM products $filteredIdsCondition";
$typesQuery = "SELECT DISTINCT type FROM products $filteredIdsCondition";
$stylesQuery = "SELECT DISTINCT style FROM products $filteredIdsCondition";

// Execute the filter queries
$brandsResult = $mysqliC->query($brandsQuery);
$colorsResult = $mysqliC->query($colorsQuery);
$typesResult = $mysqliC->query($typesQuery);
$stylesResult = $mysqliC->query($stylesQuery);

// Now, for the category filter, always fetch all categories, regardless of the current filter.
$categoriesQuery = "SELECT DISTINCT category FROM products";
$categoriesResult = $mysqliC->query($categoriesQuery);


?>

<section class="products">
    <div class="products-container">
        <!-- Filter Sidebar -->
        <div class="filter-sidebar">
            <h3>Filter By</h3>
            <form method="POST" action="index.php?page=products">
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

                <button type="submit">Apply Filters</button>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="product-list">
            <h1>Our Products</h1>
            <div class="product-items">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-item">
                            <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                            <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
                            <a href="index.php?page=product&id=<?php echo $product['id']; ?>" class="view-details">View Details</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products found for the selected filters.</p>
                <?php endif; ?>
            </div>

            <!-- Pagination Form -->
            <div class="pagination">
                <?php if ($currentPageNum > 1): ?>
                    <form method="POST" action="index.php?page=products" style="display: inline;">
                        <?php foreach ($_POST as $key => $value): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                        <?php endforeach; ?>
                        <input type="hidden" name="page_num" value="<?php echo $currentPageNum - 1; ?>">
                        <button type="submit">&laquo; Previous</button>
                    </form>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <form method="POST" action="index.php?page=products" style="display: inline;">
                        <?php foreach ($_POST as $key => $value): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                        <?php endforeach; ?>
                        <input type="hidden" name="page_num" value="<?php echo $i; ?>">
                        <button type="submit"><?php echo $i; ?></button>
                    </form>
                <?php endfor; ?>

                <?php if ($currentPageNum < $totalPages): ?>
                    <form method="POST" action="index.php?page=products" style="display: inline;">
                        <?php foreach ($_POST as $key => $value): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                        <?php endforeach; ?>
                        <input type="hidden" name="page_num" value="<?php echo $currentPageNum + 1; ?>">
                        <button type="submit">Next &raquo;</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
