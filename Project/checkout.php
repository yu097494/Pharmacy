<?php
session_start();
include 'config.php';


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$total_amount = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = trim($_POST['user_name']);

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

    if (!empty($user_name)) {
        try {
            
            $stmt = $conn->prepare("INSERT INTO orders (user_name, user_id, total_amount, status) VALUES (?, ?, ?, 'pending')");
            $stmt->bind_param("sid", $user_name, $user_id, $total_amount);
            $stmt->execute();

            $order_id = $conn->insert_id;

            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");

            foreach ($_SESSION['cart'] as $item_id => $item) {
                $qty = intval($item['quantity']);
                $price = floatval($item['price']);
                
                $item_stmt->bind_param("iiid", $order_id, $item_id, $qty, $price);
                $item_stmt->execute();
            }

         
            unset($_SESSION['cart']);
            header("Location: order_history.php");
            exit();

        } catch (Exception $e) {
            die("<div style='color:red; font-family:sans-serif; padding:20px; background:#fee2e2; margin:20px; rounded:10px;'>
                    <h2>Database Error Occurred!</h2>
                    <p><b>Message:</b> " . $e->getMessage() . "</p>
                 </div>");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - City Pharmacy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-6 min-h-screen font-sans">

    <div class="max-w-lg mx-auto bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100">
        
        <!-- Header -->
        <div class="flex justify-between items-center pb-4 border-b border-gray-100 mb-6">
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-credit-card text-rose-500"></i> Checkout
            </h1>
            <a href="cart.php" class="text-xs font-semibold text-gray-500 hover:text-gray-800 transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to Cart
            </a>
        </div>

        <!-- Order Summary -->
        <div class="mb-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Order Items</h3>
            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl text-xs">
                        <span class="font-bold text-gray-800"><?php echo htmlspecialchars($item['name']); ?> <span class="text-gray-400">x<?php echo $item['quantity']; ?></span></span>
                        <span class="font-semibold text-gray-700"><?php echo number_format($item['price'] * $item['quantity']); ?> MMK</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Form -->
        <form action="checkout.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Customer Name / Phone</label>
                <input type="text" name="user_name" required placeholder="Enter name or phone" class="w-full border border-gray-200 p-3 rounded-xl text-xs bg-gray-50 focus:bg-white focus:outline-none focus:border-rose-500 transition">
            </div>

            <div class="p-4 bg-rose-50 rounded-xl border border-rose-100">
                <div class="flex justify-between text-sm font-bold text-gray-800">
                    <span>Total Amount:</span>
                    <span class="text-rose-600 text-base"><?php echo number_format($total_amount); ?> MMK</span>
                </div>
            </div>

            <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3.5 rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                Confirm Order
            </button>
        </form>

    </div>

</body>
</html>