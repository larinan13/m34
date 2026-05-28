<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';

if (!isAdmin()) {
    redirect('../login.php');
}

$bookings = getPendingBookings();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/index.css">
</head>
<body class="container">

<header class="d-flex flex-wrap justify-content-between py-3">
    <a href="../index.php" class="d-flex align-items-center text-decoration-none">
        <span class="fs-4 mx-2 fw-medium">Светлые Сны - Админ-панель</span>
    </a>
    <a href="../logout.php" class="btn btn-outline-danger">Выйти</a>
</header>

<main>
    <div class="d-flex justify-content-between flex-wrap align-items-center my-3">
        <h1>Заявки на бронирование</h1>
        <span class="badge bg-primary">Ожидают модерации: <?= count($bookings) ?></span>
    </div>

    <div id="alertMessage" class="alert d-none" role="alert"></div>

    <div class="d-flex justify-content-around flex-wrap align-items-start gap-3" id="bookingsContainer">
        <?php if (empty($bookings)): ?>
            <div class="alert alert-info">Нет новых заявок на бронирование</div>
        <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="card admin-card" data-booking-id="<?= $booking['id'] ?>" style="width: 22rem; margin-bottom: 20px;">
                    <div class="card-body">
                        <h5><?= htmlspecialchars($booking['last_name']) ?> <?= htmlspecialchars($booking['first_name']) ?></h5>
                        <p class="mb-1"><strong>Телефон:</strong> <?= htmlspecialchars($booking['phone']) ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($booking['email']) ?></p>
                        <p class="mb-1"><strong>Номер:</strong> <?= htmlspecialchars($booking['room_title']) ?></p>
                        <p class="mb-1"><strong>Цена:</strong> <?= number_format($booking['price_per_person'], 0, '', ' ') ?> ₽/чел</p>
                        <ul class="list-group list-group-flush mt-2">
                            <li class="list-group-item">Дата заезда: <?= date('d.m.Y', strtotime($booking['check_in'])) ?></li>
                            <li class="list-group-item">Дата выезда: <?= date('d.m.Y', strtotime($booking['check_out'])) ?></li>
                        </ul>
                    </div>
                    <div class="d-grid gap-2 p-3">
                        <button class="btn btn-success approve-btn">Одобрить</button>
                        <button class="btn btn-danger delete-btn">Удалить</button>
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

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('.approve-btn').click(function() {
        let $card = $(this).closest('.admin-card');
        let bookingId = $card.data('booking-id');
        
        $.ajax({
            url: '../api/approve_booking.php',
            type: 'POST',
            data: { booking_id: bookingId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#alertMessage').removeClass('d-none alert-danger').addClass('alert-success');
                    $('#alertMessage').text('Заявка одобрена');
                    $card.fadeOut();
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    $('#alertMessage').removeClass('d-none alert-success').addClass('alert-danger');
                    $('#alertMessage').text(response.message || 'Ошибка при одобрении');
                }
            },
            error: function() {
                $('#alertMessage').removeClass('d-none').addClass('alert-danger');
                $('#alertMessage').text('Произошла ошибка');
            }
        });
    });
    
    $('.delete-btn').click(function() {
        if (!confirm('Вы уверены, что хотите удалить эту заявку?')) return;
        
        let $card = $(this).closest('.admin-card');
        let bookingId = $card.data('booking-id');
        
        $.ajax({
            url: '../api/delete_booking.php',
            type: 'POST',
            data: { booking_id: bookingId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#alertMessage').removeClass('d-none alert-danger').addClass('alert-success');
                    $('#alertMessage').text('Заявка удалена');
                    $card.fadeOut();
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    $('#alertMessage').removeClass('d-none alert-success').addClass('alert-danger');
                    $('#alertMessage').text(response.message || 'Ошибка при удалении');
                }
            },
            error: function() {
                $('#alertMessage').removeClass('d-none').addClass('alert-danger');
                $('#alertMessage').text('Произошла ошибка');
            }
        });
    });
});
</script>
</body>
</html>
