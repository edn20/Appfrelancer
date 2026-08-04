<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Freelance Manager EDN - <?php echo $titulo ?? ''; ?></title>

    <!-- Fuente -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap"
        rel="stylesheet">

    <!-- Iconos -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS principal -->
    <link rel="stylesheet" href="/build/css/app.css">
</head>

<body>
    <?php echo $contenido; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- JS principal -->
    <script src="/build/js/bundle.min.js" defer></script>
</body>

</html>