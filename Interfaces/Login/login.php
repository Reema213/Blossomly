<?php
// Reema Aljaber
session_start();
require_once '../../db_connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $manager_id = $_POST['managerId'];
    $password   = $_POST['password'];

    if (!$manager_id || !$password) {
        $error = 'Please fill in all fields.';
    } 
    elseif (!is_numeric($manager_id)) {
        $error = 'Manager ID must be a number.';
    } 
    else {
        $sql = "SELECT * FROM admins WHERE admin_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt-> bindValue(1, $manager_id);
        $stmt-> execute();
        $admin = $stmt->fetch();

        if ($admin && $password === $admin['password']) {
            $_SESSION['user_id']   = $admin['admin_id'];
            $_SESSION['user_name'] = $admin['username'];
            $_SESSION['role']      = 'admin';
            header("Location: ../Manage_Product/manage_product.php"); 
            exit();
        }

        $error = 'Invalid Manager ID or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blossomly - Login</title>
    <!-- Google Fonts: Playfair Display & Lato -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="style.css">
    <style> 
    /* error message styling */
    .error-msg {
            background-color: #fce4ec;
            color: #c62828;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 18px;
            text-align: left;
            border-left: 3px solid #e57373;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1 class="brand-name">Blossomly</h1>
            <h2 class="title">Manager Login</h2>
            <p class="subtitle">Administrator access only</p>
        </div>

        <!-- Error Message (only shown on failed login) -->
        <?php if ($error): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Form Section -->
        <form id="loginForm" class="login-form" method="POST" action="">
            
            <!-- Manager ID Input -->
            <div class="input-group">
                <label for="managerId">Manager ID</label>
                <input type="number" id="managerId" name="managerId" 
                    placeholder="Enter your manager ID" min ="1"  required>
            </div>

            <!-- Password Input -->
            <div class="input-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password"
                           placeholder="Enter your password" required>
                    <button type="button" id="togglePassword" class="toggle-btn">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Sign In Button -->
            <button type="submit" class="signin-btn">
                Sign In <i class="fas fa-arrow-right"></i>
            </button>

        </form>

        <!-- Footer -->
        <div class="login-footer">
            <p>&copy; 2026 Blossomly - Created by Group 1</p>
        </div>
    </div>

    <script>
        // Password show/hide toggle
        document.getElementById('togglePassword').addEventListener('click', function () {
            const pwd  = document.getElementById('password');
            const icon = this.querySelector('i');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    </script>
</body>
</html>