<?php
$paymentId = $data['paymentId'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Payment Successful</title>
  <!-- Tailwind CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-100 flex items-center justify-center min-h-screen">
  <div class="bg-white shadow-lg rounded-lg p-8 max-w-md text-center">
    <h1 class="text-3xl font-bold text-green-600 mb-4">Payment Successful!</h1>
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Payment ID: <?php echo $paymentId; ?></h2>
    <p class="text-gray-700 mb-6">
      Thank you for your purchase. Your payment has been processed successfully.
    </p>
    <a href="/" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded">
      Return to Home
    </a>
  </div>
</body>
</html>
