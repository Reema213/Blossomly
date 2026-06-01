<?php
// Layan Mohammed Alharthi 

session_start();
require_once '../../db_connection.php';

// Redirect admin to manage products
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: ../Manage_Product/manage_product.php");
    exit();
}

// Fetch all products
$stmt         = $pdo->query("SELECT * FROM products ORDER BY name ASC");
$all_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Past purchases — read from cookie
$past_products = [];

if (isset($_COOKIE['past_purchases']) && !empty($_COOKIE['past_purchases'])) {
    // Split cookie value into array of IDs
    $ids_array = array_filter(array_map('intval', explode(',', $_COOKIE['past_purchases'])));

    if (!empty($ids_array)) {
        $placeholders = implode(',', array_fill(0, count($ids_array), '?'));
        $stmt2 = $pdo->prepare("SELECT product_id, name, picture, price FROM products WHERE product_id IN ($placeholders)");
        $stmt2->execute($ids_array);
        $past_products = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blossomly - Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* image size in past purchases */
        .past-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .past-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            text-align: center;
        }

        .past-card img {
            width: 100% !important;
            height: 180px !important;
            object-fit: cover !important;
            display: block;
        }

        .past-card a {
            text-decoration: none;
            color: inherit;
        }

        .past-name {
            font-weight: 700;
            color: #4E342E;
            margin: 0.5rem 0 0.2rem;
            padding: 0 0.5rem;
        }

        .past-price {
            color: #558B2F;
            font-weight: 700;
            margin-bottom: 0.8rem;
        }
    </style>
</head>
<body>

<?php include '../../includes/header.php'; ?>

<!-- ===================== HERO BANNER ===================== -->
<section class="hero-banner">
    <img src="../../images/banner.jpeg" alt="Spring Flower Banner" class="banner-img">
    <div class="banner-content">
        <h2>Spring Collection is Here!</h2><br>
        <p>Discover our latest bouquets and bring freshness to your home.</p><br>
        <button class="shop-now-btn"
                onclick="document.getElementById('products-section').scrollIntoView({behavior:'smooth'})">
            Shop Now
        </button>
    </div>
</section>

<!-- ===================== PAST PURCHASES ===================== -->
<?php if (!empty($past_products)): ?>
<section class="past-purchases">
    <div class="section-title">
        <h2>Your Past Purchases</h2>
        <p>Welcome back! Here are your previous beautiful choices.</p>
    </div>
    <div class="past-grid">
        <?php foreach ($past_products as $pp): ?>
            <div class="past-card">
                <a href="../Product_Details/product_detail.php?id=<?php echo $pp['product_id']; ?>">
                    <img src="../../images/<?php echo $pp['picture']; ?>"
                         alt="<?php echo $pp['name']; ?>"
                         onerror="this.src='https://via.placeholder.com/150'">
                    <p class="past-name"><?php echo $pp['name']; ?></p>
                    <p class="past-price"><?php echo $pp['price']; ?> SAR</p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ===================== PRODUCTS SECTION ===================== -->
<section class="our-products" id="products-section">
    <div class="section-title">
        <h2>Explore Our Flowers</h2>
    </div>

    <nav class="category-filters">
        <button class="filter-btn active" onclick="filterProducts('all', this)">All</button>
        <button class="filter-btn" onclick="filterProducts('Bouquets', this)">Bouquets</button>
        <button class="filter-btn" onclick="filterProducts('Singles', this)">Singles</button>
        <button class="filter-btn" onclick="filterProducts('Plants', this)">Plants</button>
        <button class="filter-btn" onclick="filterProducts('Arrangements', this)">Arrangements</button>
    </nav>

    <div class="product-grid" id="product-grid">
        <?php foreach ($all_products as $product): ?>
            <?php
            $name = strtolower($product['name']);
            $cat  = 'Bouquets';
            if (strpos($name, 'single') !== false || strpos($name, 'stem') !== false) {
                $cat = 'Singles';
            } elseif (strpos($name, 'pot') !== false || strpos($name, 'plant') !== false || strpos($name, 'succulent') !== false) {
                $cat = 'Plants';
            } elseif (strpos($name, 'arrangement') !== false || strpos($name, 'vase') !== false ||
                      strpos($name, 'tropical') !== false     || strpos($name, 'baby') !== false ||
                      strpos($name, 'pastel') !== false) {
                $cat = 'Arrangements';
            }
            ?>
            <div class="product-card" data-category="<?php echo $cat; ?>">
                <a href="../Product_Details/product_detail.php?id=<?php echo $product['product_id']; ?>">
                    <img src="../../images/<?php echo $product['picture']; ?>"
                         alt="<?php echo $product['name']; ?>"
                         onerror="this.src='https://via.placeholder.com/300'">
                    <h3><?php echo $product['name']; ?></h3>
                    <p class="description"><?php echo $product['description']; ?></p>
                    <p class="price"><?php echo $product['price']; ?> SAR</p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <p id="no-results" style="display:none; text-align:center; color:#888; padding:30px;">
        No products found in this category.
    </p>
</section>

<?php include '../../includes/footer.html'; ?>

<script>
    function filterProducts(selectedCategory, clickedBtn) {
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        clickedBtn.classList.add('active');

        let visible = 0;
        document.querySelectorAll('.product-card').forEach(card => {
            if (selectedCategory === 'all' || card.dataset.category === selectedCategory) {
                card.style.display = 'block';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });
        document.getElementById('no-results').style.display = visible === 0 ? 'block' : 'none';
    }
</script>

</body>
</html>