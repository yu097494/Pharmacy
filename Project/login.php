<?php
session_start();
include 'config.php';

$login_error = "";


if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

      
        if ($user['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: pharma.php");
        }
        exit();
    } else {
        $login_error = "Error: Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pharmacy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-lg w-96 border border-gray-100">
        <h2 class="text-xl font-bold mb-6 text-center text-rose-500">Login to Pharmacy</h2>
        
        <?php if (!empty($login_error)): ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-600 px-3 py-2 rounded-xl text-xs font-bold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-sm"></i>
                <span><?php echo $login_error; ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label class="text-xs font-semibold text-gray-600 block mb-1">Email</label>
                <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required class="w-full border border-gray-300 p-2.5 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 block mb-1">Password</label>
                <input type="password" name="password" required class="w-full border border-gray-300 p-2.5 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-rose-500">
            </div>
            <button type="submit" name="login" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-bold py-2.5 rounded-xl text-xs transition shadow-md">
                Sign In
            </button>
        </form>

    
        <p class="text-center text-xs text-gray-500 mt-6">
            Don't have an account? <a href="register.php" class="text-rose-500 font-bold hover:underline">Register here</a>
        </p>
    </div>

</body>
</html>