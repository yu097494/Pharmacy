<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Admin - City Pharmacy</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-6 min-h-screen font-sans flex items-center justify-center">

    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
        
        <!-- Header -->
        <div class="flex justify-between items-center pb-4 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-headset text-rose-500"></i> Contact Admin
            </h1>
            <a href="pharma.php" class="text-xs font-semibold text-gray-500 hover:text-gray-800 transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to Store
            </a>
        </div>

        <!-- Contact Details -->
        <div class="space-y-4 text-xs text-gray-700">
            <p class="text-gray-500">Welcome to City Pharmacy! If you have any questions or need assistance, feel free to contact us using the information below.</p>

            <!-- Phone -->
            <div class="flex items-center gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <div>
                    <span class="block text-gray-400 font-semibold">Phone Number</span>
                    <span class="font-bold text-gray-800 text-sm">080 1111 1111</span>
                </div>
            </div>

            <!-- Email -->
            <div class="flex items-center gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div>
                    <span class="block text-gray-400 font-semibold">Email Address</span>
                    <span class="font-bold text-gray-800 text-sm">admin@citypharmacy.com</span>
                </div>
            </div>

            <!-- Telegram / Viber -->
            <div class="flex items-center gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600">
                    <i class="fa-brands fa-telegram"></i>
                </div>
                <div>
                    <span class="block text-gray-400 font-semibold">Telegram / Viber</span>
                    <span class="font-bold text-gray-800 text-sm">@CityPharmacyAdmin</span>
                </div>
            </div>
        </div>

        <!-- Return Button -->
        <a href="pharma.php" class="block text-center w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3.5 rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
            Return to Store
        </a>

    </div>

</body>
</html>