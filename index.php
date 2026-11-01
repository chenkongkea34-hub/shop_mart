<?php
session_start();
require_once __DIR__ . '/config/database.php';

$loginError = '';
$products = [];
$productLoadError = '';

if (isset($_GET['logout'])) {
  $_SESSION = [];
  session_destroy();
  header('Location: index.php');
  exit;
}

if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 191 || $password === '') {
    $loginError = 'Enter a valid email address and password.';
  } else {
    try {
      $database = ShopMate_db();
      $statement = $database->prepare('SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1');
      $statement->execute([$email]);
      $user = $statement->fetch();

      if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
          'id' => (int) $user['id'],
          'name' => $user['name'],
          'email' => $user['email'],
        ];
        header('Location: index.php');
        exit;
      }

      $loginError = 'The email or password is incorrect.';
    } catch (PDOException $exception) {
      error_log('ShopMate login database error: ' . $exception->getMessage());
      $loginError = 'Unable to connect to MySQL or process the login.';
    }
  }
}

try {
  $productRows = ShopMate_db()->query(
    'SELECT id, name, price, description, image, category FROM products ORDER BY id DESC'
  )->fetchAll();

  foreach ($productRows as $productRow) {
    $products[] = [
      'id' => (int) $productRow['id'],
      'name' => (string) $productRow['name'],
      'category' => (string) $productRow['category'],
      'price' => (float) $productRow['price'],
      'description' => (string) $productRow['description'],
      'image' => 'uploads/' . rawurlencode((string) $productRow['image']),
      'badge' => 'Available',
    ];
  }
} catch (PDOException $exception) {
  error_log('ShopMate homepage product list error: ' . $exception->getMessage());
  $productLoadError = 'Products are temporarily unavailable. Please try again later.';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopMate E-Commerce App</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
  <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
  <script src="https://unpkg.com/framer-motion@10.16.5/dist/framer-motion.js"></script>
  <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

    body {
      font-family: 'Inter', sans-serif;
    }

    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    ::-webkit-scrollbar-track {
      background: #f5f5f4;
    }

    ::-webkit-scrollbar-thumb {
      background: #d6d3d1;
      border-radius: 9999px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: #a8a29e;
    }
  </style>
</head>

<body class="bg-stone-50 text-stone-800 antialiased selection:bg-lime-200 selection:text-lime-900">
  <div id="root"></div>
  <script type="text/babel">
    const { useState, useEffect } = React;
        const { motion, AnimatePresence } = window.Motion;

      const PRODUCTS = <?php echo json_encode($products, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
      const PRODUCT_LOAD_ERROR = <?php echo json_encode($productLoadError, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        const CATEGORIES = [
          { id: 'Fruits & Vegetables', name: 'Fruits & Vegetables', image: 'https://i.pinimg.com/736x/59/ff/1f/59ff1fcc1ae8d2ae9b9b461d38074e91.jpg' },
          { id: 'Dairy & Eggs', name: 'Dairy & Eggs', image: 'https://i.pinimg.com/736x/04/11/66/0411661c5e9656ff884dbd19fe2e1206.jpg' },
          { id: 'snacks', name: 'Snacks', image: 'https://i.pinimg.com/1200x/5d/78/73/5d7873cf31f38554be270c5517cf78b9.jpg' },
          { id: 'Meat & Seafood', name: 'Meat & Seafood', image: 'https://i.pinimg.com/736x/4e/56/2b/4e562b2fe00aa1699c65056842f2fba8.jpg' },
          { id: 'bakery', name: 'Bakery', image: 'https://i.pinimg.com/736x/7b/42/32/7b4232f50f1f728c361f2bcf3eae3416.jpg' },
          { id: 'beverages', name: 'beverages', image: 'https://i.pinimg.com/1200x/22/c6/24/22c6245ca9e09b627eb1cc1edc54c853.jpg' },
        ];
        const TESTIMONIALS = [
          {
            id: 1,
            comment: "Amazing products and fast delivery! ShopMate is my go-to store for all my needs.",
            author: "John D.",
            role: "Verified Buyer",
            avatar: "https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100&auto=format&fit=crop&q=80"
          },
          {
            id: 2,
            comment: "Great quality at affordable prices. The customer support is also very responsive.",
            author: "Sarah T.",
            role: "Interior Designer",
            avatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&auto=format&fit=crop&q=80"
          },
          {
            id: 3,
            comment: "Very happy with my purchase. Highly recommend ShopMate to everyone!",
            author: "Michael P.",
            role: "Tech Enthusiast",
            avatar: "https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?w=100&auto=format&fit=crop&q=80"
          }
        ];
        function App() {
          const [activeTab, setActiveTab] = useState('home');
          const [cart, setCart] = useState([]);
          const [wishlist, setWishlist] = useState([]);
          const [searchQuery, setSearchQuery] = useState('');
          const [isCartOpen, setIsCartOpen] = useState(false);
          const [isCheckoutOpen, setIsCheckoutOpen] = useState(false);
          const [isSubmittingOrder, setIsSubmittingOrder] = useState(false);
          const [checkoutError, setCheckoutError] = useState('');
          const [toastMessage, setToastMessage] = useState(null);
          const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
          const [isLoginOpen, setIsLoginOpen] = useState(<?php echo $loginError !== '' ? 'true' : 'false'; ?>);
          const currentUser = <?php echo json_encode($_SESSION['user'] ?? null, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
          const csrfToken = <?php echo json_encode($_SESSION['csrf_token'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

          useEffect(() => {
            if (window.lucide) {
              window.lucide.createIcons();
            }
          }, [activeTab, isCartOpen, mobileMenuOpen]);

          const showToast = (msg) => {
            setToastMessage(msg);
            setTimeout(() => setToastMessage(null), 3000);
          };

          const addToCart = (product) => {
          setCart(prev => {
            const productId = product.id || product.name; 
            const existing = prev.find(item => (item.id || item.name) === productId);
        
            if (existing) {
              return prev.map(item => 
                (item.id || item.name) === productId 
                  ? { ...item, qty: item.qty + 1 } 
                  : item
              );
            }
            return [...prev, { ...product, id: productId, qty: 1 }];
          });
          showToast(`Added ${product.name} to cart!`);
        };

          const updateCartQty = (id, delta) => {
            setCart(prev => prev.map(item => {
              if (item.id === id) {
                const newQty = item.qty + delta;
                return newQty > 0 ? { ...item, qty: newQty } : null;
              }
              return item;
            }).filter(Boolean));
          };

          const removeFromCart = (id) => {
            setCart(prev => prev.filter(item => item.id !== id));
            showToast("Item removed from cart.");
          };

          const toggleWishlist = (productId) => {
            setWishlist(prev => 
              prev.includes(productId) ? prev.filter(id => id !== productId) : [...prev, productId]
            );
          };

          const totalCartItems = cart.reduce((acc, item) => acc + item.qty, 0);
          const cartSubtotal = cart.reduce((acc, item) => acc + item.price * item.qty, 0);
          const deliveryFee = cart.length > 0 ? 1.50 : 0.00; 
          const cartTotal = cartSubtotal + deliveryFee;
          const visibleProducts = PRODUCTS
            .filter(product => product.name.toLowerCase().includes(searchQuery.toLowerCase())
              || product.category.toLowerCase().includes(searchQuery.toLowerCase()));
          

          return (
            <div className="min-h-screen flex flex-col bg-stone-50 text-stone-800">
              
              {/* Toast Notification */}
              <AnimatePresence>
                {toastMessage && (
                  <motion.div 
                    initial={{ opacity: 0, y: -50, x: '-50%' }}
                    animate={{ opacity: 1, y: 20, x: '-50%' }}
                    exit={{ opacity: 0, y: -20, x: '-50%' }}
                    className="fixed top-0 left-1/2 z-50 bg-stone-900 text-white px-6 py-3 rounded-full shadow-2xl flex items-center gap-3 text-sm font-medium border border-stone-700"
                  >
                    <i data-lucide="sparkles" className="w-4 h-4 text-lime-400"></i>
                    <span>{toastMessage}</span>
                  </motion.div>
                )}
              </AnimatePresence>

              {/* Login modal */}
              <AnimatePresence>
                {isLoginOpen && !currentUser && (
                  <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <motion.div
                      initial={{ opacity: 0 }}
                      animate={{ opacity: 1 }}
                      exit={{ opacity: 0 }}
                      onClick={() => setIsLoginOpen(false)}
                      className="absolute inset-0 bg-stone-950/60 backdrop-blur-sm"
                    />
                    <motion.div
                      initial={{ opacity: 0, y: 20, scale: 0.96 }}
                      animate={{ opacity: 1, y: 0, scale: 1 }}
                      exit={{ opacity: 0, y: 20, scale: 0.96 }}
                      className="relative w-full max-w-md rounded-3xl bg-white p-8 shadow-2xl"
                    >
                      <button onClick={() => setIsLoginOpen(false)} className="absolute right-5 top-5 rounded-full p-2 text-stone-400 hover:bg-stone-100 hover:text-stone-700" aria-label="Close login form">
                        <i data-lucide="x" className="h-5 w-5"></i>
                      </button>
                      <div className="mb-7">
                        <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-lime-100 text-lime-800">
                          <i data-lucide="lock-keyhole" className="h-6 w-6"></i>
                        </div>
                        <h2 className="text-2xl font-black text-stone-900">Welcome back</h2>
                        <p className="mt-1 text-sm text-stone-500">Sign in to continue shopping with ShopMate.</p>
                      </div>
                      <?php if ($loginError !== ''): ?>
                        <div className="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert">
                          <?php echo htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                      <?php endif; ?>
                      <form method="post" className="space-y-4">
                        <div>
                          <label for="login-email" className="mb-1.5 block text-sm font-semibold text-stone-700">Email address</label>
                          <input id="login-email" name="email" type="email" autocomplete="email" required className="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-lime-600 focus:ring-2 focus:ring-lime-100" placeholder="you@example.com" />
                        </div>
                        <div>
                          <label for="login-password" className="mb-1.5 block text-sm font-semibold text-stone-700">Password</label>
                          <input id="login-password" name="password" type="password" autocomplete="current-password" required className="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-lime-600 focus:ring-2 focus:ring-lime-100" placeholder="Enter your password" />
                        </div>
                        <button type="submit" name="login" value="1" className="w-full rounded-xl bg-lime-700 py-3.5 text-sm font-bold text-white shadow-lg shadow-lime-700/20 transition hover:bg-lime-800">Sign in</button>
                      </form>
                    </motion.div>
                  </div>
                )}
              </AnimatePresence>

              {/* Header */}
              <header className="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-stone-200 shadow-xs">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                  <div className="flex items-center gap-8">
                    <motion.div 
                      whileHover={{ scale: 1.05 }}
                      onClick={() => setActiveTab('home')}
                      className="flex items-center gap-2 cursor-pointer"
                    >
                      <div className="bg-lime-700 text-white p-2 rounded-xl shadow-md">
                        <i data-lucide="shopping-bag" className="w-6 h-6"></i>
                      </div>
                      <span className="text-2xl font-black tracking-tight text-stone-900">ShopMart</span>
                    </motion.div>

                    <nav className="hidden md:flex items-center gap-6 font-medium text-sm text-stone-600">
                      {['home', 'shop', 'categories', 'testimonials'].map((tab) => (
                        <button
                          key={tab}
                          onClick={() => setActiveTab(tab)}
                          className={`capitalize transition-colors relative py-2 ${activeTab === tab ? 'text-lime-700 font-semibold' : 'hover:text-stone-900'}`}
                        >
                          {tab === 'testimonials' ? 'Reviews' : tab } 
                          {activeTab === tab && (
                            <motion.div layoutId="underline" className="absolute bottom-0 left-0 right-0 h-0.5 bg-lime-700 rounded-full" />
                          )}
                        </button>
                      ))}
                    </nav>
                  </div>

                  <div className="flex items-center gap-4">
                    <div className="hidden lg:flex items-center bg-stone-100 rounded-full px-4 py-2 w-64 border border-stone-200 focus-within:border-lime-600 transition-colors">
                      <i data-lucide="search" className="w-4 h-4 text-stone-400 mr-2"></i>
                      <input 
                        type="text" 
                        placeholder="Search products..." 
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        className="bg-transparent border-none outline-none text-xs w-full text-stone-800 placeholder-stone-400"
                      />
                    </div>

                   <a href="order_history.php">
                    <motion.div 
                        whileHover={{ scale: 1.1 }}
                        whileTap={{ scale: 0.95 }}
                        className="relative p-2.5 rounded-full hover:bg-stone-100 text-stone-700 transition-colors inline-flex items-center justify-center cursor-pointer"
                    >
                        <i data-lucide="heart" className={`w-5 h-5 ${wishlist.length > 0 ? 'fill-rose-500 text-rose-500' : ''}`}></i>
                        {wishlist.length > 0 && (
                            <span className="absolute top-1 right-1 bg-rose-500 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">
                                {wishlist.length}
                            </span>
                        )}
                    </motion.div>
                  </a>

                    <motion.button 
                      whileHover={{ scale: 1.1 }}
                      whileTap={{ scale: 0.95 }}
                      onClick={() => setIsCartOpen(true)}
                      className="relative bg-lime-700 hover:bg-lime-800 text-white p-3 rounded-full shadow-lg shadow-lime-700/20 transition-all flex items-center gap-2 px-4"
                    >
                      <i data-lucide="shopping-bag" className="w-4 h-4"></i>
                      <span className="text-xs font-bold">{totalCartItems}</span>
                    </motion.button>

                    {currentUser ? (
                      <div className="hidden items-center gap-3 sm:flex">
                        <span className="max-w-28 truncate text-xs font-semibold text-stone-700">Hi, {currentUser.name}</span>
                        <a href="?logout=1" className="text-xs font-semibold text-stone-500 transition hover:text-rose-600">Log out</a>
                      </div>
                    ) : (
                      <button onClick={() => { window.location.href = 'user_login.php'; }} className="hidden items-center gap-2 rounded-full border border-stone-200 px-4 py-2.5 text-xs font-bold text-stone-700 transition hover:border-lime-600 hover:text-lime-700 sm:flex">
                        <i data-lucide="user-round" className="h-4 w-4"></i>
                        Sign in
                      </button>
                    )}

                    <button 
                      onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                      className="md:hidden p-2 text-stone-700 hover:bg-stone-100 rounded-lg"
                    >
                      <i data-lucide={mobileMenuOpen ? "x" : "menu"} className="w-6 h-6"></i>
                    </button>
                  </div>
                </div>

                <AnimatePresence>
                  {mobileMenuOpen && (
                    <motion.div 
                      initial={{ opacity: 0, height: 0 }}
                      animate={{ opacity: 1, height: 'auto' }}
                      exit={{ opacity: 0, height: 0 }}
                      className="md:hidden border-t border-stone-200 bg-white px-6 py-4 space-y-3"
                    >
                      {['home', 'shop', 'categories', 'testimonials'].map((tab) => (
                        <button
                          key={tab}
                          onClick={() => { setActiveTab(tab); setMobileMenuOpen(false); }}
                          className={`block w-full text-left py-2 capitalize font-medium ${activeTab === tab ? 'text-lime-700 font-bold' : 'text-stone-600'}`}
                        >
                          {tab === 'testimonials' ? 'Reviews' : tab}
                        </button>
                      ))}
                    </motion.div>
                  )}
                </AnimatePresence>
              </header>

              {/* Main Content Sections */}
              <main className="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
                <AnimatePresence mode="wait">
                  {activeTab === 'home' && (
                    <motion.div
                      key="home"
                      initial={{ opacity: 0, y: 20 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0, y: -20 }}
                      transition={{ duration: 0.3 }}
                      className="space-y-16"
                    >
                      {/* Hero Section */}
                      <div className="bg-stone-100/80 border border-stone-200/60 rounded-3xl p-8 md:p-12 lg:p-16 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative overflow-hidden shadow-sm">
                        <div className="absolute top-0 right-0 w-96 h-96 bg-lime-200/30 rounded-full blur-3xl pointer-events-none -mr-20 -mt-20"></div>
                        
                        <div className="lg:col-span-7 space-y-6 z-10">
                          <div className="inline-flex items-center gap-2 bg-lime-100 text-lime-800 px-3.5 py-1.5 rounded-full text-xs font-semibold tracking-wide uppercase">
                            <i data-lucide="sparkles" className="w-3.5 h-3.5"></i> NEW ARRIVALS
                          </div>
                          <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black text-stone-900 tracking-tight leading-[1.1]">
                            Freshness The Best <span className="text-lime-700">Delivered</span> Daily
                          </h1>
                          <p className="text-stone-600 text-base sm:text-lg max-w-xl leading-relaxed">
                            Farm_fress groceries for a healtier, happieer you!
                          </p>
                          
                          <div className="flex flex-wrap items-center gap-4 pt-2">
                            <motion.button 
                              whileHover={{ scale: 1.05 }}
                              whileTap={{ scale: 0.95 }}
                              onClick={() => setActiveTab('shop')}
                              className="bg-lime-700 hover:bg-lime-800 text-white font-medium px-8 py-4 rounded-full shadow-lg shadow-lime-700/25 flex items-center gap-3 transition-all"
                            >
                              Shop Now <i data-lucide="arrow-right" className="w-4 h-4"></i>
                            </motion.button>
                            <motion.button 
                              whileHover={{ scale: 1.05 }}
                              whileTap={{ scale: 0.95 }}
                              onClick={() => setActiveTab('categories')}
                              className="bg-white hover:bg-stone-50 text-stone-800 border border-stone-300 font-medium px-8 py-4 rounded-full shadow-sm transition-all"
                            >
                              Explore Deals
                            </motion.button>
                          </div>

                          <div className="flex items-center gap-4 pt-4 border-t border-stone-200/80">
                            <div className="flex -space-x-2 overflow-hidden">
                              <img className="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80" alt="User" />
                              <img className="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80" alt="User" />
                              <img className="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover" src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&auto=format&fit=crop&q=80" alt="User" />
                            </div>
                            <div>
                              <div className="flex items-center text-amber-500">
                                {[...Array(5)].map((_, i) => <i key={i} data-lucide="star" className="w-3.5 h-3.5 fill-amber-500"></i>)}
                              </div>
                              <p className="text-xs font-medium text-stone-600 mt-0.5">Trusted by 10,000+ Happy Customers</p>
                            </div>
                          </div>
                        </div>

                        <div className="lg:col-span-5 relative flex justify-center z-10">
                          <motion.div 
                            initial={{ scale: 0.9, opacity: 0 }}
                            animate={{ scale: 1, opacity: 1 }}
                            transition={{ duration: 0.5, delay: 0.2 }}
                            className="relative w-full max-w-md aspect-square rounded-3xl p-6 flex items-center justify-center"
                          >
                            <div className="absolute inset-0 bg-gradient-to-tr from-lime-200/40 to-stone-200/50 rounded-3xl transform rotate-3"></div>
                            <img 
                              src="https://i.pinimg.com/1200x/98/ae/e1/98aee19e3cdd6fbc07fd31cef9af95d3.jpg" 
                              alt="Hero Product" 
                              className="relative z-10 w-full h-80 object-cover rounded-2xl shadow-2xl transform -rotate-2 hover:rotate-0 transition-transform duration-500"
                            />
                          </motion.div>
                        </div>
                      </div>

                      {/* Feature Badges */}
                      <div className="grid grid-cols-2 md:grid-cols-4 gap-4 bg-white p-6 rounded-2xl border border-stone-200 shadow-xs">
                        {[
                          { icon: 'truck', title: 'Free Shipping', desc: 'On orders over $50' },
                          { icon: 'shield-check', title: 'Secure Payment', desc: '100% secure payment' },
                          { icon: 'refresh-cw', title: 'Easy Returns', desc: '30 days return policy' },
                          { icon: 'headphones', title: '24/7 Support', desc: 'Dedicated support' },
                        ].map((item, idx) => (
                          <div key={idx} className="flex items-center gap-4 p-3 rounded-xl hover:bg-stone-50 transition-colors">
                            <div className="bg-lime-100 text-lime-800 p-3 rounded-xl">
                              <i data-lucide={item.icon} className="w-6 h-6"></i>
                            </div>
                            <div>
                              <h4 className="font-bold text-sm text-stone-900">{item.title}</h4>
                              <p className="text-xs text-stone-500">{item.desc}</p>
                            </div>
                          </div>
                        ))}
                      </div>

                      {/* Categories */}
                      <div className="space-y-6">
                        <div className="flex items-center justify-between">
                          <h2 className="text-2xl font-black text-stone-900 tracking-tight">Shop by Categories</h2>
                          <button 
                            onClick={() => setActiveTab('categories')}
                            className="text-sm font-semibold text-lime-700 hover:text-lime-800 flex items-center gap-1 group"
                          >
                            View All Categories <i data-lucide="chevron-right" className="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                          </button>
                        </div>

                        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6">
                          {CATEGORIES.map((cat) => (
                            <motion.div
                              key={cat.id}
                              whileHover={{ y: -8 }}
                              whileTap={{ scale: 0.95 }}
                              onClick={() => setActiveTab('shop')}
                              className="bg-white p-4 rounded-2xl border border-stone-200 shadow-xs text-center cursor-pointer group flex flex-col items-center"
                            >
                              <div className="w-24 h-24 rounded-full overflow-hidden mb-3 border-2 border-stone-100 group-hover:border-lime-600 transition-colors shadow-inner">
                                <img src={cat.image} alt={cat.name} className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                              </div>
                              <h3 className="font-bold text-sm text-stone-800 group-hover:text-lime-700 transition-colors">{cat.name}</h3>
                            </motion.div>
                          ))}
                        </div>
                      </div>

                      {/* Best Selling Products */}
                      <div className="space-y-6">
                        <div className="flex items-center justify-between">
                          <h2 className="text-2xl font-black text-stone-900 tracking-tight">Latest Products</h2>
                          <button 
                            onClick={() => setActiveTab('shop')}
                            className="text-sm font-semibold text-lime-700 hover:text-lime-800 flex items-center gap-1 group"
                          >
                            View All Products <i data-lucide="chevron-right" className="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                          </button>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                          {PRODUCTS.length === 0 ? (
                            <p className="col-span-full rounded-2xl border border-stone-200 bg-white p-6 text-center text-sm text-stone-500">
                              {PRODUCT_LOAD_ERROR || 'No products are available yet.'}
                            </p>
                          ) : PRODUCTS.slice(0, 5).map((product) => (
                            <motion.div
                              key={product.id}
                              whileHover={{ y: -6 }}
                              className="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group"
                            >
                              <div className="relative aspect-square overflow-hidden bg-stone-100">
                                <span className="absolute top-3 left-3 z-10 bg-stone-900/80 backdrop-blur-md text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                                  {product.badge}
                                </span>
                                <button 
                                  onClick={() => toggleWishlist(product.id)}
                                  className="absolute top-3 right-3 z-10 bg-white/80 hover:bg-white backdrop-blur-md p-2 rounded-full shadow-md text-stone-700 transition-colors"
                                >
                                  <i data-lucide="heart" className={`w-4 h-4 ${wishlist.includes(product.id) ? 'fill-rose-500 text-rose-500' : ''}`}></i>
                                </button>
                                <img 
                                  src={product.image} 
                                  alt={product.name} 
                                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                />
                              </div>

                              <div className="p-4 space-y-3 flex-1 flex flex-col justify-between">
                                <div>
                                  <div className="flex items-center gap-1 text-amber-500 text-xs mb-1">
                                    <span className="text-stone-500">{product.category}</span>
                                  </div>
                                  <h3 className="font-bold text-stone-900 text-sm line-clamp-1 group-hover:text-lime-700 transition-colors">{product.name}</h3>
                                  <p className="mt-1 text-xs text-stone-500 line-clamp-2">{product.description}</p>
                                  <div className="flex items-center gap-2 mt-1">
                                    <span className="font-black text-stone-900">${product.price.toFixed(2)}</span>
                                    {product.originalPrice && (
                                      <span className="text-xs text-stone-400 line-through">${product.originalPrice.toFixed(2)}</span>
                                    )}
                                  </div>
                                </div>

                                <motion.button 
                                  whileTap={{ scale: 0.95 }}
                                  onClick={() => addToCart(product)}
                                  className="w-full bg-lime-700 hover:bg-lime-800 text-white font-medium py-2.5 rounded-xl text-xs flex items-center justify-center gap-2 shadow-md shadow-lime-700/20 transition-all"
                                >
                                  <i data-lucide="shopping-bag" className="w-3.5 h-3.5"></i> Add to Cart
                                </motion.button>
                              </div>
                            </motion.div>
                          ))}
                        </div>
                      </div>

                      {/* Promotional Banner */}
                      <div className="relative rounded-3xl overflow-hidden bg-gradient-to-r from-stone-200 to-amber-100/60 p-8 md:p-12 border border-stone-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-8">
                        <div className="space-y-4 max-w-lg z-10">
                          <span className="bg-amber-700 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Special Offer</span>
                          <h2 className="text-3xl sm:text-4xl font-black text-stone-900 tracking-tight">Free Delivery on shopping 100$</h2>
                          <p className="text-stone-600 text-sm sm:text-base leading-relaxed">
                            Limited time offer on selected items. Hurry up and grab the best deals before they run out!
                          </p>
                          <motion.button 
                            whileHover={{ scale: 1.05 }}
                            whileTap={{ scale: 0.95 }}
                            onClick={() => setActiveTab('shop')}
                            className="bg-stone-900 hover:bg-stone-800 text-white font-medium px-8 py-3.5 rounded-full shadow-lg transition-all flex items-center gap-2"
                          >
                            Shop the Sale <i data-lucide="arrow-right" className="w-4 h-4"></i>
                          </motion.button>
                        </div>

                        <div className="relative z-10 w-full md:w-auto flex justify-center">
                          <img 
                            src="https://i.pinimg.com/1200x/6c/3c/3b/6c3c3b3e92190182f6df4a26b731417c.jpg" 
                            alt="Sale Promo" 
                            className="w-80 h-64 object-cover rounded-2xl shadow-2xl transform rotate-2 hover:rotate-0 transition-transform duration-500"
                          />
                        </div>
                      </div>

                      {/* Customer Testimonials */}
                      <div className="space-y-8 pb-12">
                        <div className="text-center space-y-2">
                          <h2 className="text-2xl sm:text-3xl font-black text-stone-950 tracking-tight">What Our Customers Say</h2>
                          <p className="text-stone-500 text-sm">Real feedback from verified shoppers worldwide</p>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                          {TESTIMONIALS.map((t) => (
                            <motion.div 
                              key={t.id} 
                              whileHover={{ y: -4 }}
                              className="bg-white p-6 rounded-2xl border border-stone-200 shadow-xs space-y-4 flex flex-col justify-between"
                            >
                              <div className="space-y-3">
                                <div className="flex items-center text-amber-500">
                                  {[...Array(5)].map((_, i) => <i key={i} data-lucide="star" className="w-4 h-4 fill-amber-500"></i>)}
                                </div>
                                <p className="text-stone-700 text-sm italic leading-relaxed">"{t.comment}"</p>
                              </div>

                              <div className="flex items-center gap-3 pt-4 border-t border-stone-100">
                                <img src={t.avatar} alt={t.author} className="w-10 h-10 rounded-full object-cover" />
                                <div>
                                  <h4 className="font-bold text-sm text-stone-900">{t.author}</h4>
                                  <p className="text-xs text-stone-500">{t.role}</p>
                                </div>
                              </div>
                            </motion.div>
                          ))}
                        </div>
                      </div>
                    </motion.div>
                  )}

                  {activeTab === 'shop' && (
                    <motion.div
                      key="shop"
                      initial={{ opacity: 0, y: 20 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0, y: -20 }}
                      transition={{ duration: 0.3 }}
                      className="space-y-8"
                    >
                    <div className="bg-white p-6 rounded-2xl border border-stone-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
                        <div>
                          <h1 className="text-3xl font-black text-stone-900">Explore Products</h1>
                          <p className="text-stone-500 text-sm mt-1">Browse all products currently available in our shop</p>
                        </div>
                    
                    </div> 

                      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        {visibleProducts.map((product) => (
                            <motion.div
                              key={product.id}
                              whileHover={{ y: -6 }}
                              className="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group"
                            >
                              <div className="relative aspect-square overflow-hidden bg-stone-100">
                                <span className="absolute top-3 left-3 z-10 bg-stone-900/85 backdrop-blur-md text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                                  {product.badge}
                                </span>
                                <button 
                                  onClick={() => toggleWishlist(product.id)}
                                  className="absolute top-3 right-3 z-10 bg-white/80 hover:bg-white backdrop-blur-md p-2 rounded-full shadow-md text-stone-700 transition-colors"
                                >
                                  <i data-lucide="heart" className={`w-4 h-4 ${wishlist.includes(product.id) ? 'fill-rose-500 text-rose-500' : ''}`}></i>
                                </button>
                                <img 
                                  src={product.image} 
                                  alt={product.name} 
                                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                />
                              </div>

                              <div className="p-4 space-y-3 flex-1 flex flex-col justify-between">
                                <div>
                                  <div className="flex items-center justify-between text-xs mb-1">
                                    <span className="text-stone-400 font-medium">{product.category}</span>
                                  </div>
                                  <h3 className="font-bold text-stone-900 text-sm line-clamp-1 group-hover:text-lime-700 transition-colors">{product.name}</h3>
                                  <p className="mt-1 text-xs text-stone-500 line-clamp-2">{product.description}</p>
                                  <div className="flex items-center gap-2 mt-1">
                                    <span className="font-black text-stone-900">${product.price.toFixed(2)}</span>
                                    {product.originalPrice && (
                                      <span className="text-xs text-stone-400 line-through">${product.originalPrice.toFixed(2)}</span>
                                    )}
                                  </div>
                                </div>

                                <motion.button 
                                  whileTap={{ scale: 0.95 }}
                                  onClick={() => addToCart(product)}
                                  className="w-full bg-lime-700 hover:bg-lime-800 text-white font-medium py-2.5 rounded-xl text-xs flex items-center justify-center gap-2 shadow-md shadow-lime-700/20 transition-all"
                                >
                                  <i data-lucide="shopping-bag" className="w-3.5 h-3.5"></i> Add to Cart
                                </motion.button>
                              </div>
                            </motion.div>
                          ))}
                        {visibleProducts.length === 0 && (
                          <p className="col-span-full rounded-2xl border border-stone-200 bg-white p-6 text-center text-sm text-stone-500">
                            {PRODUCT_LOAD_ERROR || (PRODUCTS.length === 0
                              ? 'No products are available yet.'
                              : 'No products match your search.')}
                          </p>
                        )}
                      </div>
                    </motion.div>
                  )}

                  {activeTab === 'categories' && (
                    <motion.div
                      key="categories"
                      initial={{ opacity: 0, y: 20 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0, y: -20 }}
                      transition={{ duration: 0.3 }}
                      className="space-y-8"
                    >
                      <div className="text-center space-y-3 max-w-xl mx-auto">
                        <h1 className="text-3xl sm:text-4xl font-black text-stone-900">Explore Categories</h1>
                        <p className="text-stone-500 text-sm">Find exactly what you are looking for by browsing through our specialized departments.</p>
                      </div>

                      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        {CATEGORIES.map((cat) => (
                          <motion.div
                            key={cat.id}
                            whileHover={{ y: -6 }}
                            whileTap={{ scale: 0.98 }}
                            onClick={() => setActiveTab('shop')}
                            className="relative rounded-3xl overflow-hidden aspect-video shadow-md cursor-pointer group flex items-end p-6"
                          >
                            <div className="absolute inset-0 bg-gradient-to-t from-stone-950/80 via-stone-950/20 to-transparent z-10"></div>
                            <img src={cat.image} alt={cat.name} className="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                            
                            <div className="relative z-20 text-white space-y-1">
                              <span className="text-xs font-semibold text-lime-400 uppercase tracking-widest">Department</span>
                              <h3 className="text-2xl font-black">{cat.name}</h3>
                              <p className="text-xs text-stone-300 flex items-center gap-1 pt-1 group-hover:underline">
                                Browse Products <i data-lucide="chevron-right" className="w-3.5 h-3.5"></i>
                              </p>
                            </div>
                          </motion.div>
                        ))}
                      </div>
                    </motion.div>
                  )}

                  {activeTab === 'testimonials' && (
                    <motion.div
                      key="testimonials"
                      initial={{ opacity: 0, y: 20 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0, y: -20 }}
                      transition={{ duration: 0.3 }}
                      className="space-y-12 max-w-4xl mx-auto"
                    >
                      <div className="text-center space-y-3">
                        <h1 className="text-3xl sm:text-4xl font-black text-stone-900">Customer Reviews</h1>
                        <p className="text-stone-500 text-sm">See why thousands of customers love shopping with ShopMate.</p>
                      </div>

                      <div className="space-y-6">
                        {TESTIMONIALS.map((t) => (
                          <div key={t.id} className="bg-white p-8 rounded-3xl border border-stone-200 shadow-xs flex flex-col md:flex-row gap-6 items-start md:items-center justify-between">
                            <div className="space-y-3 max-w-xl">
                              <div className="flex items-center text-amber-500">
                                {[...Array(5)].map((_, i) => <i key={i} data-lucide="star" className="w-4 h-4 fill-amber-500"></i>)}
                              </div>
                              <p className="text-stone-800 text-base italic">"{t.comment}"</p>
                              <div className="text-xs text-stone-400">Verified Purchase via ShopMate Secure Checkout</div>
                            </div>

                            <div className="flex items-center gap-4 bg-stone-50 p-4 rounded-2xl w-full md:w-auto">
                              <img src={t.avatar} alt={t.author} className="w-12 h-12 rounded-full object-cover" />
                              <div>
                                <h4 className="font-bold text-sm text-stone-900">{t.author}</h4>
                                <p className="text-xs text-stone-500">{t.role}</p>
                              </div>
                            </div>
                          </div>
                        ))}
                      </div>
                    </motion.div>
                  )}
                </AnimatePresence>
              </main>

              {/* Shopping Cart Drawer */}
              <AnimatePresence>
                {isCartOpen && (
                  <div className="fixed inset-0 z-50 flex justify-end">
                    <motion.div 
                      initial={{ opacity: 0 }}
                      animate={{ opacity: 1 }}
                      exit={{ opacity: 0 }}
                      onClick={() => setIsCartOpen(false)}
                      className="absolute inset-0 bg-stone-950/60 backdrop-blur-xs"
                    />
                    
                    <motion.div 
                      initial={{ x: '100%' }}
                      animate={{ x: 0 }}
                      exit={{ x: '100%' }}
                      transition={{ type: 'spring', damping: 25, stiffness: 200 }}
                      className="relative w-full max-w-md bg-white h-full shadow-2xl flex flex-col z-10"
                    >
                      <div className="p-6 border-b border-stone-200 flex items-center justify-between">
                        <div className="flex items-center gap-2">
                          <i data-lucide="shopping-bag" className="w-5 h-5 text-lime-700"></i>
                          <h2 className="font-black text-lg text-stone-900">Your Shopping Cart</h2>
                        </div>
                        <button onClick={() => setIsCartOpen(false)} className="p-2 hover:bg-stone-100 rounded-full text-stone-500">
                          <i data-lucide="x" className="w-5 h-5"></i>
                        </button>
                      </div>

                      <div className="flex-1 overflow-y-auto p-6 space-y-4">
                        {cart.length === 0 ? (
                          <div className="text-center py-20 space-y-4">
                            <div className="w-16 h-16 bg-stone-100 text-stone-400 rounded-full flex items-center justify-center mx-auto">
                              <i data-lucide="shopping-bag" className="w-8 h-8"></i>
                            </div>
                            <h3 className="font-bold text-stone-800">Your cart is empty</h3>
                            <p className="text-xs text-stone-500">Explore our catalog and add items to your cart.</p>
                            <button 
                              onClick={() => { setIsCartOpen(false); setActiveTab('shop'); }}
                              className="bg-lime-700 text-white text-xs font-medium px-6 py-2.5 rounded-full shadow-md"
                            >
                              Start Shopping
                            </button>
                          </div>
                        ) : (
                          cart.map((item) => (
                            <div key={item.id} className="flex gap-4 items-center bg-stone-50 p-4 rounded-2xl border border-stone-200">
                              <img src={item.image} alt={item.name} className="w-16 h-16 rounded-xl object-cover" />
                              <div className="flex-1">
                                <h4 className="font-bold text-xs text-stone-900 line-clamp-1">{item.name}</h4>
                                <div className="text-xs font-black text-lime-700 mt-1">${(item.price * item.qty).toFixed(2)}</div>
                                
                                <div className="flex items-center gap-3 mt-2">
                                  <div className="flex items-center bg-white rounded-lg border border-stone-200">
                                    <button onClick={() => updateCartQty(item.id, -1)} className="p-1 hover:bg-stone-100 text-stone-600 rounded-l-lg">
                                      <i data-lucide="minus" className="w-3 h-3"></i>
                                    </button>
                                    <span className="px-2.5 text-xs font-bold text-stone-800">{item.qty}</span>
                                    <button onClick={() => updateCartQty(item.id, 1)} className="p-1 hover:bg-stone-100 text-stone-600 rounded-r-lg">
                                      <i data-lucide="plus" className="w-3 h-3"></i>
                                    </button>
                                  </div>

                                  <button onClick={() => removeFromCart(item.id)} className="text-stone-400 hover:text-rose-600 p-1">
                                    <i data-lucide="trash-2" className="w-4 h-4"></i>
                                  </button>
                                </div>
                              </div>
                            </div>
                          ))
                        )}
                      </div>

                      {cart.length > 0 && (
                        <div className="p-6 border-t border-stone-200 space-y-4 bg-stone-50">
                          <div className="space-y-2 text-xs">
                            <div className="flex justify-between text-stone-600">
                              <span>Subtotal</span>
                              <span className="font-bold text-stone-900">${cartSubtotal.toFixed(2)}</span>
                            </div>
                            <div className="flex justify-between text-stone-600">
                              <span>Shipping</span>
                              <span>${deliveryFee.toFixed(2)}</span>
                            </div>
                            <div className="flex justify-between text-base font-black text-stone-900 pt-2 border-t border-stone-200">
                              <span>Total</span>
                              <span>${cartTotal.toFixed(2)}</span>
                            </div>
                          </div>

                          <motion.button 
                             whileTap={{ scale: 0.98 }}
                             onClick={() => {
                               if (!currentUser) {
                                 showToast('Please sign in before placing an order.');
                                 setIsLoginOpen(true);
                                 return;
                               }
                               setIsCartOpen(false);   
                               setCheckoutError('');
                               setIsCheckoutOpen(true);  
                             }}
                             className="w-full bg-lime-700 hover:bg-lime-800 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-lime-700/25 transition-all text-sm flex items-center justify-center gap-2"
                           >
                             Proceed to Checkout <i data-lucide="arrow-right" className="w-4 h-4"></i>
                           </motion.button>
                        </div>
                      )}
                    </motion.div>
                  </div>
                )}
                {/* Checkout Modal */}
               {isCheckoutOpen && (
                 <div className="fixed inset-0 z-50 flex items-center justify-center bg-stone-950/60 backdrop-blur-xs p-4">
                   <div className="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden">
                     <div className="p-6 border-b border-stone-200 flex items-center justify-between">
                       <h3 className="font-bold text-lg text-stone-900 flex items-center gap-2">
                         <i className="fa-solid fa-truck-fast text-lime-700"></i> ព័ត៌មានដឹកជញ្ជូន (Checkout)
                       </h3>
                       <button 
                         onClick={() => {
                           if (!isSubmittingOrder) {
                             setIsCheckoutOpen(false);
                             setCheckoutError('');
                           }
                         }}
                         disabled={isSubmittingOrder}
                         className="p-2 hover:bg-stone-100 rounded-full text-stone-500"
                       >
                         <i className="fa-solid fa-xmark"></i>
                       </button>
                     </div>
               
                     <div className="p-6">
                       <form 
                         onSubmit={async (e) => {
                           e.preventDefault();
                          if (isSubmittingOrder || cart.length === 0) return;
                          setIsSubmittingOrder(true);
                          setCheckoutError('');

                          const formData = new FormData(e.currentTarget);
                          try {
                            const response = await fetch('create_order.php', {
                              method: 'POST',
                              headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-Token': csrfToken
                              },
                              body: JSON.stringify({
                                name: formData.get('name'),
                                phone: formData.get('phone'),
                                address: formData.get('address'),
                                payment: formData.get('payment'),
                                items: cart.map((item) => ({
                                  productId: item.id,
                                  quantity: item.qty
                                }))
                              })
                            });
                            const result = await response.json();
                            if (!response.ok) {
                              throw new Error(result.error || 'Unable to save your order.');
                            }

                            setCart([]);
                            setIsCheckoutOpen(false);
                            showToast(`Order ${result.orderNumber} placed successfully! Thank you for shopping with ShopMate.`);
                          } catch (error) {
                            setCheckoutError(error.message || 'Unable to save your order. Please try again.');
                          } finally {
                            setIsSubmittingOrder(false);
                          }
                        }}
                        className="space-y-4"
                       >
                        {checkoutError && (
                          <div className="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert">
                            {checkoutError}
                          </div>
                        )}
                         <div className="space-y-1">
                           <label className="text-xs font-bold text-stone-700" htmlFor="custName">Full Name</label>
                           <input type="text" id="custName" placeholder="ឧ. សុខ ជា / Sok Chea" required name="name" className="w-full p-3 text-xs border border-stone-200 rounded-xl focus:outline-none focus:border-lime-700" />
                         </div>
               
                         <div className="space-y-1">
                           <label className="text-xs font-bold text-stone-700" htmlFor="custPhone">Phone Number</label>
                           <input type="tel" id="custPhone" placeholder="012 345 678" required name="phone" className="w-full p-3 text-xs border border-stone-200 rounded-xl focus:outline-none focus:border-lime-700" />
                         </div>
               
                         <div className="space-y-1">
                           <label className="text-xs font-bold text-stone-700" htmlFor="custAddress">Delivery Address</label>
                           <textarea id="custAddress" rows="2" placeholder="ផ្ទះលេខ, ផ្លូវ, ខណ្ឌ/ក្រុង..." required name="address" className="w-full p-3 text-xs border border-stone-200 rounded-xl focus:outline-none focus:border-lime-700"></textarea>
                         </div>
               
                         <div className="space-y-1">
                           <label className="text-xs font-bold text-stone-700">Payment Method</label>
                           <div className="space-y-2 pt-1">
                             <label className="flex items-center gap-2 p-3 border border-stone-200 rounded-xl cursor-pointer text-xs">
                               <input type="radio" name="payment" value="cod" defaultChecked className="accent-lime-700" />
                               <span><i className="fa-solid fa-hand-holding-dollar text-lime-700"></i> បង់ប្រាក់ពេលទំនិញដល់ (COD)</span>
                             </label>
                             <label className="flex items-center gap-2 p-3 border border-stone-200 rounded-xl cursor-pointer text-xs">
                               <input type="radio" name="payment" value="qr" className="accent-lime-700" />
                               <span><i className="fa-solid fa-qrcode text-lime-700"></i>QR Code</span>
                             </label>
                           </div>
                         </div>
               
                         <button type="submit" disabled={isSubmittingOrder} className="w-full bg-lime-700 hover:bg-lime-800 disabled:cursor-wait disabled:opacity-60 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-lime-700/25 transition-all text-sm flex items-center justify-center gap-2 mt-4">
                           <i className={isSubmittingOrder ? "fa-solid fa-spinner fa-spin" : "fa-solid fa-check"}></i> {isSubmittingOrder ? 'Saving order...' : 'បញ្ជាក់ការបញ្ជាទិញ (Confirm Order)'}
                         </button>
                       </form>
                     </div>
                   </div>
                 </div>
               )}
              </AnimatePresence>

              {/* Footer */}
              <footer className="bg-stone-900 text-stone-300 pt-16 pb-12 border-t border-stone-800 mt-20">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-10">
                  <div className="space-y-4">
                    <div className="flex items-center gap-2">
                      <div className="bg-lime-500 text-stone-950 p-2 rounded-xl">
                        <i data-lucide="shopping-bag" className="w-5 h-5"></i>
                      </div>
                      <span className="text-xl font-black text-white">ShopMa</span>
                    </div>
                    <p className="text-xs text-stone-400 leading-relaxed">
                      Your one-stop shop for quality products at the best prices. Experience lightning-fast delivery and top-notch customer care.
                    </p>
                  </div>

                  <div>
                    <h4 className="font-bold text-white text-sm uppercase tracking-wider mb-4">Quick Links</h4>
                    <ul className="space-y-2.5 text-xs">
                      {['home', 'shop', 'categories', 'testimonials'].map((tab) => (
                        <li key={tab}>
                          <button onClick={() => setActiveTab(tab)} className="hover:text-lime-400 capitalize transition-colors">
                            {tab === 'testimonials' ? 'Reviews' : tab}
                          </button>
                        </li>
                      ))}
                    </ul>
                  </div>

                  <div>
                    <h4 className="font-bold text-white text-sm uppercase tracking-wider mb-4">Customer Service</h4>
                    <ul className="space-y-2.5 text-xs text-stone-400">
                      <li><a href="#" className="hover:text-white transition-colors">Track Order</a></li>
                      <li><a href="#" className="hover:text-white transition-colors">Returns & Refunds</a></li>
                      <li><a href="#" className="hover:text-white transition-colors">Privacy Policy</a></li>
                      <li><a href="#" className="hover:text-white transition-colors">Terms of Service</a></li>
                    </ul>
                  </div>

                  <div className="space-y-4">
                    <h4 className="font-bold text-white text-sm uppercase tracking-wider">Subscribe to our newsletter</h4>
                    <p className="text-xs text-stone-400">Get the latest updates on new products and upcoming sales.</p>
                    <div className="flex items-center gap-2">
                      <input 
                        type="email" 
                        placeholder="Enter your email" 
                        className="bg-stone-800 border border-stone-700 px-4 py-2.5 rounded-xl text-xs text-white placeholder-stone-500 outline-none focus:border-lime-500 w-full"
                      />
                      <button 
                        onClick={() => showToast("Subscribed successfully!")}
                        className="bg-lime-600 hover:bg-lime-700 text-stone-950 font-bold px-4 py-2.5 rounded-xl text-xs transition-colors whitespace-nowrap"
                      >
                        Join
                      </button>
                    </div>
                  </div>
                </div>

                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pt-6 border-t border-stone-800 text-center text-xs text-stone-500">
                  &copy; {new Date().getFullYear()} ShopMate Inc. All rights reserved. Designed with Framer Motion & Tailwind CSS.
                </div>
              </footer>
            </div>
          );
        }
        ReactDOM.render(<App />, document.getElementById('root'));
    </script>
</body>

</html>