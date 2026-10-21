<?php
session_start();
include 'config.php';

$error = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);
    $role = 'user'; 

  
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
       
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "An account with this email already exists.";
        } else {
           
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $password, $role);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;

                header("Location: pharma.php");
                exit();
            } else {
                $error = "Could not register user. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - City Pharmacy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-md w-96 border">
        <h2 class="text-xl font-bold mb-6 text-center text-rose-500">Register New Account</h2>
        
        <!-- PHP Server-Side Error Display -->
        <?php if (!empty($error)): ?>
            <p class="text-red-500 text-xs mb-4 text-center font-bold bg-red-50 p-2 rounded border border-red-200"><?php echo $error; ?></p>
        <?php endif; ?>

        <!-- JS Validation Message Container -->
        <div id="msg"></div>

        <form id="regForm" action="register.php" method="POST" class="space-y-3">
            <div>
                <label class="text-xs font-semibold text-gray-600 block mb-1">Username</label>
                <input type="text" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required class="w-full border border-gray-300 p-2.5 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-rose-500">
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-600 block mb-1">Email</label>
                <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required class="w-full border border-gray-300 p-2.5 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-rose-500">
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-600 block mb-1">Password</label>
                <input type="password" id="password" name="password" required class="w-full border border-gray-300 p-2.5 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-rose-500">
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-600 block mb-1">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required class="w-full border border-gray-300 p-2.5 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-rose-500">
            </div>

            <button type="submit" name="register" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-bold py-2.5 rounded-xl text-xs transition shadow-md">
                Register
            </button>
        </form>

        <p class="text-center text-xs text-gray-500 mt-4">
            Already have an account? <a href="login.php" class="text-rose-500 font-bold hover:underline">Login here</a>
        </p>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("#regForm");
        const password = document.querySelector("#password");
        const confirmPassword = document.querySelector("#confirmPassword");
        const message = document.querySelector("#msg");

        if (form) {
            form.addEventListener("submit", function (event) {
                const errors = [];

                if (password.value.length < 6) {
                    errors.push("Password must be at least 6 characters long.");
                }

                if (password.value !== confirmPassword.value) {
                    errors.push("Passwords do not match.");
                }

                if (errors.length > 0) {
                    event.preventDefault(); 
                    message.className = "text-xs font-bold text-center mb-3 text-rose-500 bg-rose-50 p-2 rounded-lg border border-rose-200";
                    message.textContent = errors.join(" ");
                }
            });
        }
    });
    </script>
</body>
</html>