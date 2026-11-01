<?php
// products.php

$products = [
    [
        'name' => 'Fresh Organic Bananas',
        'category' => 'Fruits & Vegetables',
        'price' => 2.99,
        'originalPrice' => 3.99,
        'rating' => 4.8,
        'reviews' => 128,
        'image' => 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Best Seller'
    ],
    [
        'name' => 'Crisp Hass Avocados (Pack of 4)',
        'category' => 'Fruits & Vegetables',
        'price' => 4.99,
        'originalPrice' => 6.50,
        'rating' => 4.7,
        'reviews' => 94,
        'image' => 'https://images.unsplash.com/photo-1523049673857-eb18f1d7b578?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Popular'
    ],
    [
        'name' => 'Organic Red Tomatoes',
        'category' => 'Fruits & Vegetables',
        'price' => 3.49,
        'originalPrice' => 4.99,
        'rating' => 4.6,
        'reviews' => 156,
        'image' => 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Fresh'
    ],
    [
        'name' => 'Fresh Baby Spinach',
        'category' => 'Fruits & Vegetables',
        'price' => 2.50,
        'originalPrice' => 3.50,
        'rating' => 4.9,
        'reviews' => 82,
        'image' => 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Organic'
    ],
    // Dairy & Eggs
    [
        'name' => 'Farm Fresh Grade A Eggs (Dozen)',
        'category' => 'Dairy & Eggs',
        'price' => 4.29,
        'originalPrice' => 5.49,
        'rating' => 4.9,
        'reviews' => 210,
        'image' => 'https://i.pinimg.com/1200x/7f/e6/c6/7fe6c68edc51bb3d64a80d54e78c4cf1.jpg',
        'badge' => 'Best Seller'
    ],
    [
        'name' => 'Artisan Cheddar Cheese Block',
        'category' => 'Dairy & Eggs',
        'price' => 5.99,
        'originalPrice' => 7.50,
        'rating' => 4.7,
        'reviews' => 64,
        'image' => 'https://images.unsplash.com/photo-1618160702438-9b02ab6515c9?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Trending'
    ],
    [
        'name' => 'Greek Yogurt Plain (500g)',
        'category' => 'Dairy & Eggs',
        'price' => 3.99,
        'originalPrice' => 4.99,
        'rating' => 4.8,
        'reviews' => 112,
        'image' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Healthy'
    ],
    // Snacks
    [
        'name' => 'Crispy Sea Salt Potato Chips',
        'category' => 'Snacks',
        'price' => 2.25,
        'originalPrice' => 3.00,
        'rating' => 4.5,
        'reviews' => 44,
        'image' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Popular'
    ],
    [
        'name' => 'Roasted & Salted Mixed Nuts',
        'category' => 'Snacks',
        'price' => 6.99,
        'originalPrice' => 8.99,
        'rating' => 4.7,
        'reviews' => 45,
        'image' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Sale'
    ],
    [
        'name' => 'Dark Chocolate Almond Bar',
        'category' => 'Snacks',
        'price' => 3.50,
        'originalPrice' => 4.50,
        'rating' => 4.8,
        'reviews' => 130,
        'image' => 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Trending'
    ],
    // Meat & Seafood
    [
        'name' => 'Fresh Boneless Chicken Breast',
        'category' => 'Meat & Seafood',
        'price' => 9.99,
        'originalPrice' => 12.99,
        'rating' => 4.8,
        'reviews' => 142,
        'image' => 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Best Seller'
    ],
    [
        'name' => 'Atlantic Salmon Fillet',
        'category' => 'Meat & Seafood',
        'price' => 12.50,
        'originalPrice' => 15.00,
        'rating' => 4.9,
        'reviews' => 88,
        'image' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Fresh'
    ],
    [
        'name' => 'Lean Ground Beef (1 lb)',
        'category' => 'Meat & Seafood',
        'price' => 7.99,
        'originalPrice' => 9.99,
        'rating' => 4.6,
        'reviews' => 76,
        'image' => 'https://images.unsplash.com/photo-1588168333986-5078d3ae3976?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Popular'
    ],
    // Bakery
    [
        'name' => 'Artisan Sourdough Bread',
        'category' => 'Bakery',
        'price' => 4.50,
        'originalPrice' => 5.50,
        'rating' => 4.8,
        'reviews' => 115,
        'image' => 'https://images.unsplash.com/photo-1586444248902-2f64eddc13df?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Freshly Baked'
    ],
    [
        'name' => 'All-Butter Croissants (4 Pack)',
        'category' => 'Bakery',
        'price' => 5.00,
        'originalPrice' => 6.50,
        'rating' => 4.7,
        'reviews' => 95,
        'image' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Popular'
    ],
    [
        'name' => 'Chocolate Chip Cookies',
        'category' => 'Bakery',
        'price' => 3.99,
        'originalPrice' => 4.99,
        'rating' => 4.9,
        'reviews' => 160,
        'image' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Best Seller'
    ],
    // beverages
    [
        'name' => 'Fresh Whole Milk (1 Gallon)',
        'category' => 'beverages',
        'price' => 3.79,
        'originalPrice' => 4.49,
        'rating' => 4.8,
        'reviews' => 189,
        'image' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Essential'
    ],
    [
        'name' => 'Organic Almond Milk (Unsweetened)',
        'category' => 'beverages',
        'price' => 3.99,
        'originalPrice' => 4.99,
        'rating' => 4.6,
        'reviews' => 110,
        'image' => 'https://images.unsplash.com/photo-1563636619-e9143da7973b?w=600&auto=format&fit=crop&q=80',
        'badge' => 'Plant-Based'
    ],
    [
        'name' => 'Oat Milk Barista Edition',
        'category' => 'beverages',
        'price' => 4.29,
        'originalPrice' => 5.29,
        'rating' => 4.9,
        'reviews' => 204,
        'image' => 'https://i.pinimg.com/736x/ba/3e/81/ba3e81a5f2c2991c739d426bafd90c07.jpg',
        'badge' => 'Trending'
    ],


    [
        'name' => 'Fresh Organic Cherry',
        'category' => 'Fruits & Vegetables',
        'price' => 2.99,
        'originalPrice' => 3.99,
        'rating' => 4.8,
        'reviews' => 128,
        'image' => 'https://i.pinimg.com/236x/e0/f8/6e/e0f86e3e0fdbd82a53b0f4f42b4ae163.jpg',
        'badge' => 'Best Seller'
    ],
    [
        'name' => 'Healty Fruit',
        'category' => 'Fruits & Vegetables',
        'price' => 4.99,
        'originalPrice' => 6.50,
        'rating' => 4.7,
        'reviews' => 94,
        'image' => 'https://i.pinimg.com/236x/6b/dd/62/6bdd62d8f58d723f5b7aa66c4fdac416.jpg',
        'badge' => 'Popular'
    ],
    [
        'name' => 'Mango',
        'category' => 'Fruits & Vegetables',
        'price' => 3.49,
        'originalPrice' => 4.99,
        'rating' => 4.6,
        'reviews' => 156,
        'image' => 'https://i.pinimg.com/736x/25/a6/94/25a694afb5e9b2309865fc25b12b0eb3.jpg',
        'badge' => 'Fresh'
    ],
    [
        'name' => 'Apple',
        'category' => 'Fruits & Vegetables',
        'price' => 2.50,
        'originalPrice' => 3.50,
        'rating' => 4.9,
        'reviews' => 82,
        'image' => 'https://i.pinimg.com/736x/b4/c2/77/b4c27737284b5e82c463742cbaf52a22.jpg',
        'badge' => 'Organic'
    ],
    [
        'name' => 'KiWi',
        'category' => 'Fruits & Vegetables',
        'price' => 2.50,
        'originalPrice' => 3.50,
        'rating' => 4.9,
        'reviews' => 82,
        'image' => 'https://i.pinimg.com/736x/9a/c8/8d/9ac88d29d7bf700eb174f932f918b09d.jpg',
        'badge' => 'Organic'
    ],
     [
        'name' => 'Orange',
        'category' => 'Fruits & Vegetables',
        'price' => 5.50,
        'originalPrice' => 3.50,
        'rating' => 4.9,
        'reviews' => 82,
        'image' => 'https://i.pinimg.com/736x/ad/7a/a6/ad7aa6ca9b11e4f63385c658b92267ac.jpg',
        'badge' => 'Organic'
    ],
    // Dairy & Eggs
    [
        'name' => 'Mayonnaise',
        'category' => 'Dairy & Eggs',
        'price' => 4.29,
        'originalPrice' => 5.49,
        'rating' => 4.9,
        'reviews' => 210,
        'image' => 'https://i.pinimg.com/1200x/ea/7e/52/ea7e52f750dfa70c1d38737ab11232ba.jpg',
        'badge' => 'Best Seller'
    ],
    [
        'name' => 'NIDO',
        'category' => 'Dairy & Eggs',
        'price' => 5.99,
        'originalPrice' => 7.50,
        'rating' => 4.7,
        'reviews' => 64,
        'image' => 'https://i.pinimg.com/1200x/b1/6d/4d/b16d4ddca2fa06f17292e8532ccd8a9f.jpg',
        'badge' => 'Trending'
    ],
    [
        'name' => 'Chesse',
        'category' => 'Dairy & Eggs',
        'price' => 3.99,
        'originalPrice' => 4.99,
        'rating' => 4.8,
        'reviews' => 112,
        'image' => 'https://i.pinimg.com/736x/14/f2/aa/14f2aa762c17ade9301762bb4a9ff18c.jpg',
        'badge' => 'Healthy'
    ],
    // Snacks
    [
        'name' => 'Doristos',
        'category' => 'Snacks',
        'price' => 2.25,
        'originalPrice' => 3.00,
        'rating' => 4.5,
        'reviews' => 44,
        'image' => 'https://i.pinimg.com/736x/54/f6/c8/54f6c82607973072dbac93df47563664.jpg',
        'badge' => 'Popular'
    ],
    [
        'name' => 'KitKat',
        'category' => 'Snacks',
        'price' => 6.99,
        'originalPrice' => 8.99,
        'rating' => 4.7,
        'reviews' => 45,
        'image' => 'https://i.pinimg.com/736x/5a/71/84/5a7184e1d347ebbdb920c49ae5c99266.jpg',
        'badge' => 'Sale'
    ],
    [
        'name' => 'Buener',
        'category' => 'Snacks',
        'price' => 3.50,
        'originalPrice' => 4.50,
        'rating' => 4.8,
        'reviews' => 130,
        'image' => 'https://i.pinimg.com/736x/d0/72/8f/d0728fe0d394f0f9eb2031bb19c13cea.jpg',
        'badge' => 'Trending'
    ],
    [
        'name' => 'Cholate',
        'category' => 'Snacks',
        'price' => 3.50,
        'originalPrice' => 4.50,
        'rating' => 4.8,
        'reviews' => 130,
        'image' => 'https://i.pinimg.com/1200x/96/21/a4/9621a4544cd0a3c7db9eae8791d13714.jpg',
        'badge' => 'Trending'
    ],
    [
        'name' => 'Pocky',
        'category' => 'Snacks',
        'price' => 3.50,
        'originalPrice' => 4.50,
        'rating' => 4.5,
        'reviews' => 130,
        'image' => 'https://i.pinimg.com/1200x/45/aa/6a/45aa6ac06aeed9241ce1b07294adc473.jpg',
        'badge' => 'Trending'
    ],
    [
        'name' => 'Garmmy',
        'category' => 'Snacks',
        'price' => 4.50,
        'originalPrice' => 4.50,
        'rating' => 4.8,
        'reviews' => 130,
        'image' => 'https://i.pinimg.com/736x/e1/38/c6/e138c6eb090907b4425e08d0e76baf12.jpg',
        'badge' => 'Trending'
    ],
    // Meat & Seafood
    [
        'name' => 'Shrimp',
        'category' => 'Meat & Seafood',
        'price' => 9.99,
        'originalPrice' => 12.99,
        'rating' => 4.8,
        'reviews' => 142,
        'image' => 'https://i.pinimg.com/736x/af/77/bc/af77bc4f7b260c8057a76a69dd9d8e4d.jpg',
        'badge' => 'Best Seller'
    ],
    [
        'name' => 'Beef meat',
        'category' => 'Meat & Seafood',
        'price' => 12.50,
        'originalPrice' => 15.00,
        'rating' => 4.9,
        'reviews' => 88,
        'image' => 'https://i.pinimg.com/736x/b5/00/69/b5006951b0d6b07a83e4ac7cbd017382.jpg',
        'badge' => 'Fresh'
    ],
    [
        'name' => 'Ocean Legacy Salmon',
        'category' => 'Meat & Seafood',
        'price' => 9.5,
        'originalPrice' => 9.99,
        'rating' => 4.6,
        'reviews' => 76,
        'image' => 'https://i.pinimg.com/1200x/f9/b4/5a/f9b45a9820da55ced00a57c7f3b5ad3f.jpg',
        'badge' => 'Popular'
    ],
    [
        'name' => 'chicken meat',
        'category' => 'Meat & Seafood',
        'price' => 8,
        'originalPrice' => 9.99,
        'rating' => 4.6,
        'reviews' => 76,
        'image' => 'https://i.pinimg.com/736x/bf/f2/3f/bff23fa9ebd6c71fce885211f3d899c5.jpg',
        'badge' => 'Popular'
    ],
    [
        'name' => 'chicken meat',
        'category' => 'Meat & Seafood',
        'price' => 7.99,
        'originalPrice' => 9.99,
        'rating' => 4.6,
        'reviews' => 76,
        'image' => 'https://i.pinimg.com/1200x/a2/74/bc/a274bc5141875fc70ec6478a9167b0db.jpg',
        'badge' => 'Popular'
    ],
    [
        'name' => 'ShuShy',
        'category' => 'Meat & Seafood',
        'price' => 20.00,
        'originalPrice' => 9.99,
        'rating' => 4.6,
        'reviews' => 76,
        'image' => 'https://i.pinimg.com/736x/77/c6/ef/77c6efc5bded79b3a67f3e96c95ee25a.jpg',
        'badge' => 'Popular'
    ],
    [
        'name' => 'Phiza',
        'category' => 'Meat & Seafood',
        'price' => 20.00,
        'originalPrice' => 9.99,
        'rating' => 4.6,
        'reviews' => 76,
        'image' => 'https://i.pinimg.com/1200x/13/83/41/138341f8c3db0f8129cc0baf6871e843.jpg',
        'badge' => 'Popular'
    ],
    [
        'name' => 'Burrito',
        'category' => 'Meat & Seafood',
        'price' => 20.00,
        'originalPrice' => 9.99,
        'rating' => 4.6,
        'reviews' => 76,
        'image' => 'https://i.pinimg.com/736x/f6/51/23/f65123f27c668548a1791e24f011b234.jpg',
        'badge' => 'Popular'
    ],
     [
        'name' => 'Shanwich',
        'category' => 'Meat & Seafood',
        'price' => 20.00,
        'originalPrice' => 9.99,
        'rating' => 4.6,
        'reviews' => 76,
        'image' => 'https://i.pinimg.com/736x/54/40/20/5440207cf2cb7b3d7c37378e5772aa03.jpg',
        'badge' => 'Popular'
    ],
    // Bakery
    [
        'name' => 'Artisan Sourdough Bread',
        'category' => 'Bakery',
        'price' => 4.50,
        'originalPrice' => 5.50,
        'rating' => 4.8,
        'reviews' => 115,
        'image' => 'https://i.pinimg.com/1200x/8b/29/54/8b29544b31b3093fcd151562d3382270.jpg',
        'badge' => 'Freshly Baked'
    ],
    [
        'name' => 'sourdough bread',
        'category' => 'Bakery',
        'price' => 5.00,
        'originalPrice' => 6.50,
        'rating' => 4.7,
        'reviews' => 95,
        'image' => 'https://i.pinimg.com/736x/e2/04/3b/e2043bad7d0432150abc85c413273a93.jpg',
        'badge' => 'Popular'
    ],
    [
        'name' => 'buguer',
        'category' => 'Bakery',
        'price' => 3.99,
        'originalPrice' => 4.99,
        'rating' => 4.9,
        'reviews' => 160,
        'image' => 'https://i.pinimg.com/736x/7f/ed/37/7fed376a60ef328d0825df67e340a0d0.jpg',
        'badge' => 'Best Seller'
    ],
    [
        'name' => 'Croissant',
        'category' => 'Bakery',
        'price' => 1.00,
        'originalPrice' => 4.99,
        'rating' => 4.9,
        'reviews' => 160,
        'image' => 'https://i.pinimg.com/736x/24/be/15/24be158eccb0b4fbd6d51427a002f519.jpg',
        'badge' => 'Best Seller'
    ],
    // beverages
    [
        'name' => 'Milk Tea',
        'category' => 'beverages',
        'price' => 1.50,
        'originalPrice' => 4.49,
        'rating' => 4.8,
        'reviews' => 189,
        'image' => 'https://i.pinimg.com/1200x/5c/8e/85/5c8e8511b021f3377491199e482b04cf.jpg',
        'badge' => 'Essential'
    ],
    [
        'name' => 'COCA',
        'category' => 'beverages',
        'price' => 2.00,
        'originalPrice' => 4.99,
        'rating' => 4.6,
        'reviews' => 110,
        'image' => 'https://i.pinimg.com/736x/d1/bc/a1/d1bca114355758791654eadf794ca8b2.jpg',
        'badge' => 'Plant-Based'
    ],
    [
        'name' => 'Yukult',
        'category' => 'beverages',
        'price' => 1.50,
        'originalPrice' => 5.29,
        'rating' => 4.9,
        'reviews' => 204,
        'image' => 'https://i.pinimg.com/736x/af/03/93/af0393259a7cd64dcd681d1f76cd82d1.jpg',
        'badge' => 'Trending'
    ]
];
?>