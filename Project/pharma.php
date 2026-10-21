<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'config.php';


if (isset($_POST['add_to_cart'])) {
  
    if (!isset($_SESSION['username'])) {
        echo "<script>
                alert('Please login first to add items to your cart!');
                window.location.href = 'login.php';
              </script>";
        exit();
    }

    $item_id = $_POST['item_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    
   
    $quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;


    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$item_id] = array(
            'name' => $name,
            'price' => $price,
            'image' => $image,
            'quantity' => $quantity
        );
    }

   
    if (isset($_SESSION['cart'][$item_id])) {
        $redirect_url = "pharma.php" . (isset($_GET['cat']) ? "?cat=" . urlencode($_GET['cat']) : "");
        header("Location: " . $redirect_url);
        exit();
    } else {
        echo "<script>alert('Failed to add item to cart. Please try again.');</script>";
    }
}

$selected_cat = isset($_GET['cat']) ? strtolower(trim($_GET['cat'])) : 'all';

if ($selected_cat === 'new') {
    $query = "SELECT * FROM items WHERE is_new = 1 ORDER BY id DESC";
} elseif ($selected_cat !== 'all') {
    $safe_cat = $conn->real_escape_string($selected_cat);
    $query = "SELECT * FROM items WHERE LOWER(category) = '$safe_cat' ORDER BY id DESC";
} else {
    $query = "SELECT * FROM items ORDER BY id DESC";
}

$items_result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Pharmacy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 p-6">

    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <h1 class="text-xl font-bold text-rose-500 flex items-center gap-2">
                <i class="fa-solid fa-house-medical"></i> City Pharmacy
            </h1>
            <div class="flex items-center gap-3">
                <a href="contact.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-2 rounded-lg font-bold flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-headset text-rose-500"></i> Contact Admin
                </a>

                <?php if (isset($_SESSION['username'])): ?>
                    <span class="text-xs font-semibold text-gray-600">Logged in as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="admin.php" class="bg-rose-500 text-white text-xs px-3 py-2 rounded-lg font-bold">Admin Dashboard</a>
                    <?php endif; ?>
                    <a href="logout.php" class="bg-gray-800 text-white text-xs px-3 py-2 rounded-lg font-bold">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="bg-rose-500 text-white text-xs px-3 py-2 rounded-lg font-bold">Login</a>
                <?php endif; ?>

                <?php 
                    $cart_count = 0;
                    if (isset($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $c_item) {
                            $cart_count += $c_item['quantity'];
                        }
                    }
                ?>
                <a href="cart.php" class="bg-rose-100 text-rose-600 text-xs px-3 py-2 rounded-lg font-bold flex items-center gap-1">
                    <i class="fa-solid fa-cart-shopping"></i> Cart (<?php echo $cart_count; ?>)
                </a>
            </div>
        </div>

        <!-- Category Filter Buttons -->
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <?php
            $categories = [
                'all' => 'All Products',
                'new' => ' New Items',
                'skincare' => 'Skincare',
                'collagen' => 'Collagen',
                'drop' => 'Drop',
                'supplement' => 'Supplement',
                'general' => 'General'
            ];

            foreach ($categories as $key => $label):
                $active_class = ($selected_cat === $key) 
                    ? 'bg-rose-500 text-white shadow-sm' 
                    : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200';
            ?>
                <a href="pharma.php?cat=<?php echo $key; ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition <?php echo $active_class; ?>">
                    <?php echo $label; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Search Bar -->
        <div class="bg-white p-3.5 rounded-2xl shadow-sm border border-gray-100 mb-6 flex items-center gap-3">
            <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm pl-1"></i>
            <input 
                type="text" 
                id="searchMedicine" 
                onkeyup="filterMedicines()" 
                placeholder="Search medicine name or category..." 
                class="w-full text-xs bg-transparent focus:outline-none text-gray-700"
            >
        </div>

        <!-- Medicine Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5" id="productGrid">
            <?php if ($items_result && $items_result->num_rows > 0): ?>
                <?php while ($item = $items_result->fetch_assoc()): ?>
                    <div class="product-card bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between relative">
                        <div>
                            <div class="relative mb-3">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" class="w-full h-40 object-cover rounded-xl border border-gray-100" onerror="this.src='https://via.placeholder.com/150?text=No+Image';">
                              
                                <?php if (isset($item['is_new']) && intval($item['is_new']) === 1): ?>
                                    <span class="absolute top-2 right-2 bg-rose-500 text-white text-[9px] font-extrabold px-2 py-0.5 rounded-full uppercase shadow">
                                        NEW
                                    </span>
                                <?php endif; ?>
                            </div>

                            <span class="product-category text-[10px] font-bold text-blue-500 bg-blue-50 px-2 py-0.5 rounded-md inline-block mb-1">
                                <?php echo htmlspecialchars($item['category'] ?? 'General'); ?>
                            </span>

                            <h3 class="product-name font-bold text-xs text-gray-800 line-clamp-2"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="text-rose-500 font-extrabold text-xs mt-1"><?php echo number_format($item['price']); ?> MMK</p>
                        </div>

                        <!-- Add to Cart Form with Quantity Field and Confirmation Alert -->
                        <form action="pharma.php<?php echo isset($_GET['cat']) ? '?cat='.urlencode($_GET['cat']) : ''; ?>" method="POST" class="mt-4 space-y-2">
                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($item['name']); ?>">
                            <input type="hidden" name="price" value="<?php echo $item['price']; ?>">
                            <input type="hidden" name="image" value="<?php echo htmlspecialchars($item['image']); ?>">
                            
                            <!-- Quantity Selector -->
                            <div class="flex items-center justify-between border rounded-xl p-1 bg-gray-50">
                                <span class="text-[11px] font-semibold text-gray-500 pl-2">Qty:</span>
                                <input 
                                    type="number" 
                                    name="quantity" 
                                    value="1" 
                                    min="1" 
                                    max="99" 
                                    required 
                                    class="w-14 text-center font-bold text-xs bg-white border rounded-lg py-1 text-gray-700 focus:outline-none focus:ring-1 focus:ring-rose-500"
                                >
                            </div>

                            <button type="submit" name="add_to_cart" onclick="return confirm('Are you sure you want to add this item to your cart?');" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-bold text-xs py-2.5 rounded-xl transition shadow-sm flex items-center justify-center gap-1">
                                <i class="fa-solid fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-4 text-center py-10 text-gray-400 text-xs font-bold bg-white rounded-2xl border border-gray-100">
                    No items found in this category.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Real-time JavaScript Search Filter -->
    <script>
    function filterMedicines() {
        let input = document.getElementById('searchMedicine').value.toLowerCase().trim();
        let cards = document.querySelectorAll('#productGrid .product-card');

        cards.forEach(card => {
            let nameElement = card.querySelector('.product-name');
            let categoryElement = card.querySelector('.product-category');

            if (nameElement && categoryElement) {
                let name = nameElement.textContent.toLowerCase();
                let category = categoryElement.textContent.toLowerCase();

                if (name.includes(input) || category.includes(input)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            }
        });
    }
    </script>

</body>
</html>