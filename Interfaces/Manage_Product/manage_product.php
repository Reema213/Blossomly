<?php
// Sharifah Alyousef
// 1. Start Session & Connect DB
session_start();
require_once '../../db_connection.php';

// 2. Check Admin Login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Login/login.php');
    exit;
}

// 3. Handle AJAX Requests
$action = $_GET['action'] ?? $_POST['action'] ?? '';

function sendJson($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Get all products
if ($action === 'get_products') {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY product_id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    sendJson(['success' => true, 'products' => $products]);
}

// Add product
if ($action === 'add_product') {
    $name        = trim($_POST['name'] ?? '');
    $price       = floatval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category'] ?? 0);
    $stock       = intval($_POST['stock'] ?? 0);
    $desc        = trim($_POST['desc'] ?? '');

    if (empty($name) || $category_id < 1 || $category_id > 4 || $price <= 0 || $stock < 0) {
        sendJson(['success' => false, 'message' => 'Invalid product data']);
    }

    $picture = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../images/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array($ext, $allowed)) {
            $filename = uniqid() . '.' . $ext;
            $dest     = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $picture = $filename;
            }
        }
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, stock, description, picture) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $category_id, $price, $stock, $desc, $picture]);
    sendJson(['success' => true, 'image_path' => $picture]);
}

