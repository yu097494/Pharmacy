<?php
session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: pharma.php");
    exit();
}

include 'config.php';

$conn->query("ALTER TABLE items MODIFY COLUMN image TEXT");


if (isset($_POST['add_item'])) {
    $name     = trim($_POST['name']);
    $price    = trim($_POST['price']);
    $category = trim($_POST['category']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $image    = trim($_POST['image']);
    $is_new   = isset($_POST['is_new']) ? 1 : 0;

    if (strpos($image, 'http://') === 0) {
        $image = str_replace('http://', 'https://', $image);
    }

    if (!empty($name) && !empty($price)) {
        $stmt = $conn->prepare("INSERT INTO items (name, price, category, quantity, image, is_new) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsssi", $name, $price, $category, $quantity, $image, $is_new);

        if ($stmt->execute()) {
            echo "<script>alert('Product added successfully!'); window.location.href='admin.php';</script>";
            exit();
        } else {
            echo "<script>alert('Failed to add product: " . addslashes($conn->error) . "');</script>";
        }
    }
}


if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('Item deleted successfully!'); window.location.href='admin.php';</script>";
        exit();
    }
}


$sql = "SELECT * FROM items ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - City Pharmacy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 text-gray-800 font-sans min-h-screen p-6">

    <div class="max-w-6xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Admin Dashboard</h1>
                <p class="text-xs text-gray-500">Manage your pharmacy products and inventory</p>
            </div>
            <div class="flex gap-3">
                <a href="pharma.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-4 py-2.5 rounded-xl font-semibold transition">
                    View Store
                </a>
                <a href="logout.php" class="bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs px-4 py-2.5 rounded-xl font-semibold transition">
                    Logout
                </a>
            </div>
        </div>

        <!-- Add New Product Form Section -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Add New Product</h2>
            
            <form action="admin.php" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Product Name -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Product Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Eye Drop" class="w-full border p-2.5 rounded-xl text-xs bg-gray-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-rose-500">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
                        <input type="text" name="category" placeholder="e.g. Drop, Skincare, Collagen" class="w-full border p-2.5 rounded-xl text-xs bg-gray-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-rose-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Price -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Price (MMK) *</label>
                        <input type="number" name="price" required placeholder="e.g. 25000" class="w-full border p-2.5 rounded-xl text-xs bg-gray-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-rose-500">
                    </div>

                    <!-- Stock Quantity -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Stock Quantity *</label>
                        <input type="number" name="quantity" value="10" min="0" required class="w-full border p-2.5 rounded-xl text-xs bg-gray-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-rose-500">
                    </div>
                </div>

                <!-- Unsplash Image URL -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Image URL (Unsplash Link)</label>
                    <input type="text" name="image" placeholder="https://images.unsplash.com/..." class="w-full border p-2.5 rounded-xl text-xs bg-gray-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-rose-500">
                </div>

                <!-- Mark as New Badge Checkbox -->
                <div class="flex items-center justify-between pt-2">
                    <label class="inline-flex items-center text-xs font-semibold text-gray-600 cursor-pointer">
                        <input type="checkbox" name="is_new" value="1" checked class="rounded text-rose-500 mr-2 focus:ring-0"> 
                        Mark as New Item (NEW Badge)
                    </label>

                    <button type="submit" name="add_item" class="bg-rose-500 hover:bg-rose-600 text-white font-bold text-xs px-6 py-2.5 rounded-xl transition shadow-sm">
                        + Add Product
                    </button>
                </div>
            </form>
        </div>

        <!-- Product Table Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-800">Product List</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">ID</th>
                            <th class="p-4 font-semibold">Image</th>
                            <th class="p-4 font-semibold">Name</th>
                            <th class="p-4 font-semibold">Category</th>
                            <th class="p-4 font-semibold">Price</th>
                            <th class="p-4 font-semibold">Stock</th>
                            <th class="p-4 font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="p-4 font-medium text-gray-500">#<?php echo $row['id']; ?></td>
                                    
                                    <!-- Image Display -->
                                    <td class="p-4">
                                        <?php 
                                            $img_url = !empty($row['image']) ? $row['image'] : 'https://via.placeholder.com/150?text=No+Image';
                                            if (strpos($img_url, 'http://') === 0) {
                                                $img_url = str_replace('http://', 'https://', $img_url);
                                            }
                                        ?>
                                        <img src="<?php echo htmlspecialchars($img_url); ?>" 
                                             alt="<?php echo htmlspecialchars($row['name']); ?>" 
                                             referrerpolicy="no-referrer"
                                             class="w-12 h-12 object-cover rounded-xl border border-gray-100 bg-gray-50"
                                             onerror="this.onerror=null; this.src='https://via.placeholder.com/150?text=Error';">
                                    </td>

                                    <td class="p-4 font-bold text-gray-800">
                                        <?php echo htmlspecialchars($row['name']); ?>
                                        <?php if (isset($row['is_new']) && $row['is_new'] == 1): ?>
                                            <span class="ml-1 bg-rose-100 text-rose-600 text-[10px] font-bold px-2 py-0.5 rounded-full">NEW</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-gray-500"><?php echo htmlspecialchars($row['category'] ?? '-'); ?></td>
                                    <td class="p-4 font-bold text-emerald-600"><?php echo number_format($row['price']); ?> MMK</td>
                                    <td class="p-4 text-gray-600"><?php echo htmlspecialchars($row['quantity'] ?? 0); ?> pcs</td>
                                    
                                    <!-- Action Buttons -->
                                    <td class="p-4 text-center">
                                        <div class="flex justify-center items-center gap-2">
                                            <a href="edit_item.php?id=<?php echo $row['id']; ?>" 
                                               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg font-semibold transition flex items-center gap-1">
                                                <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                                            </a>
                                            <a href="admin.php?delete_id=<?php echo $row['id']; ?>" 
                                               onclick="return confirm('Are you sure you want to delete this item?');" 
                                               class="bg-rose-50 hover:bg-rose-100 text-rose-600 px-3 py-1.5 rounded-lg font-semibold transition flex items-center gap-1">
                                                <i class="fa-solid fa-trash text-[10px]"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="p-8 text-center text-gray-400">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>