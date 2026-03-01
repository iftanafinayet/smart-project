<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enterprise Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 flex">
    <?= $this->include('layouts/sidebar') ?>

    <main class="flex-1 p-8">
        <?= $this->renderSection('content') ?>
    </main>
</body>
</html>