<?php
// Juri Sulayhim
session_start();
require_once '../../db_connection.php';

if (isset($_POST['add_to_cart'])) {
    $p_id = (int)$_POST['product_id'];
    $qty  = (int)$_POST['quantity'];

    $stmt = $pdo->prepare("SELECT stock FROM products WHERE product_id = ?");
    $stmt->execute([$p_id]);
    $stock_data = $stmt->fetch(PDO::FETCH_ASSOC);

    $current_in_cart = isset($_SESSION['cart'][$p_id]) ? $_SESSION['cart'][$p_id] : 0;

    if ($stock_data && ($current_in_cart + $qty) <= $stock_data['stock']) {
        if (isset($_SESSION['cart'][$p_id])) {
            $_SESSION['cart'][$p_id] += $qty;
        } else {
            $_SESSION['cart'][$p_id] = $qty;
        }
        header("Location: ../Home_Page/Home.php");
        exit();
    } else {
        $error = "Sorry, not enough stock available.";
    }
}

if (isset($_POST['checkout_now'])) {
    $p_id = (int)$_POST['product_id'];
    $qty  = (int)$_POST['quantity'];

    $stmt = $pdo->prepare("SELECT stock FROM products WHERE product_id = ?");
    $stmt->execute([$p_id]);
    $stock_data = $stmt->fetch(PDO::FETCH_ASSOC);

    $current_in_cart = isset($_SESSION['cart'][$p_id]) ? $_SESSION['cart'][$p_id] : 0;

    if ($stock_data && ($current_in_cart + $qty) <= $stock_data['stock']) {
        if (isset($_SESSION['cart'][$p_id])) {
            $_SESSION['cart'][$p_id] += $qty;
        } else {
            $_SESSION['cart'][$p_id] = $qty;
        }
        header("Location: ../Checkout/checkout.php");
        exit();
    } else {
        $error = "Sorry, not enough stock available.";
    }
}

if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        die("Product not found.");
    }
} else {
    die("No product ID provided.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blossomly - <?php echo $product['name']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .title-row {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            position: relative;
        }

        .help-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: #FCE4EC;
            border: 1px solid #8D6E63;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            color: #4E342E;
            flex-shrink: 0;
        }

        .help-btn:hover {
            background-color: #FFCDD2;
        }

        #popupHelp {
            position: absolute;
            top: 3rem;
            right: 0;
            left: auto;
            background-color: #ffffff;
            border: 1px solid #e8d5d0;
            border-radius: 12px;
            padding: 1.5rem;
            width: 280px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            z-index: 1000;
        }

        #popupHelp h3 {
            font-size: 1rem;
            color: #3E2723;
            margin-bottom: 0.8rem;
            font-family: 'Playfair Display', serif;
        }

        #popupHelp p {
            font-size: 0.85rem;
            color: #757575;
            margin-bottom: 0.6rem;
            line-height: 1.5;
        }

        .popup-btn {
            margin-top: 0.8rem;
            background-color: #A5D6A7;
            color: #3E2723;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
        }

        .popup-btn:hover {
            background-color: #81C784;
        }

        .error-msg {
            color: #D32F2F;
            font-weight: bold;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <?php include '../../includes/header.php'; ?>

    <main class="main-content">
        <div class="product-container">

            <div class="product-image-section">
                <img src="../../images/<?php echo $product['picture']; ?>"
                     alt="<?php echo $product['name']; ?>"
                     onerror="this.src='https://via.placeholder.com/500'">
            </div>

            <div class="product-details-section">
                <span class="product-category">Fresh Flowers</span>

                <div class="title-row">
                    <h1 class="product-title"><?php echo $product['name']; ?></h1>
                    <button type="button" id="helpBtn" class="help-btn">?</button>
                </div>

                <h2 class="product-price"><?php echo $product['price']; ?> SAR</h2>
                <p class="product-description"><?php echo $product['description']; ?></p>

                <?php if (isset($error)): ?>
                    <p class="error-msg"><?php echo $error; ?></p>
                <?php endif; ?>

                <form class="product-form" action="" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                    <div class="quantity-wrapper">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    </div>

                    <div class="button-group">
                        <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                        <button type="submit" name="checkout_now" class="btn btn-secondary">Checkout Now</button>
                    </div>
                </form>
            </div>

        </div>
    </main>

    <?php include '../../includes/footer.html'; ?>

    <script>
        document.getElementById("helpBtn").addEventListener("click", function () {
            const existing = document.getElementById("popupHelp");
            if (existing) {
                existing.remove();
                return;
            }

            const helpBox = document.createElement("div");
            helpBox.id = "popupHelp";

            const title = document.createElement("h3");
            title.textContent = "Need Help?";

            const text1 = document.createElement("p");
            text1.textContent = "Add to Cart: saves the item to your cart so you can keep browsing and buy later.";

            const text2 = document.createElement("p");
            text2.textContent = "Checkout Now: adds the item and takes you straight to checkout to complete your order.";

            const text3 = document.createElement("p");
            text3.textContent = "Use the quantity field to choose how many you want before clicking either button.";

            const closeBtn = document.createElement("button");
            closeBtn.textContent = "Close";
            closeBtn.className = "popup-btn";
            closeBtn.addEventListener("click", function () {
                helpBox.remove();
            });

            helpBox.appendChild(title);
            helpBox.appendChild(text1);
            helpBox.appendChild(text2);
            helpBox.appendChild(text3);
            helpBox.appendChild(closeBtn);

            document.querySelector('.title-row').appendChild(helpBox);
        });
    </script>

</body>
</html>