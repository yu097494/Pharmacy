<?php
session_start();
include 'config.php';


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if (isset($_POST['update_quantity'])) {
    $item_id = intval($_POST['item_id']);
    $action = $_POST['action'];

    if (isset($_SESSION['cart'][$item_id])) {
        if ($action === 'increase') {
            $_SESSION['cart'][$item_id]['quantity'] += 1;
        } elseif ($action === 'decrease') {
            $_SESSION['cart'][$item_id]['quantity'] -= 1;
            if ($_SESSION['cart'][$item_id]['quantity'] <= 0) {
                unset($_SESSION['cart'][$item_id]); 
            }
        }
    }
    header("Location: cart.php");
    exit();
}


if (isset($_GET['remove_id'])) {
    $remove_id = intval($_GET['remove_id']);
    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
    }
    header("Location: cart.php");
    exit();
}


$total_amount = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shopping Cart - City Pharmacy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 text-gray-800 font-sans min-h-screen p-6">

    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
        
        <!-- Header -->
        <div class="flex justify-between items-center pb-6 border-b border-gray-100 mb-6">
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-bag-shopping text-rose-500"></i> My Shopping Cart
            </h1>
            <a href="pharma.php" class="text-xs font-semibold text-gray-500 hover:text-gray-800 transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to Store
            </a>
        </div>

        <!-- Cart Items List -->
        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="divide-y divide-gray-100">
                <?php foreach ($_SESSION['cart'] as $id => $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total_amount += $subtotal;
                    
                    $img_url = !empty($item['image']) ? $item['image'] : 'https://via.placeholder.com/150?text=No+Image';
                    if (strpos($img_url, 'http://') === 0) {
                        $img_url = str_replace('http://', 'https://', $img_url);
                    }
                ?>
                    <div class="py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                        
                        <!-- Item Image & Info -->
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <img src="<?php echo htmlspecialchars($img_url); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                 referrerpolicy="no-referrer"
                                 class="w-16 h-16 object-cover rounded-xl border border-gray-100 bg-gray-50 flex-shrink-0"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/150?text=Error';">
                            
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="text-xs text-gray-500 mt-0.5"><?php echo number_format($item['price']); ?> MMK each</p>
                            </div>
                        </div>

                        <!-- Quantity Controls & Subtotal & Delete -->
                        <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto">
                            
                            <!-- Quantity +/- Form -->
                            <form action="cart.php" method="POST" class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-gray-50">
                                <input type="hidden" name="item_id" value="<?php echo $id; ?>">
                                <button type="submit" name="action" value="decrease" name="update_quantity" class="px-2.5 py-1 text-gray-600 hover:bg-gray-200 text-xs font-bold transition">-</button>
                                <span class="px-3 py-1 text-xs font-semibold text-gray-800 bg-white"><?php echo $item['quantity']; ?></span>
                                <button type="submit" name="action" value="increase" name="update_quantity" class="px-2.5 py-1 text-gray-600 hover:bg-gray-200 text-xs font-bold transition">+</button>
                                <input type="hidden" name="update_quantity" value="1">
                            </form>

                            <!-- Subtotal -->
                            <div class="text-right">
                                <span class="text-sm font-bold text-gray-800"><?php echo number_format($subtotal); ?> MMK</span>
                            </div>

                            <!-- Remove Button -->
                            <a href="cart.php?remove_id=<?php echo $id; ?>" 
                               onclick="return confirm('Are you sure you want to remove this item?');"
                               class="text-gray-400 hover:text-rose-500 transition p-1 text-sm">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Total Amount & Checkout Button -->
            <div class="mt-8 pt-6 border-t border-gray-100 space-y-4">
                <div class="flex justify-between items-center text-base font-bold text-gray-800">
                    <span>Total Amount:</span>
                    <span class="text-xl text-rose-600"><?php echo number_format($total_amount); ?> MMK</span>
                </div>

                <a href="checkout.php" class="block w-full text-center bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 rounded-xl transition shadow-sm text-sm">
                    Proceed to Checkout
                </a>
            </div>

        <?php else: ?>
            <!-- Empty Cart Display -->
            <div class="text-center py-12">
                <i class="fa-solid fa-cart-flatbed text-4xl text-gray-300 mb-3"></i>
                <p class="text-sm font-semibold text-gray-500">Your cart is currently empty.</p>
                <a href="pharma.php" class="inline-block mt-4 bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold px-6 py-2.5 rounded-xl transition">
                    Start Shopping
                </a>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>