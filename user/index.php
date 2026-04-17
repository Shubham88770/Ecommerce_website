<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// ✅ SESSION MANAGEMENT (Must be at the top)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ DATABASE CONNECTION
include("../config/db.php");

// ✅ FETCH FEATURED PRODUCTS (LIMIT TO FIRST 8 FOR PERFORMANCE)
$featured_query = "SELECT * FROM products ORDER BY id DESC LIMIT 8";
$featured_result = $conn->query($featured_query);

// ✅ FETCH TOP CATEGORIES (SIMULATED FOR DEMO, ADJUST BASED ON YOUR DB SCHEMA)
// In a real scenario, you'd have a categories table. We'll create a dynamic category list from products.
$category_query = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' LIMIT 6";
$category_result = $conn->query($category_query);
$categories = [];
if ($category_result && $category_result->num_rows > 0) {
    while($cat = $category_result->fetch_assoc()) {
        $categories[] = $cat['category'];
    }
}
// Fallback categories if none exist in DB
if (empty($categories)) {
    $categories = ['Electronics', 'Fashion', 'Home & Living', 'Mobiles', 'Accessories', 'Beauty'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
<title>SwiftCart | Lightning Fast Delivery</title>

<!-- Bootstrap 5.3 + Font Awesome 6 + Google Fonts (Inter & Poppins) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<!-- AOS Animation Library -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<!-- Swiper JS for Carousels -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --primary-light: #a5b4fc;
    --secondary: #f43f5e;
    --dark: #0f172a;
    --gray-dark: #1e293b;
    --gray: #64748b;
    --gray-light: #f1f5f9;
    --success: #10b981;
    --warning: #f59e0b;
    --white: #ffffff;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    --radius: 1rem;
    --radius-sm: 0.75rem;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: var(--gray-light);
    color: var(--dark);
    overflow-x: hidden;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}
::-webkit-scrollbar-track {
    background: var(--gray-light);
}
::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 10px;
}

/* Glassmorphism Navbar */
.navbar-top {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(12px);
    padding: 0.8rem 0;
    position: sticky;
    top: 0;
    z-index: 1030;
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(99, 102, 241, 0.1);
}

