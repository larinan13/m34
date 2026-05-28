<?php
require_once 'inc/config.php';
require_once 'inc/functions.php';

$selectedCategory = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;
$rooms = filterRoomsByCategory($selectedCategory);
$categories = getCategories();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Светлые Сны - Каталог номеров</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body class="container">

<header class="d-flex flex-wrap justify-content-center py-3">
    <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
        <span class="fs-4 mx-2 fw-medium">Светлые Сны</span>
    </a>
    <ul class="nav">
        <li class="nav-item"><a href="#" class="nav-link">Приезжайте как гости, уезжайте как друзья!</a></li>
        <?php if (isAdmin()): ?>
            <li class="nav-item"><a href="admin/index.php" class="nav-link text-primary fw-bold">Админ-панель</a></li>
            <li class="nav-item"><a href="logout.php" class="nav-link text-danger">Выход</a></li>
        <?php else: ?>
            <li class="nav-item"><a href="login.php" class="nav-link">Вход для админа</a></li>
        <?php endif; ?>
    </ul>
</header>

<main>
    <div class="d-flex justify-content-between flex-wrap align-items-center my-3">
        <h1>Каталог номеров</h1>
        <form method="GET" action="index.php" class="d-flex gap-2 align-items-center">
            <select name="category" class="form-select w-auto">
                <option value="">Все категории</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $selectedCategory == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Применить</button>
            <a href="index.php" class="btn btn-danger">Сбросить фильтр</a>
        </form>
    </div>

    <div class="d-flex justify-content-around flex-wrap align-items-start gap-3">
        <?php if (empty($rooms)): ?>
            <div class="alert alert-warning text-center w-100">Нет номеров, соответствующих выбранной категории.</div>
        <?php else: ?>
            <?php foreach ($rooms as $room): 
                $features = json_decode($room['features'], true);
            ?>
                <div class="card" style="width: 22rem; margin-bottom: 20px;">
                    <img src="img/<?= htmlspecialchars($room['image']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?= htmlspecialchars($room['category_name']) ?>">
                    <div class="card-body">
                        <h3>Категория: <?= htmlspecialchars($room['category_name']) ?></h3>
                        <h5>Цена: <?= number_format($room['price_per_person'], 0, '', ' ') ?> ₽ / чел</h5>
                        <h5>Характеристики:</h5>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($features as $feature): ?>
                                <li class="list-group-item"><?= htmlspecialchars($feature) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="d-grid gap-2 p-3">
                        <a href="order.php?room_id=<?= $room['id'] ?>" class="btn btn-success">Забронировать</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<footer class="py-2 my-2 mt-4 border-top">
    <ul class="nav justify-content-between align-items-center">
        <li class="nav-item"><a href="#" class="nav-link text-body-secondary">ул. г.Москва, ул. Ивовая, 48</a></li>
        <li class="nav-item"><a href="#" class="nav-link text-body-secondary">Время работы: Пн-Пт, с 8:00-17:00</a></li>
        <li class="nav-item"><a href="tel:88005553535" class="nav-link text-body-secondary">тел. 8 (800) 555-35-35</a></li>
        <li class="nav-item"><a href="mailto:обращения@СветлыеСны.рф" class="nav-link text-body-secondary">Email: обращения@СветлыеСны.рф</a></li>
    </ul>
</footer>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
