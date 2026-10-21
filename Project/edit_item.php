<?php
session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: pharma.php");
    exit();
}

include 'config.php';


if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $image = trim($_POST['image']); 

  
    $stmt = $conn->prepare("UPDATE items SET name = ?, category = ?, price = ?, quantity = ?, image = ? WHERE id = ?");
    $stmt->bind_param("ssdisi", $name, $category, $price, $quantity, $image, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully!'); window.location.href='admin.php';</script>";
        exit();
    } else {
        $error = "Failed to update item: " . $conn->error;
    }
}


$stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Item not found!'); window.location.href='admin.php';</script>";
    exit();
}

$item = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 font-sans min-h-screen p-6">

    <div class="max-w-xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-bold text-gray-800">Edit Product</h1>
            <a href="admin.php" class="text-xs text-gray-500 hover:text-gray-800 font-semibold">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-rose-50 text-rose-600 p-3 rounded-xl text-xs mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4 text-xs">
            <!-- Product Name -->
            <div>
                <label class="block font-semibold mb-1 text-gray-700">Product Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-blue-500">
            </div>

            <!-- Category -->
            <div>
                <label class="block font-semibold mb-1 text-gray-700">Category</label>
                <input type="text" name="category" value="<?php echo htmlspecialchars($item['category'] ?? ''); ?>" required
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-blue-500">
            </div>

            <!-- Price & Quantity -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">Price (MMK)</label>
                    <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($item['price']); ?>" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">Quantity (Stock)</label>
                    <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity'] ?? 0); ?>" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <!-- Image URL -->
            <div>
                <label class="block font-semibold mb-1 text-gray-700">Image URL (Unsplash Link)</label>
                <input type="text" name="image" value="<?php echo htmlspecialchars($item['image'] ?? ''); ?>" required
                       placeholder="https://images.unsplash.com/..."
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-blue-500">
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition shadow-sm">
                    Update Product
                </button>
            </div>
        </form>
    </div>

</body>
</html>