.logo {
    font-size: 1.8rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    text-decoration: none;
    letter-spacing: -0.5px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.logo i {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    font-size: 1.6rem;
}

.tagline {
    font-size: 0.65rem;
    font-weight: 500;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    letter-spacing: 0.5px;
}

/* Premium Search Bar */
.search-bar {
    background: var(--gray-light);
    border-radius: 2.5rem;
    padding: 0.7rem 1.2rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    border: 2px solid transparent;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.search-bar:focus-within {
    background: white;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    transform: translateY(-1px);
}

.search-bar input {
    background: transparent;
    border: none;
    outline: none;
    width: 100%;
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--dark);
}

.search-bar button {
    background: transparent;
    border: none;
    color: var(--primary);
    font-size: 1.1rem;
    transition: transform 0.2s;
}

/* User Elements */
.user-greeting {
    background: linear-gradient(135deg, #eef2ff, #e0e7ff);
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--primary-dark);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.dropdown-toggle-custom {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 2rem;
    padding: 0.5rem 1.2rem;
    font-weight: 500;
    transition: all 0.2s;
}

.dropdown-toggle-custom:hover {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.cart-wrapper {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: white;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    transition: all 0.2s;
    border: 1px solid #e2e8f0;
    text-decoration: none;
}

.cart-wrapper:hover {
    background: var(--primary);
    border-color: var(--primary);
    transform: scale(1.05);
}

.cart-wrapper:hover i {
    color: white;
}

.cart-wrapper i {
    font-size: 1.3rem;
    color: var(--gray-dark);
    transition: color 0.2s;
}

.cart-count {
    position: absolute;
    top: -6px;
    right: -8px;
    background: linear-gradient(135deg, var(--secondary), #e11d48);
    color: white;
    border-radius: 30px;
    font-size: 0.7rem;
    font-weight: 800;
    padding: 2px 7px;
    min-width: 20px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(244, 63, 94, 0.4);
}

/* Hero Section - Modern Gradient */
.hero-section {
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
    border-radius: 2rem;
    margin: 1.5rem 0 2rem;
    padding: 3.5rem 2rem;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: "";
    position: absolute;
    top: -30%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(99,102,241,0.3) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.hero-section::after {
    content: "";
    position: absolute;
    bottom: -20%;
    left: -5%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(244,63,94,0.2) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.hero-badge {
    background: rgba(99, 102, 241, 0.2);
    backdrop-filter: blur(8px);
    padding: 0.5rem 1.2rem;
    border-radius: 2rem;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 1rem;
    border: 1px solid rgba(255,255,255,0.1);
}

.btn-shop {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none;
    padding: 1rem 2rem;
    border-radius: 3rem;
    font-weight: 700;
    transition: all 0.3s;
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
    color: white;
    text-decoration: none;
    display: inline-block;
}

.btn-shop:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(99, 102, 241, 0.4);
    color: white;
}

/* Trust Cards */
.trust-card {
    background: white;
    border-radius: var(--radius);
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: var(--shadow-sm);
}

.trust-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-light);
}

.trust-card i {
    font-size: 2.5rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    margin-bottom: 1rem;
}

/* Section Header */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 2.5rem 0 1.5rem;
}

.section-header h2 {
    font-size: 1.8rem;
    font-weight: 800;
    position: relative;
    display: inline-block;
    color: var(--dark);
    font-family: 'Poppins', sans-serif;
}

.section-header h2:after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    border-radius: 3px;
}

.view-all {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.view-all:hover {
    color: var(--primary-dark);
    gap: 0.8rem;
}

/* Premium Product Cards */
.product-card {
    background: white;
    border-radius: var(--radius);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.2, 0, 0, 1);
    border: 1px solid rgba(0,0,0,0.05);
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    box-shadow: var(--shadow-sm);
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-light);
}

.product-img-wrapper {
    position: relative;
    overflow: hidden;
    height: 240px;
    background: var(--gray-light);
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.product-card:hover .product-img-wrapper img {
    transform: scale(1.08);
}

.product-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: linear-gradient(135deg, var(--secondary), #e11d48);
    color: white;
    padding: 0.25rem 0.8rem;
    border-radius: 2rem;
    font-size: 0.7rem;
    font-weight: 700;
    z-index: 2;
    box-shadow: var(--shadow-sm);
}

.product-body {
    padding: 1.2rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-name {
    font-weight: 700;
    font-size: 1rem;
    margin-bottom: 0.5rem;
    color: var(--dark);
    line-height: 1.4;
}

.product-price {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--primary);
    margin: 0.5rem 0;
}

.product-price small {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--gray);
    text-decoration: line-through;
    margin-left: 0.5rem;
}

.btn-view {
    background: var(--gray-light);
    border: none;
    border-radius: 2rem;
    padding: 0.6rem;
    font-weight: 600;
    transition: all 0.2s;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-decoration: none;
    color: var(--dark);
}

.btn-view:hover {
    background: var(--primary);
    color: white;
}

/* Category Cards Modern */
.category-card {
    background: white;
    border-radius: var(--radius);
    padding: 1.2rem;
    text-align: center;
    transition: all 0.3s;
    cursor: pointer;
    border: 1px solid rgba(0,0,0,0.05);
    text-decoration: none;
    display: block;
    box-shadow: var(--shadow-sm);
}

