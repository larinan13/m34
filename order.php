<?php
require_once 'inc/config.php';
require_once 'inc/functions.php';

$roomId = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;
if (!$roomId) {
    redirect('index.php');
}

$stmt = $pdo->prepare("SELECT r.*, c.name as category_name FROM rooms r JOIN categories c ON r.category_id = c.id WHERE r.id = ?");
$stmt->execute([$roomId]);
$room = $stmt->fetch();

if (!$room) {
    redirect('index.php');
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Бронирование - <?= htmlspecialchars($room['category_name']) ?></title>
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
    </ul>
</header>

<main>
    <div class="d-flex justify-content-between flex-wrap align-items-center my-3">
        <h1>Бронирование номера "<?= htmlspecialchars($room['category_name']) ?>"</h1>
    </div>

    <div id="alertMessage" class="alert d-none" role="alert"></div>

    <form id="bookingForm" class="row g-3 my-2" novalidate>
        <input type="hidden" name="room_id" value="<?= $roomId ?>">
        
        <div class="col-md-6">
            <label for="first_name" class="form-label">Имя</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label for="last_name" class="form-label">Фамилия</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label for="phone" class="form-label">Телефон</label>
            <input type="tel" class="form-control" id="phone" name="phone" placeholder="+7 (___)-___-__-__" required>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label for="check_in" class="form-label">Дата заезда</label>
            <input type="date" class="form-control" id="check_in" name="check_in" required>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label for="check_out" class="form-label">Дата выезда</label>
            <input type="date" class="form-control" id="check_out" name="check_out" required>
            <div class="invalid-feedback"></div>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">Отправить заявку</button>
            <a href="index.php" class="btn btn-secondary">Вернуться к каталогу</a>
        </div>
    </form>
</main>

<footer class="py-2 my-2">
    <ul class="nav justify-content-between align-items-center">
        <li class="nav-item"><a href="#" class="nav-link text-body-secondary">ул. г.Москва, ул. Ивовая, 48</a></li>
        <li class="nav-item"><a href="#" class="nav-link text-body-secondary">Время работы: Пн-Пт, с 8:00-17:00</a></li>
        <li class="nav-item"><a href="tel:88005553535" class="nav-link text-body-secondary">тел. 8 (800) 555-35-35</a></li>
        <li class="nav-item"><a href="mailto:обращения@СветлыеСны.рф" class="nav-link text-body-secondary">Email: обращения@СветлыеСны.рф</a></li>
    </ul>
</footer>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/jquery-3.7.1.min.js"></script>
<script src="js/jquery.inputmask.min.js"></script>
<script>
$(document).ready(function() {
    $('#phone').inputmask('+7 (999) 999-99-99');
    
    $('#bookingForm').on('submit', function(e) {
        e.preventDefault();
        
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#alertMessage').addClass('d-none');
        
        $.ajax({
            url: 'api/create_booking.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#alertMessage').removeClass('d-none alert-danger').addClass('alert-success');
                    $('#alertMessage').text(response.message);
                    $('#bookingForm')[0].reset();
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    $('#alertMessage').removeClass('d-none alert-success').addClass('alert-danger');
                    $('#alertMessage').text('Пожалуйста, исправьте ошибки в форме');
                    
                    if (response.errors) {
                        $.each(response.errors, function(field, message) {
                            let $field = $('#' + field);
                            $field.addClass('is-invalid');
                            $field.siblings('.invalid-feedback').text(message);
                        });
                    }
                }
            },
            error: function() {
                $('#alertMessage').removeClass('d-none').addClass('alert-danger');
                $('#alertMessage').text('Произошла ошибка при отправке. Попробуйте позже.');
            }
        });
    });
});
</script>
</body>
</html>
