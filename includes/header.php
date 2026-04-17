<?php
// ✅ SAFE SESSION (ERROR FIX)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SwiftCart - Lightning Fast Delivery</title>

    <link rel="stylesheet" href="/ecommerce/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #f8fafc;
    font-family: 'Inter', sans-serif;
    overflow-x: hidden;
}

/* Premium Navbar */
.premium-navbar {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(12px);
    padding: 0.8rem 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.03);
}

.navbar-scrolled {
    background: rgba(255, 255, 255, 0.98);
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
    padding: 0.5rem 0;
}

/* Premium Logo - SwiftCart Styling */
.premium-logo {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: transform 0.3s ease;
}

.premium-logo:hover {
    transform: scale(1.02);
}

.logo-icon {
    width: 38px;
    height: 38px;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: white;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
}

.logo-text {
    font-size: 1.6rem;
    font-weight: 800;
    background: linear-gradient(135deg, #6366f1, #f43f5e);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    letter-spacing: -0.5px;
}

.logo-tagline {
    font-size: 0.65rem;
    font-weight: 500;
    background: linear-gradient(135deg, #6366f1, #f43f5e);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    letter-spacing: 0.3px;
    margin-top: -2px;
}

/* Premium Search Bar */
.premium-search {
    position: relative;
    width: 100%;
    max-width: 450px;
}

.search-container {
    position: relative;
    width: 100%;
}

.search-input {
    width: 100%;
    padding: 0.85rem 1.2rem 0.85rem 3rem;
    border: 2px solid #e2e8f0;
    border-radius: 2rem;
    font-size: 0.9rem;
    font-weight: 500;
    color: #1e293b;
    background: #f8fafc;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-family: 'Inter', sans-serif;
}

.search-input:focus {
    outline: none;
    border-color: #6366f1;
    background: white;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    transform: translateY(-1px);
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 1.1rem;
    pointer-events: none;
}

.search-button {
    position: absolute;
    right: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    border: none;
    border-radius: 2rem;
    padding: 0.5rem 1.2rem;
    color: white;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.3s;
}

.search-button:hover {
    transform: translateY(-50%) scale(1.02);
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.4);
}

/* User Actions */
.user-actions {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

/* Cart Button Premium */
.cart-btn {
    position: relative;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 2rem;
    padding: 0.6rem 1.2rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    text-decoration: none;
    transition: all 0.3s;
    color: #1e293b;
    font-weight: 600;
}

.cart-btn:hover {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    border-color: transparent;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3);
}

.cart-btn i {
    font-size: 1.1rem;
}

.cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: linear-gradient(135deg, #f43f5e, #e11d48);
    color: white;
    border-radius: 30px;
    font-size: 0.7rem;
    font-weight: 800;
    padding: 3px 8px;
    min-width: 22px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(244, 63, 94, 0.4);
}

/* User Greeting Premium */
.user-greeting {
    background: linear-gradient(135deg, #eef2ff, #e0e7ff);
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-weight: 600;
    font-size: 0.85rem;
    color: #4f46e5;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.user-greeting i {
    color: #6366f1;
    font-size: 1rem;
}

/* Dropdown Button Premium */
.dropdown-btn {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 2rem;
    padding: 0.6rem 1.2rem;
    font-weight: 600;
    transition: all 0.3s;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.dropdown-btn:hover {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    border-color: transparent;
    color: white;
    transform: translateY(-2px);
}

.dropdown-btn i {
    font-size: 1rem;
}

.dropdown-menu-custom {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    padding: 0.5rem;
    margin-top: 0.8rem;
    min-width: 220px;
}

.dropdown-item-custom {
    padding: 0.7rem 1rem;
    border-radius: 0.75rem;
    transition: all 0.2s;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.dropdown-item-custom:hover {
    background: linear-gradient(135deg, #eef2ff, #e0e7ff);
    transform: translateX(5px);
}

.dropdown-item-custom i {
    width: 20px;
    font-size: 1rem;
}

.dropdown-divider-custom {
    margin: 0.5rem 0;
    border-top: 1px solid #e2e8f0;
}

/* Auth Buttons */
.auth-buttons {
    display: flex;
    gap: 0.8rem;
}

.btn-login {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    border: none;
    border-radius: 2rem;
    padding: 0.6rem 1.5rem;
    font-weight: 600;
    color: white;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
    color: white;
}

.btn-signup {
    background: transparent;
    border: 2px solid #e2e8f0;
    border-radius: 2rem;
    padding: 0.6rem 1.5rem;
    font-weight: 600;
    color: #1e293b;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-signup:hover {
    border-color: #6366f1;
    color: #6366f1;
    transform: translateY(-2px);
}

/* Mobile Menu Toggle */
.mobile-toggle {
    display: none;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 1rem;
    padding: 0.5rem 1rem;
    color: #1e293b;
    font-weight: 600;
}

/* Responsive */
@media (max-width: 992px) {
    .premium-search {
        order: 3;
        margin-top: 1rem;
        max-width: 100%;
    }
    
    .mobile-toggle {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .user-actions {
        order: 2;
    }
    
    .premium-logo {
        order: 1;
    }
    
    .nav-content {
        flex-wrap: wrap;
    }
}

@media (max-width: 768px) {
    .auth-buttons {
        gap: 0.5rem;
    }
    
    .btn-login, .btn-signup {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }
    
    .logo-text {
        font-size: 1.3rem;
    }
    
    .logo-icon {
        width: 32px;
        height: 32px;
        font-size: 1rem;
    }
    
    .cart-btn {
        padding: 0.5rem 1rem;
    }
    
    .dropdown-btn {
        padding: 0.5rem 1rem;
    }
}
</style>

</head>

<body>

<nav class="premium-navbar" id="navbar">
<div class="container">
    <div class="d-flex align-items-center justify-content-between flex-wrap nav-content">
        
        <!-- 🔥 Premium Logo - SwiftCart -->
        <a href="/ecommerce/user/index.php" class="premium-logo">
            <div class="logo-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <div>
                <div class="logo-text">SwiftCart</div>
                <div class="logo-tagline">Lightning Fast. Premium Quality.</div>
            </div>
        </a>

        <!-- 🔍 Premium Search Bar -->
        <div class="premium-search">
            <form action="/ecommerce/user/search.php" method="GET" class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="q" class="search-input" placeholder="Search for products, brands, and more..." required>
                <button type="submit" class="search-button">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>

        <!-- 👤 User Actions -->
        <div class="user-actions">
            
            <!-- 🛒 Shopping Cart -->
            <a href="/ecommerce/user/cart.php" class="cart-btn">
                <i class="fas fa-shopping-bag"></i>
                <span>Cart</span>
                <span class="cart-count">
                <?php
                if(isset($_SESSION['user'])){
                    include("../config/db.php");
                    $uid = $_SESSION['user']['id'];
                    $count_result = $conn->query("SELECT * FROM cart WHERE user_id=$uid");
                    $count = $count_result ? $count_result->num_rows : 0;
                    echo $count;
                } else {
                    echo 0;
                }
                ?>
                </span>
            </a>

            <?php if(isset($_SESSION['user'])): ?>

                <!-- 👋 User Greeting -->
                <div class="user-greeting">
                    <i class="fas fa-user-circle"></i>
                    <span>Hi, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
                </div>

                <!-- 📋 Account Dropdown -->
                <div class="dropdown">
                    <button class="dropdown-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-astronaut"></i>
                        <span>Account</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                        <li>
                            <a class="dropdown-item dropdown-item-custom" href="/ecommerce/user/profile.php">
                                <i class="fas fa-id-card"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item dropdown-item-custom" href="/ecommerce/user/orders.php">
                                <i class="fas fa-truck-fast"></i>
                                <span>My Orders</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item dropdown-item-custom" href="/ecommerce/user/wishlist.php">
                                <i class="fas fa-heart"></i>
                                <span>Wishlist</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item dropdown-item-custom" href="/ecommerce/user/address.php">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Addresses</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider-custom"></li>
                        <li>
                            <a class="dropdown-item dropdown-item-custom text-danger" href="/ecommerce/user/logout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>

            <?php else: ?>

                <!-- 🔐 Auth Buttons -->
                <div class="auth-buttons">
                    <a href="/ecommerce/user/login.php" class="btn-login">
                        <i class="fas fa-lock"></i>
                        <span>Login</span>
                    </a>
                    <a href="/ecommerce/user/register.php" class="btn-signup">
                        <i class="fas fa-user-plus"></i>
                        <span>Sign Up</span>
                    </a>
                </div>

            <?php endif; ?>

        </div>

        <!-- Mobile Menu Toggle (Optional for additional mobile menu) -->
        <button class="mobile-toggle d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
            <i class="fas fa-bars"></i>
            <span>Menu</span>
        </button>
    </div>
    
    <!-- Mobile Menu (Optional - can add more mobile links here) -->
    <div class="collapse mt-3" id="mobileMenu">
        <div class="bg-white rounded-3 p-3 shadow-sm">
            <div class="d-flex flex-column gap-2">
                <a href="/ecommerce/user/index.php" class="text-decoration-none text-dark py-2"><i class="fas fa-home me-2"></i>Home</a>
                <a href="/ecommerce/user/products.php" class="text-decoration-none text-dark py-2"><i class="fas fa-store me-2"></i>Products</a>
                <a href="/ecommerce/user/offers.php" class="text-decoration-none text-dark py-2"><i class="fas fa-tag me-2"></i>Offers</a>
                <a href="/ecommerce/user/contact.php" class="text-decoration-none text-dark py-2"><i class="fas fa-headset me-2"></i>Support</a>
            </div>
        </div>
    </div>
</div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
    } else {
        navbar.classList.remove('navbar-scrolled');
    }
});

// Add hover animation to dropdown
document.querySelectorAll('.dropdown').forEach(dropdown => {
    dropdown.addEventListener('show.bs.dropdown', () => {
        const btn = dropdown.querySelector('.dropdown-btn');
        if (btn) btn.style.transform = 'translateY(-2px)';
    });
    dropdown.addEventListener('hide.bs.dropdown', () => {
        const btn = dropdown.querySelector('.dropdown-btn');
        if (btn) btn.style.transform = 'translateY(0)';
    });
});

// Search input animation
const searchInput = document.querySelector('.search-input');
if (searchInput) {
    searchInput.addEventListener('focus', () => {
        searchInput.parentElement.style.transform = 'scale(1.01)';
    });
    searchInput.addEventListener('blur', () => {
        searchInput.parentElement.style.transform = 'scale(1)';
    });
}
</script>

<div class="container mt-4">
    <!-- Your page content goes here -->
</div>

</body>
</html>