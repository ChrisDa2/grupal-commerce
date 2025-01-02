<?php
require 'database.php';
if ($mysqliC->connect_error) {
    die("Connection failed: " . $mysqliC->connect_error);
}

// Get filter and pagination parameters from the POST request
$categoryFilter = $_POST['category'] ?? '';
$brandFilter = $_POST['brand'] ?? '';
$colorFilter = $_POST['color'] ?? '';
$typeFilter = $_POST['type'] ?? '';
$styleFilter = $_POST['style'] ?? '';
$minPrice = $_POST['min_price'] ?? '';
$maxPrice = $_POST['max_price'] ?? '';
$search = $_POST['search'] ?? '';
$sortOption = $_POST['sort'] ?? '';
$pageNum = $_POST['page_num'] ?? 1;

// Pagination parameters
$itemsPerPage = 12;
$offset = ($pageNum - 1) * $itemsPerPage;

// Start building the SQL query
$query = "SELECT * FROM products WHERE 1=1";

// Apply filters
if ($categoryFilter) {
    $query .= " AND category = '" . $mysqliC->real_escape_string($categoryFilter) . "'";
}
if ($brandFilter) {
    $query .= " AND brand = '" . $mysqliC->real_escape_string($brandFilter) . "'";
}
if ($colorFilter) {
    $query .= " AND color = '" . $mysqliC->real_escape_string($colorFilter) . "'";
}
if ($typeFilter) {
    $query .= " AND type = '" . $mysqliC->real_escape_string($typeFilter) . "'";
}
if ($styleFilter) {
    $query .= " AND style = '" . $mysqliC->real_escape_string($styleFilter) . "'";
}
if ($minPrice) {
    $query .= " AND price >= " . (float)$minPrice;
}
if ($maxPrice) {
    $query .= " AND price <= " . (float)$maxPrice;
}
if ($search) {
    $query .= " AND name LIKE '%" . $mysqliC->real_escape_string($search) . "%'";
}

// Sorting
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
        default:
            $query .= " ORDER BY name ASC"; // Default sorting
            break;
    }
} else {
    $query .= " ORDER BY name ASC"; // Default sorting if no sorting is selected
}

// Limit the results for pagination
$query .= " LIMIT $offset, $itemsPerPage";

// Log the query to check if it's correct
error_log("SQL Query: " . $query);

// Execute the query to fetch products
$result = $mysqliC->query($query);

// Check if the query is successful
if (!$result) {
    error_log("Query error: " . $mysqliC->error);  // Log any SQL errors
}

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'price' => $row['price'],
        'image_path' => $row['image_path'],
    ];
}

// Calculate the total number of products that match the filters
$totalQuery = "SELECT COUNT(*) as total FROM products WHERE 1=1";
if ($categoryFilter) { $totalQuery .= " AND category = '" . $mysqliC->real_escape_string($categoryFilter) . "'"; }
if ($brandFilter) { $totalQuery .= " AND brand = '" . $mysqliC->real_escape_string($brandFilter) . "'"; }
if ($colorFilter) { $totalQuery .= " AND color = '" . $mysqliC->real_escape_string($colorFilter) . "'"; }
if ($typeFilter) { $totalQuery .= " AND type = '" . $mysqliC->real_escape_string($typeFilter) . "'"; }
if ($styleFilter) { $totalQuery .= " AND style = '" . $mysqliC->real_escape_string($styleFilter) . "'"; }
if ($minPrice) { $totalQuery .= " AND price >= " . (float)$minPrice; }
if ($maxPrice) { $totalQuery .= " AND price <= " . (float)$maxPrice; }
if ($search) { $totalQuery .= " AND name LIKE '%" . $mysqliC->real_escape_string($search) . "%'"; }

$totalResult = $mysqliC->query($totalQuery);
if (!$totalResult) {
    error_log("Query error: " . $mysqliC->error);  // Log any SQL errors
}
$totalRow = $totalResult->fetch_assoc();
$totalProducts = $totalRow['total'];

// Calculate the total number of pages
$totalPages = ceil($totalProducts / $itemsPerPage);

// Prepare the response
$response = [
    'products' => $products,
    'total_pages' => $totalPages,
];


// Return the response as JSON
//header('Content-Type: application/json');
echo json_encode($response);
?>