.category-card:hover {
    transform: translateY(-6px);
    background: linear-gradient(135deg, #fff, #faf5ff);
    border-color: var(--primary-light);
    box-shadow: var(--shadow-md);
}

.category-card i {
    font-size: 2.2rem;
    color: var(--primary);
    margin-bottom: 0.8rem;
}

.category-card span {
    font-weight: 600;
    font-size: 0.9rem;
    display: block;
    color: var(--dark);
}

/* Offer Banner Premium */
.offer-banner {
    background: linear-gradient(120deg, #fef9e8, #fff4e6);
    border-radius: var(--radius);
    padding: 2rem;
    text-align: center;
    border: 1px solid #fed7aa;
    margin: 2rem 0;
    position: relative;
    overflow: hidden;
}

.offer-banner::before {
    content: "⚡";
    font-size: 8rem;
    position: absolute;
    right: -20px;
    bottom: -20px;
    opacity: 0.1;
}

/* Newsletter Section */
.newsletter-section {
    background: linear-gradient(135deg, var(--dark), #2d2b5e);
    border-radius: var(--radius);
    padding: 3rem 2rem;
    margin: 2rem 0;
    color: white;
}

/* Premium Footer */
.footer-premium {
    background: var(--dark);
    margin-top: 4rem;
    padding: 3rem 0 1.5rem;
    border-radius: 2rem 2rem 0 0;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-section h1 {
        font-size: 1.8rem;
    }
    .section-header h2 {
        font-size: 1.4rem;
    }
    .product-img-wrapper {
        height: 180px;
    }
}
</style>
</head>
<body>

<!-- MODERN HEADER WITH GLASSMORPHISM -->
<div class="navbar-top">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-md-3 col-6">
                <a href="index.php" class="logo">
                    <i class="fas fa-bolt"></i> SwiftCart
                </a>
                <div class="tagline">Lightning Fast. Premium Quality.</div>
            </div>
            <div class="col-md-5 col-12">
                <form method="GET" action="search.php" class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" placeholder="Search for products, brands and more..." required>
                    <button type="submit"><i class="fas fa-arrow-right"></i></button>
                </form>
            </div>
            <div class="col-md-4 col-6">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <?php if(isset($_SESSION['user'])): ?>
                        <div class="user-greeting">
                            <i class="fas fa-user-circle"></i> Hi, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>
                        </div>
                        <div class="dropdown">
                            <button class="btn dropdown-toggle-custom dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> Account
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-id-card me-2"></i>My Profile</a></li>
                                <li><a class="dropdown-item" href="orders.php"><i class="fas fa-truck me-2"></i>My Orders</a></li>
                                <li><a class="dropdown-item" href="wishlist.php"><i class="fas fa-heart me-2"></i>Wishlist</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                        <a href="cart.php" class="cart-wrapper">
                            <i class="fas fa-shopping-bag"></i>
                            <span class="cart-count">
                                <?php
                                if(isset($_SESSION['user'])){
                                    $uid = $_SESSION['user']['id'];
                                    $count = $conn->query("SELECT * FROM cart WHERE user_id=$uid")->num_rows;
                                    echo $count;
                                } else {
                                    echo 0;
                                }
                                ?>
                            </span>
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary rounded-pill px-4" style="background: var(--primary); border: none;">
                            <i class="fas fa-lock me-1"></i> Login
                        </a>
                        <a href="register.php" class="btn btn-outline-secondary rounded-pill">
                            Sign Up
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- HERO SECTION WITH ANIMATION -->
<div class="container">
    <div class="hero-section text-white" data-aos="fade-up" data-aos-duration="1000">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-badge">
                    <i class="fas fa-bolt me-1"></i> Flash Sale Live
                </div>
                <h1 class="mb-3">Upgrade Your <br>Style with <span style="color: var(--primary-light);">SwiftCart</span></h1>
                <p class="mb-4 opacity-75">Free delivery on orders above ₹999 | 100% Authentic | 7-Day Returns</p>
                <a href="#" class="btn-shop">
                    EXPLORE NOW <i class="fas fa-arrow-right ms-2"></i>
                </a>
                <div class="mt-4 d-flex gap-3 flex-wrap">
                    <span><i class="fas fa-tag text-warning me-1"></i> Up to 70% OFF</span>
                    <span><i class="fas fa-gem text-warning me-1"></i> Premium Quality</span>
                    <span><i class="fas fa-truck-fast text-warning me-1"></i> Same Day Dispatch</span>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-center">
                <i class="fas fa-shopping-cart fa-5x opacity-25"></i>
                <i class="fas fa-box-open fa-4x opacity-25 ms-3"></i>
            </div>
        </div>
    </div>

    <!-- TRUST BADGES WITH ICONS -->
    <div class="row g-3 mb-5" data-aos="fade-up" data-aos-delay="200">
        <div class="col-md-4">
            <div class="trust-card">
                <i class="fas fa-truck-fast"></i>
                <h6>Free Express Delivery</h6>
                <p>On orders above ₹999</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="trust-card">
                <i class="fas fa-shield-alt"></i>
                <h6>Secure Payments</h6>
                <p>SSL Encrypted Transactions</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="trust-card">
                <i class="fas fa-headset"></i>
                <h6>24/7 Support</h6>
                <p>Dedicated customer care</p>
            </div>
        </div>
    </div>

    <!-- SHOP BY CATEGORY - DYNAMIC -->
    <div class="section-header" data-aos="fade-right">
        <h2><i class="fas fa-th-large me-2" style="color: var(--primary);"></i> Shop by Category</h2>
        <a href="#" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
    </div>
    
    <div class="row g-3 mb-5">
        <?php foreach($categories as $category): ?>
        <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="100">
            <a href="category.php?cat=<?php echo urlencode($category); ?>" class="category-card">
                <i class="fas fa-tag"></i>
                <span><?php echo htmlspecialchars($category); ?></span>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- FEATURED PRODUCTS SECTION -->
    <div class="section-header" data-aos="fade-right">
        <h2><i class="fas fa-fire me-2" style="color: var(--primary);"></i> Trending Now</h2>
        <a href="products.php" class="view-all">Browse All <i class="fas fa-arrow-right"></i></a>
    </div>

    <div class="row g-4 mb-5">
        <?php if(isset($featured_result) && $featured_result && $featured_result->num_rows > 0): 
            while($row = $featured_result->fetch_assoc()):
                $product_name = htmlspecialchars($row['name']);
                $product_price = number_format((float)$row['price'], 2);
                $product_id = (int)$row['id'];
                $product_image = htmlspecialchars($row['image']);
                $img_src = (!empty($product_image)) ? "../assets/images/".$product_image : "https://placehold.co/400x300?text=SwiftCart";
        ?>
            <div class="col-md-6 col-lg-3 d-flex" data-aos="fade-up" data-aos-delay="<?php echo rand(100, 400); ?>">
                <div class="product-card w-100">
                    <div class="product-img-wrapper">
                        <img src="<?php echo $img_src; ?>" alt="<?php echo $product_name; ?>" onerror="this.src='https://placehold.co/400x300?text=SwiftCart+Product'">
                        <div class="product-badge">BESTSELLER</div>
                    </div>
                    <div class="product-body">
                        <h5 class="product-name"><?php echo $product_name; ?></h5>
                        <div class="product-price">
                            ₹<?php echo $product_price; ?>
                            <small>₹<?php echo round($product_price * 1.25, 2); ?></small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-warning">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                            </span>
                            <span class="small text-muted">4.5 (128)</span>
                        </div>
                        <a href="product.php?id=<?php echo $product_id; ?>" class="btn-view">
                            <i class="fas fa-eye"></i> Quick View
                        </a>
                    </div>
                </div>
            </div>
        <?php 
            endwhile;
        else: ?>
            <div class="col-12 text-center py-5">
                <div class="bg-white rounded-4 p-5 shadow-sm">
                    <i class="fas fa-box-open fa-4x text-primary mb-3"></i>
                    <h4>No Products Available</h4>
                    <p class="text-muted">Check back soon for amazing deals!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- OFFER BANNER -->
    <div class="offer-banner" data-aos="flip-up">
        <i class="fas fa-gift fa-2x text-warning mb-2"></i>
        <h3 class="fw-bold mb-2">Limited Time Offer!</h3>
        <p class="mb-3">Get extra 15% off on your first order | Use code: <span class="fw-bold text-dark bg-warning px-2 py-1 rounded">Sell15</span></p>
        <a href="#" class="btn btn-dark rounded-pill px-4">Grab Deal <i class="fas fa-arrow-right ms-2"></i></a>
    </div>

    <!-- NEWSLETTER SECTION -->
    <div class="newsletter-section" data-aos="fade-up">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h3 class="fw-bold mb-2">Subscribe & Get <span style="color: var(--primary-light);">20% OFF</span></h3>
                <p>Be the first to know about exclusive offers, new arrivals and more.</p>
            </div>
            <div class="col-lg-6">
                <div class="input-group">
                    <input type="email" class="form-control form-control-lg bg-white bg-opacity-10 border-0 text-white" placeholder="Your email address" style="background: rgba(255,255,255,0.1) !important;">
                    <button class="btn btn-warning px-4" type="button">Subscribe</button>
                </div>
                <small class="opacity-50 mt-2 d-block">No spam, unsubscribe anytime.</small>
            </div>
        </div>
    </div>
</div>

<!-- PREMIUM FOOTER -->
<div class="footer-premium">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5><i class="fas fa-bolt text-primary"></i> SwiftCart</h5>
                <p class="text-white-50 small">Redefining online shopping with lightning-fast delivery, premium products, and exceptional customer experience.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-white-50 hover-text-primary"><i class="fab fa-facebook-f fa-lg"></i></a>
                    <a href="#" class="text-white-50"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-white-50"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-white-50"><i class="fab fa-linkedin-in fa-lg"></i></a>
                </div>
            </div>
            <div class="col-md-2">
                <h6 class="text-white">Shop</h6>
                <ul class="footer-links list-unstyled">
                    <li><a href="#" class="text-white-50 text-decoration-none small">New Arrivals</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none small">Best Sellers</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none small">Offers Zone</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none small">Gift Cards</a></li>
                </ul>
            </div>
            <div class="col-md-2">
                <h6 class="text-white">Support</h6>
                <ul class="footer-links list-unstyled">
                    <li><a href="#" class="text-white-50 text-decoration-none small">Help Center</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none small">Track Order</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none small">Returns Policy</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none small">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-white">Download App</h6>
                <div class="d-flex gap-2 mt-2">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-pill"><i class="fab fa-apple"></i> App Store</a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-pill"><i class="fab fa-google-play"></i> Google Play</a>
                </div>
                <p class="small text-white-50 mt-3">We accept</p>
                <div class="d-flex gap-2">
                    <i class="fab fa-cc-visa fa-2x text-white-50"></i>
                    <i class="fab fa-cc-mastercard fa-2x text-white-50"></i>
                    <i class="fab fa-cc-amex fa-2x text-white-50"></i>
                    <i class="fab fa-cc-paypal fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
        <hr class="bg-secondary mt-4">
        <div class="text-center small text-white-50">
            <p class="mb-0">© 2026 SwiftCart — Lightning Fast Delivery. All rights reserved.</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });
    
    // Smooth hover animation for product cards
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transition = 'all 0.3s cubic-bezier(0.2, 0, 0, 1)';
        });
    });
    
    // Navbar scroll effect
    window.addEventListener('scroll', () => {
        const navbar = document.querySelector('.navbar-top');
        if (window.scrollY > 50) {
            navbar.style.background = 'rgba(255, 255, 255, 0.98)';
            navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.05)';
        } else {
            navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            navbar.style.boxShadow = 'none';
        }
    });
</script>
</body>
</html>