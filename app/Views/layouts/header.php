<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'PinkSecret Messenger') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        blush: '#ff4d9d',
                        bubble: '#ffd6ea'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-pink-50 min-h-screen text-gray-800">
