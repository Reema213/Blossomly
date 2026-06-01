<?php
// Reema Aljaber

// Check if admin is logged in
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Calculate cart totals (for guests only)
$total_price = 0;
$total_items = 0;

if (!$is_admin && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if ($product) {
            $total_price += $product['price'] * $qty;
            $total_items += $qty;
        }
    }
}
?>

<style>
    :root {
        --color-pink-bg: #FCE4EC;
        --color-text-dark: #4E342E;
        --color-text-light: #8D6E63;
        --color-green: #558B2F;
        --color-red-badge: #D32F2F;
        --font-logo: 'Playfair Display', serif;
        --font-nav: 'Lato', sans-serif;
        --container-width: 1200px;
    }

    .site-header * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    a { text-decoration: none; color: inherit; }

    .site-header {
        background-color: var(--color-pink-bg);
        padding: 1rem 2rem;
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-family: var(--font-nav);
    }

    /* grid to center nav menu */
    .header-container {
        max-width: var(--container-width);
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
    }

    /* Logo */
    .brand-logo {
        font-family: var(--font-logo);
        font-size: 1.8rem;
        font-weight: 600;
        color: var(--color-text-dark);
        letter-spacing: -0.5px;
    }

    /* Nav Links */
    .main-nav {
        display: flex;
        gap: 3rem;
        align-items: center;
    }

    .main-nav a {
        font-size: 0.9rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        color: var(--color-text-dark);
        transition: color 0.3s;
    }

    .main-nav a:hover {
        color: var(--color-green);
    }

    /* Admin name label */
    .nav-admin-name {
        font-size: 0.85rem;
        color: var(--color-text-light);
    }

    /* Logout Button */
    .nav-logout-btn {
        background: none;
        border: none;
        font-family: var(--font-nav);
        font-size: 0.9rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        color: var(--color-red-badge);
        cursor: pointer;
        padding: 0;
        transition: opacity 0.3s;
    }

    .nav-logout-btn:hover {
        opacity: 0.75;
    }

    /* cart pushed to the right */
    .cart-section {
        justify-self: end;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cart-icon-wrapper {
        position: relative;
        width: 28px;
        height: 28px;
    }

    .cart-icon-wrapper svg {
        width: 100%;
        height: 100%;
        fill: var(--color-text-dark);
    }

    .cart-badge {
        position: absolute;
        top: -6px;
        right: -8px;
        background-color: var(--color-red-badge);
        color: white;
        font-size: 0.7rem;
        font-weight: 700;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
    }

    .cart-info {
        display: flex;
        flex-direction: column;
        line-height: 1.1;
    }

    .cart-label {
        font-size: 0.7rem;
        color: var(--color-text-light);
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .cart-price {
        font-size: 1rem;
        font-weight: 700;
        color: var(--color-text-dark);
    }
</style>

<!-- HEADER -->
<header class="site-header">
    <div class="header-container">

        <!-- Logo -->
        <a href="../Home_Page/Home.php" class="brand-logo">Blossomly</a>

        <!-- Navigation -->
        <nav class="main-nav">
            <a href="../Home_Page/Home.php">HOME</a>
            <a href="../Contact_Us/contact.php">CONTACT</a>

            <?php if ($is_admin): ?>
                <!-- Admin is logged in: show name + logout button -->
                <span class="nav-admin-name">
                    Hi, <?php echo $_SESSION['user_name']; ?>
                </span>
                <form method="POST" action="../Login/start.php" style="margin:0">
                    <button type="submit" class="nav-logout-btn">LOGOUT</button>
                </form>
            <?php else: ?>
                <!-- Guest: show login -->
                <a href="../Login/login.php">LOGIN</a>
            <?php endif; ?>
        </nav>

        <!-- Cart (hidden for admins) -->
        <?php if (!$is_admin): ?>
        <div class="cart-section">
            <div class="cart-icon-wrapper">
                <a href="../Checkout/checkout.php">
                    <!-- Cart SVG Icon -->
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                    <!-- Item Count Badge -->
                    <span class="cart-badge"><?php echo $total_items; ?></span>
                </a>
            </div>

            <!-- Cart Total -->
            <div class="cart-info">
                <a href="../Checkout/checkout.php">
                    <span class="cart-label">TOTAL</span>
                    <span class="cart-price"><?php echo $total_price; ?> SAR</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

    </div>
</header>