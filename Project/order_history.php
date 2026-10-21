<?php
session_start();
include 'config.php';

$sql = "SELECT * FROM orders ORDER BY id DESC";
$orders = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History - City Pharmacy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-6 min-h-screen">
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-rose-500"></i> Order History
            </h1>
            <a href="pharma.php" class="text-xs text-rose-500 font-bold hover:underline">Back to Store</a>
        </div>

        <?php if ($orders && $orders->num_rows > 0): ?>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                    <div class="flex justify-between items-center border-b pb-3 border-gray-100">
                        <div>
                            <span class="text-xs font-bold text-gray-400">Order #<?php echo $order['id']; ?></span>
                            <h3 class="text-sm font-bold text-gray-800">Customer: <?php echo htmlspecialchars($order['user_name']); ?></h3>
                        </div>
                        <span class="text-xs text-gray-400"><?php echo $order['created_at']; ?></span>
                    </div>

                    <div class="space-y-2 text-xs text-gray-600">
                        <?php
                        $order_id = $order['id'];
                        $items_sql = "SELECT order_items.*, items.name 
                                      FROM order_items 
                                      LEFT JOIN items ON order_items.item_id = items.id 
                                      WHERE order_items.order_id = $order_id";
                        $items = $conn->query($items_sql);
                        while ($item = $items->fetch_assoc()):
                            $subtotal = $item['price'] * $item['quantity'];
                        ?>
                            <div class="flex justify-between items-center bg-gray-50 p-2.5 rounded-xl">
                                <div>
                                    <span class="font-bold text-gray-800"><?php echo htmlspecialchars($item['name'] ?? 'Item #'.$item['item_id']); ?></span>
                                    <span class="text-gray-400 ml-2">x <?php echo $item['quantity']; ?></span>
                                </div>
                                <span class="font-semibold text-gray-700"><?php echo number_format($subtotal); ?> MMK</span>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="border-t pt-3 border-gray-100 flex justify-between items-center font-bold text-sm">
                        <span class="text-gray-600">Total Paid:</span>
                        <span class="text-emerald-600 text-base"><?php echo number_format($order['total_amount']); ?> MMK</span>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="bg-white p-8 text-center rounded-2xl text-gray-400 text-sm border border-gray-100">
                No order history found.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>