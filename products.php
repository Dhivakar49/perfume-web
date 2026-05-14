<?php
require_once __DIR__ . '/bootstrap.php';

$page_title = 'Shop Products - Perfume Palace';
$page_description = 'Browse our complete collection of luxury perfumes and fragrances';

include 'includes/header.php';

// Get filter parameters
$category = $_GET['category'] ?? '';
$brand = $_GET['brand'] ?? '';
$filter = $_GET['filter'] ?? ''; // featured, new, bestseller, sale
$sort = $_GET['sort'] ?? 'newest';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = ITEMS_PER_PAGE;
$offset = ($page - 1) * $per_page;

try {
    $db = Database::getInstance()->getConnection();
    
    // Build query
    $where_conditions = ["p.status = 'active'"];
    $params = [];
    
    if ($category) {
        $where_conditions[] = "c.slug = ?";
        $params[] = $category;
    }
    
    if ($brand) {
        $where_conditions[] = "b.slug = ?";
        $params[] = $brand;
    }
    
    if ($filter === 'featured') {
        $where_conditions[] = "p.is_featured = 1";
    } elseif ($filter === 'new') {
        $where_conditions[] = "p.is_new = 1";
    } elseif ($filter === 'bestseller') {
        $where_conditions[] = "p.is_bestseller = 1";
    } elseif ($filter === 'sale') {
        $where_conditions[] = "p.sale_price IS NOT NULL";
    }
    
    if ($search) {
        $where_conditions[] = "(p.name LIKE ? OR p.description LIKE ? OR p.meta_keywords LIKE ?)";
        $search_term = "%{$search}%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Sorting
    $order_by = match($sort) {
        'price_low' => 'COALESCE(p.sale_price, p.price) ASC',
        'price_high' => 'COALESCE(p.sale_price, p.price) DESC',
        'name' => 'p.name ASC',
        'rating' => 'p.rating_avg DESC',
        'popular' => 'p.sales_count DESC',
        default => 'p.created_at DESC'
    };
    
    // Get total count
    $count_sql = "SELECT COUNT(*) as total FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  LEFT JOIN brands b ON p.brand_id = b.id 
                  WHERE {$where_clause}";
    $stmt = $db->prepare($count_sql);
    $stmt->execute($params);
    $total_products = $stmt->fetch()['total'];
    $total_pages = ceil($total_products / $per_page);
    
    // Get products
    $sql = "SELECT p.*, pi.image_path, c.name as category_name, b.name as brand_name
            FROM products p
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE {$where_clause}
            ORDER BY {$order_by}
            LIMIT {$per_page} OFFSET {$offset}";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    // Get categories for filter
    $stmt = $db->query("SELECT * FROM categories WHERE is_active = 1 AND parent_id IS NULL ORDER BY name");
    $categories = $stmt->fetchAll();
    
    // Get brands for filter
    $stmt = $db->query("SELECT * FROM brands WHERE is_active = 1 ORDER BY name");
    $brands = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Products page error: " . $e->getMessage());
    $products = [];
    $total_products = 0;
}
?>

<!-- Breadcrumb -->
<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Products</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Products Section -->
<section class="section">
    <div class="container">
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="filter-sidebar">
                    <h4 class="filter-title">Filters</h4>
                    
                    <!-- Categories -->
                    <div class="filter-group">
                        <h5 class="filter-group-title">Categories</h5>
                        <?php foreach ($categories as $cat): ?>
                            <div class="filter-option">
                                <input type="checkbox" id="cat-<?php echo $cat['id']; ?>" 
                                       value="<?php echo $cat['slug']; ?>"
                                       <?php echo $category === $cat['slug'] ? 'checked' : ''; ?>>
                                <label for="cat-<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Brands -->
                    <div class="filter-group">
                        <h5 class="filter-group-title">Brands</h5>
                        <?php foreach ($brands as $b): ?>
                            <div class="filter-option">
                                <input type="checkbox" id="brand-<?php echo $b['id']; ?>" 
                                       value="<?php echo $b['slug']; ?>"
                                       <?php echo $brand === $b['slug'] ? 'checked' : ''; ?>>
                                <label for="brand-<?php echo $b['id']; ?>"><?php echo $b['name']; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Price Range -->
                    <div class="filter-group">
                        <h5 class="filter-group-title">Price Range</h5>
                        <div class="price-range-slider">
                            <input type="range" class="form-range" min="0" max="10000" step="100" id="price-range">
                            <div class="d-flex justify-content-between mt-2">
                                <span>₹0</span>
                                <span id="price-value">₹10000</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Special Filters -->
                    <div class="filter-group">
                        <h5 class="filter-group-title">Special</h5>
                        <div class="filter-option">
                            <input type="checkbox" id="filter-sale" value="sale" <?php echo $filter === 'sale' ? 'checked' : ''; ?>>
                            <label for="filter-sale">On Sale</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="filter-new" value="new" <?php echo $filter === 'new' ? 'checked' : ''; ?>>
                            <label for="filter-new">New Arrivals</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="filter-featured" value="featured" <?php echo $filter === 'featured' ? 'checked' : ''; ?>>
                            <label for="filter-featured">Featured</label>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary w-100 mt-3" onclick="applyFilters()">Apply Filters</button>
                    <button class="btn btn-outline-secondary w-100 mt-2" onclick="clearFilters()">Clear All</button>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="col-lg-9">
                <!-- Products Header -->
                <div class="products-header">
                    <div class="products-count">
                        Showing <?php echo min($offset + 1, $total_products); ?>-<?php echo min($offset + $per_page, $total_products); ?> 
                        of <?php echo $total_products; ?> products
                    </div>
                    <div class="products-sort">
                        <select class="form-select" id="sort-select" onchange="changeSort(this.value)">
                            <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Name: A to Z</option>
                            <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                            <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                        </select>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <?php if (!empty($products)): ?>
                    <div class="products-grid">
                        <?php foreach ($products as $product): ?>
                            <?php include 'includes/product-card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Products pagination" class="mt-4">
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&<?php echo http_build_query(array_diff_key($_GET, ['page' => ''])); ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query(array_diff_key($_GET, ['page' => ''])); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&<?php echo http_build_query(array_diff_key($_GET, ['page' => ''])); ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>No products found</h4>
                        <p class="text-muted">Try adjusting your filters or search terms</p>
                        <a href="products.php" class="btn btn-primary mt-3">View All Products</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function changeSort(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', value);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}

function applyFilters() {
    const url = new URL(window.location.href);
    
    // Get selected category
    const selectedCategory = document.querySelector('input[id^="cat-"]:checked');
    if (selectedCategory) {
        url.searchParams.set('category', selectedCategory.value);
    } else {
        url.searchParams.delete('category');
    }
    
    // Get selected brand
    const selectedBrand = document.querySelector('input[id^="brand-"]:checked');
    if (selectedBrand) {
        url.searchParams.set('brand', selectedBrand.value);
    } else {
        url.searchParams.delete('brand');
    }
    
    // Get special filters
    const saleFilter = document.getElementById('filter-sale');
    const newFilter = document.getElementById('filter-new');
    const featuredFilter = document.getElementById('filter-featured');
    
    if (saleFilter.checked) {
        url.searchParams.set('filter', 'sale');
    } else if (newFilter.checked) {
        url.searchParams.set('filter', 'new');
    } else if (featuredFilter.checked) {
        url.searchParams.set('filter', 'featured');
    } else {
        url.searchParams.delete('filter');
    }
    
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}

function clearFilters() {
    window.location.href = 'products.php';
}

// Price range slider
document.getElementById('price-range').addEventListener('input', function() {
    document.getElementById('price-value').textContent = '₹' + this.value;
});
</script>

<?php include 'includes/footer.php'; ?>
