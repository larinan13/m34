<?php
header('Content-Type: application/json');
require_once '../inc/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'errors' => ['Метод не поддерживается']]);
    exit;
}

$data = [
    'room_id' => (int)$_POST['room_id'],
    'first_name' => sanitizeInput($_POST['first_name']),
    'last_name' => sanitizeInput($_POST['last_name']),
    'phone' => sanitizeInput($_POST['phone']),
    'email' => sanitizeInput($_POST['email']),
    'check_in' => $_POST['check_in'],
    'check_out' => $_POST['check_out']
];

$errors = validateBookingData($data);

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

if (createBooking($data)) {
    echo json_encode(['success' => true, 'message' => 'Заявка успешно отправлена']);
} else {
    echo json_encode(['success' => false, 'errors' => ['database' => 'Ошибка при сохранении заявки']]);
}
