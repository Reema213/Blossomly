<?php
// Haneen Alsaflan
session_start();
require_once '../../db_connection.php';

$subtotal = 0;
$delivery = 20;

if (isset($_POST['update_qty'])) {
    $p_id    = (int)$_POST['p_id'];
    $new_qty = (int)$_POST['quantity'];
    $stmt    = $pdo->prepare("SELECT stock FROM products WHERE product_id = ?");
    $stmt->execute([$p_id]);
    $product_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product_data && $new_qty > 0 && $new_qty <= $product_data['stock']) {
        $_SESSION['cart'][$p_id] = $new_qty;
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    unset($_SESSION['cart'][$id]);
}

if (isset($_GET['empty'])) {
    unset($_SESSION['cart']);
}

if (isset($_POST['buy_now']) && !empty($_SESSION['cart'])) {

    $existing_ids = [];
    if (isset($_COOKIE['past_purchases']) && !empty($_COOKIE['past_purchases'])) {
        $existing_ids = array_filter(array_map('intval', explode(',', $_COOKIE['past_purchases'])));
    }

    foreach ($_SESSION['cart'] as $product_id => $qty) {
        if (!in_array($product_id, $existing_ids)) {
            $existing_ids[] = $product_id;
        }
    }

    foreach ($_SESSION['cart'] as $product_id => $qty) {
        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ? AND stock >= ?");
        $stmt->execute([$qty, $product_id, $qty]);
    }

    $expiryTime = time() + (86400 * 30);
    setcookie("past_purchases", implode(',', $existing_ids), $expiryTime, "/");

    $summary = "Last Purchase: " . count($_SESSION['cart']) . " items on " . date("Y-m-d H:i");
    setcookie("order_history", $summary, $expiryTime, "/");

    unset($_SESSION['cart']);
    $success = "Order placed successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Blossomly</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-pink-bg: #FCE4EC;
            --color-text-dark: #4E342E;
            --sage-green: #A5D6A7;
            --dark-brown: #3E2723;
            --white: #ffffff;
        }

        body {
            font-family: 'Lato', sans-serif;
            margin: 0;
            background-color: #fdfdfd;
            color: var(--color-text-dark);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .checkout-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 25px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            flex-grow: 1;
        }

        .cart-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--color-pink-bg);
            padding-bottom: 15px;
        }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; font-size: 0.8rem; text-transform: uppercase; border-bottom: 1px solid #eee; }
        td { padding: 12px 15px; border-bottom: 1px solid #f9f9f9; }

        .product-img { width: 65px; height: 65px; background: var(--color-pink-bg); border-radius: 12px; object-fit: cover; }
        .qty-input { width: 45px; padding: 5px; border: 1px solid #ddd; border-radius: 5px; text-align: center; }

        .btn-update { background: var(--sage-green); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 0.7rem; }
        .btn-buy { background-color: var(--dark-brown); color: white; padding: 15px 50px; border: none; border-radius: 8px; font-size: 1.1rem; cursor: pointer; font-family: 'Lato', sans-serif; }

        .summary-box { max-width: 300px; margin-left: auto; margin-top: 30px; border-top: 2px solid #eee; padding-top: 20px; }
        .summary-line { display: flex; justify-content: space-between; margin-bottom: 10px; font-weight: bold; }

        .site-footer { background-color: var(--dark-brown); color: white; text-align: center; padding: 25px; margin-top: auto; }

        .history-badge { margin-top: 20px; padding: 10px; background: #f9f9f9; border-left: 4px solid var(--sage-green); font-size: 0.85rem; }

        .success-box {
            text-align: center;
            padding: 50px;
        }

        .success-box p {
            color: #558B2F;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
        }

        .success-box a {
            color: var(--dark-brown);
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <?php include '../../includes/header.php'; ?>

    <div class="checkout-container">
        <div class="cart-header">
            <h1 style="margin:0; font-family:'Playfair Display', serif;">Your Cart 🛒</h1>
        </div>

        <?php if (isset($success)): ?>
        <div class="success-box">
            <p>✓ <?php echo $success; ?></p>
            <a href="../Home_Page/Home.php">Continue Shopping</a>
        </div>

        <?php elseif (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($_SESSION['cart'] as $id => $qty):
                $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
                $stmt->execute([$id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product):
                    $line_total = $product['price'] * $qty;
                    $subtotal  += $line_total;
            ?>
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:15px;">
                            <img src="../../images/<?php echo $product['picture']; ?>"
                                 class="product-img"
                                 onerror="this.src='https://via.placeholder.com/65'">
                            <div>
                                <div style="font-weight:bold;"><?php echo $product['name']; ?></div>
                                <div style="color:#888; font-size:0.9rem;"><?php echo $product['price']; ?> SAR each</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <form method="POST" style="display:flex; gap:5px;">
                            <input type="hidden" name="p_id" value="<?php echo $id; ?>">
                            <input type="number" name="quantity" value="<?php echo $qty; ?>" min="1" max="<?php echo $product['stock']; ?>" class="qty-input">
                            <button type="submit" name="update_qty" class="btn-update">Update</button>
                        </form>
                    </td>
                    <td style="font-weight:bold;"><?php echo $line_total; ?> SAR</td>
                    <td><a href="?delete=<?php echo $id; ?>" style="text-decoration:none;">🗑️</a></td>
                </tr>
            <?php
                endif;
            endforeach;
            ?>
            </tbody>
        </table>

        <div class="summary-box">
            <div class="summary-line"><span>Subtotal</span> <span><?php echo $subtotal; ?> SAR</span></div>
            <div class="summary-line"><span>Delivery</span> <span><?php echo $delivery; ?> SAR</span></div>
            <div class="summary-line" style="font-size:1.4rem; color:#3E2723;">
                <span>Total</span> <span><?php echo $subtotal + $delivery; ?> SAR</span>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">
                <a href="?empty=1" style="color:#777; font-size:0.9rem;">Empty Cart</a>
                <form method="POST">
                    <button type="submit" name="buy_now" class="btn-buy">Buy Now</button>
                </form>
            </div>
        </div>

        <?php else: ?>
        <div style="text-align:center; padding:50px;">
            <p>Your cart is currently empty.</p>
            <a href="../Home_Page/Home.php" style="color:#3E2723; font-weight:bold;">Continue Shopping</a>
        </div>
        <?php endif; ?>

        <?php if (isset($_COOKIE['order_history'])): ?>
        <div class="history-badge">
            <strong>Order History:</strong> <?php echo $_COOKIE['order_history']; ?>
        </div>
        <?php endif; ?>

    </div>

    <?php include '../../includes/footer.html'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const emptyCartLink = document.querySelector('a[href="?empty=1"]');
            if (emptyCartLink) {
                emptyCartLink.addEventListener('click', function(event) {
                    const confirmEmpty = confirm("Are you sure you want to delete all items from your cart?");
                    if (!confirmEmpty) {
                        event.preventDefault();
                    }
                });
            }

            const removeButtons = document.querySelectorAll('a[href*="delete="]');
            removeButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    const confirmDelete = confirm("Are you sure you want to remove this item?");
                    if (!confirmDelete) {
                        event.preventDefault();
                    }
                });
            });

            const buyNowButton = document.querySelector('button[name="buy_now"]');
            if (buyNowButton) {
                const checkoutForm = buyNowButton.closest('form');

                checkoutForm.addEventListener('submit', function(event) {
                    const quantityInputs = document.querySelectorAll('input[name="quantity"]');
                    let hasError = false;

                    quantityInputs.forEach(input => {
                        const qtyValue = parseInt(input.value);
                        if (qtyValue <= 0 || isNaN(qtyValue)) {
                            hasError = true;
                        }
                    });

                    if (hasError) {
                        event.preventDefault();
                        alert("Error: Quantity cannot be 0 or negative. Please enter a valid number.");
                    } else {
                        const confirmPurchase = confirm("Do you want to proceed with the purchase?");
                        if (!confirmPurchase) {
                            event.preventDefault();
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>