// Edit product
if ($action === 'edit_product') {
    $id          = intval($_POST['id'] ?? 0);
    $name        = trim($_POST['name'] ?? '');
    $price       = floatval($_POST['price'] ?? 0);
    $stock       = intval($_POST['stock'] ?? 0);
    $category_id = intval($_POST['category'] ?? 0);
    $desc        = trim($_POST['desc'] ?? '');

    if (!$id || empty($name) || $price <= 0 || $stock < 0 || $category_id < 1 || $category_id > 4) {
        sendJson(['success' => false, 'message' => 'Invalid edit data']);
    }

    $picture = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../images/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array($ext, $allowed)) {
            // Delete old picture
            $stmt = $pdo->prepare("SELECT picture FROM products WHERE product_id = ?");
            $stmt->execute([$id]);
            $old = $stmt->fetch();
            if ($old && !empty($old['picture']) && file_exists('../../images/' . $old['picture'])) {
                unlink('../../images/' . $old['picture']);
            }
            $filename = uniqid() . '.' . $ext;
            $dest     = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $picture = $filename;
            }
        }
    }

    if ($picture) {
        $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, stock=?, category_id=?, description=?, picture=? WHERE product_id=?");
        $stmt->execute([$name, $price, $stock, $category_id, $desc, $picture, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, stock=?, category_id=?, description=? WHERE product_id=?");
        $stmt->execute([$name, $price, $stock, $category_id, $desc, $id]);
    }
    sendJson(['success' => true]);
}

// Delete product
if ($action === 'delete_product') {
    $id = intval($_POST['id'] ?? 0);
    if ($id) {
        $stmt = $pdo->prepare("SELECT picture FROM products WHERE product_id = ?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch();
        if ($prod && !empty($prod['picture']) && file_exists('../../images/' . $prod['picture'])) {
            unlink('../../images/' . $prod['picture']);
        }
        $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->execute([$id]);
        sendJson(['success' => true]);
    }
    sendJson(['success' => false]);
}

// 4. Fetch all products for initial display
$stmt         = $pdo->query("SELECT * FROM products ORDER BY product_id DESC");
$all_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Blossomly</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(62, 39, 35, 0.45);
        z-index: 2000;
        display: none;
        justify-content: center;
        align-items: flex-start;
        overflow-y: scroll;
        padding: 20px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal {
        background: var(--white);
        border-radius: 16px;
        padding: 2rem;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 8px 40px rgba(62, 39, 35, 0.2);
        margin: auto;
        height: fit-content;
    }

    /* Fix table row spacing */
    .products-table td {
        padding: 0.6rem 1rem;
        border-bottom: 1px solid #f3e5e0;
        vertical-align: middle;
    }

    .products-table tbody tr {
        height: auto;
    }
</style>
</head>
<body>

    <?php include '../../includes/header.php'; ?>

    <main class="admin-main">
        <div class="page-hero">
            <h1>Manage Products</h1>
            <p>Add, edit, or remove products from your store.</p>
        </div>

        <!-- Add Product Section -->
        <section class="add-product-section">
            <h2>Add New Product</h2>
            <div class="add-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" id="productName" placeholder="e.g. Rose Bouquet">
                    </div>
                    <div class="form-group">
                        <label>Price (SAR)</label>
                        <input type="number" id="productPrice" placeholder="e.g. 120" min="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select id="productCategory">
                            <option value="">Select...</option>
                            <option value="1">Bouquets</option>
                            <option value="2">Singles</option>
                            <option value="3">Plants</option>
                            <option value="4">Arrangements</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" id="productStock" placeholder="e.g. 50" min="0">
                    </div>
                </div>
                <div class="form-group full-width">
                    <label>Description</label>
                    <textarea id="productDesc" rows="3"></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Product Image</label>
                    <input type="file" id="productImage" accept="image/jpeg,image/png,image/jpg" onchange="previewAddImage(event)">
                    <div id="addImagePreview" style="margin-top:10px; max-width:120px; display:none;">
                        <img id="addPreviewImg" style="width:100%; border-radius:8px;">
                    </div>
                </div>
                <button class="add-btn" onclick="addProduct()">+ Add Product</button>
                <div class="success-msg" id="addSuccess">✓ Product added successfully!</div>
            </div>
        </section>

        <!-- Products List Section -->
        <section class="products-section">
            <div class="section-header">
                <h2>All Products <span class="product-count" id="productCount"><?php echo count($all_products); ?></span></h2>
                <input type="text" id="searchInput" class="search-input" placeholder="Search products..." oninput="searchProducts()">
            </div>
            <div class="table-wrapper">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody"></tbody>
                </table>
                <p class="no-results" id="noResults" style="display:none;">No products found.</p>
            </div>
        </section>
    </main>

    <!-- Edit Modal -->
    <div class="modal-overlay" id="editModal">
        <div class="modal">
            <div class="modal-header">
                <h2>Edit Product</h2>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" id="editName">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Price (SAR)</label>
                    <input type="number" id="editPrice" min="0">
                </div>
                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" id="editStock" min="0">
                </div>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select id="editCategory">
                    <option value="1">Bouquets</option>
                    <option value="2">Singles</option>
                    <option value="3">Plants</option>
                    <option value="4">Arrangements</option>
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea id="editDesc" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" id="editImage" accept="image/jpeg,image/png,image/jpg" onchange="previewEditImage(event)">
                <div id="editImagePreview" style="margin-top:10px; max-width:120px;">
                    <img id="editPreviewImg" style="width:100%; border-radius:8px;">
                </div>
            </div>
            <div class="modal-actions">
                <button class="cancel-btn" onclick="closeModal()">Cancel</button>
                <button class="save-btn" onclick="saveEdit()">Save Changes</button>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal modal-sm">
            <div class="modal-header">
                <h2>Delete Product</h2>
                <button class="modal-close" onclick="closeDeleteModal()">✕</button>
            </div>
            <p>Are you sure you want to delete <strong id="deleteProductName"></strong>? This cannot be undone.</p>
            <div class="modal-actions">
                <button class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
                <button class="delete-confirm-btn" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>

    <footer class="site-footer">
        <p>&copy; 2026 Blossomly - Created by Group 1</p>
    </footer>

    <script>
        // Category map
        const categoryMap = {
            1: 'Bouquets',
            2: 'Singles',
            3: 'Plants',
            4: 'Arrangements'
        };

        let products   = <?php echo json_encode($all_products); ?>;
        let editingId  = null;
        let deletingId = null;

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        function renderTable(data) {
            const tbody = document.getElementById('productTableBody');
            tbody.innerHTML = '';
            if (data.length === 0) {
                document.getElementById('noResults').style.display = 'block';
                return;
            }
            document.getElementById('noResults').style.display = 'none';
            data.forEach((p, idx) => {
                const stockClass   = p.stock <= 5 ? 'stock-low' : p.stock <= 15 ? 'stock-mid' : 'stock-ok';
                const categoryName = categoryMap[p.category_id] || 'Unknown';
                const imgSrc       = p.picture ? '../../images/' + p.picture : '../../images/default.jpg';
                const row          = document.createElement('tr');
                row.innerHTML = `
                    <td>${idx + 1}</td>
                    <td class="product-name">${escapeHtml(p.name)}</td>
                    <td><span class="category-tag">${categoryName}</span></td>
                    <td class="price-cell">${p.price} SAR</td>
                    <td><span class="stock-badge ${stockClass}">${p.stock}</span></td>
                    <td><img src="${imgSrc}" alt="${escapeHtml(p.name)}" style="width:50px; height:50px; object-fit:cover; border-radius:8px;" onerror="this.src='https://via.placeholder.com/50'"></td>
                    <td class="actions-cell">
                        <button class="edit-btn" onclick="openEdit(${p.product_id})">Edit</button>
                        <button class="delete-btn" onclick="openDelete(${p.product_id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
            document.getElementById('productCount').innerText = products.length;
        }

        // FIXED: explicit URL
        async function refreshProducts() {
            const res  = await fetch('manage_product.php?action=get_products');
            const data = await res.json();
            if (data.success) {
                products = data.products;
                renderTable(products);
            }
        }

        function addProduct() {
            const name      = document.getElementById('productName').value.trim();
            const price     = parseFloat(document.getElementById('productPrice').value);
            const category  = document.getElementById('productCategory').value;
            const stock     = parseInt(document.getElementById('productStock').value);
            const desc      = document.getElementById('productDesc').value.trim();
            const imageFile = document.getElementById('productImage').files[0];

            if (!name || isNaN(price) || !category || isNaN(stock)) {
                alert('Please fill all required fields.');
                return;
            }

            const formData = new FormData();
            formData.append('action',   'add_product');
            formData.append('name',     name);
            formData.append('price',    price);
            formData.append('category', category);
            formData.append('stock',    stock);
            formData.append('desc',     desc);
            if (imageFile) formData.append('image', imageFile);

            // FIXED: explicit URL
            fetch('manage_product.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('productName').value     = '';
                        document.getElementById('productPrice').value    = '';
                        document.getElementById('productCategory').value = '';
                        document.getElementById('productStock').value    = '';
                        document.getElementById('productDesc').value     = '';
                        document.getElementById('productImage').value    = '';
                        document.getElementById('addImagePreview').style.display = 'none';
                        document.getElementById('addSuccess').style.display     = 'block';
                        setTimeout(() => document.getElementById('addSuccess').style.display = 'none', 3000);
                        refreshProducts();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
        }

        function searchProducts() {
            const q        = document.getElementById('searchInput').value.toLowerCase();
            const filtered = products.filter(p =>
                p.name.toLowerCase().includes(q) ||
                (categoryMap[p.category_id] || '').toLowerCase().includes(q)
            );
            renderTable(filtered);
        }

        function openEdit(id) {
            const product = products.find(p => p.product_id === id);
            if (!product) return;
            editingId = id;
            document.getElementById('editName').value     = product.name;
            document.getElementById('editPrice').value    = product.price;
            document.getElementById('editStock').value    = product.stock;
            document.getElementById('editCategory').value = product.category_id;
            document.getElementById('editDesc').value     = product.description || '';
            document.getElementById('editPreviewImg').src = product.picture ? '../../images/' + product.picture : '../../images/default.jpg';
            document.getElementById('editModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('editModal').classList.remove('active');
            editingId = null;
        }

        function saveEdit() {
            const name      = document.getElementById('editName').value.trim();
            const price     = parseFloat(document.getElementById('editPrice').value);
            const stock     = parseInt(document.getElementById('editStock').value);
            const category  = document.getElementById('editCategory').value;
            const desc      = document.getElementById('editDesc').value.trim();
            const imageFile = document.getElementById('editImage').files[0];

            if (!name || isNaN(price) || isNaN(stock) || !category) {
                alert('Please fill all required fields.');
                return;
            }

            const formData = new FormData();
            formData.append('action',   'edit_product');
            formData.append('id',       editingId);
            formData.append('name',     name);
            formData.append('price',    price);
            formData.append('stock',    stock);
            formData.append('category', category);
            formData.append('desc',     desc);
            if (imageFile) formData.append('image', imageFile);

            // FIXED: explicit URL
            fetch('manage_product.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        refreshProducts();
                        closeModal();
                    } else {
                        alert('Update failed: ' + data.message);
                    }
                });
        }

        function openDelete(id) {
            const product = products.find(p => p.product_id === id);
            if (!product) return;
            deletingId = id;
            document.getElementById('deleteProductName').innerText = product.name;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
            deletingId = null;
        }

        function confirmDelete() {
            const formData = new FormData();
            formData.append('action', 'delete_product');
            formData.append('id',     deletingId);

            // FIXED: explicit URL
            fetch('manage_product.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        refreshProducts();
                        closeDeleteModal();
                    } else {
                        alert('Delete failed.');
                    }
                });
        }

        function previewAddImage(event) {
            previewImage(event.target.files[0], 'addPreviewImg', 'addImagePreview');
        }

        function previewEditImage(event) {
            previewImage(event.target.files[0], 'editPreviewImg', null);
        }

        function previewImage(file, imgId, containerId) {
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(imgId).src = e.target.result;
                    if (containerId) document.getElementById(containerId).style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });

        // Initial render
        renderTable(products);
    </script>
</body>
</html>