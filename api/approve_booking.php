<?php
header('Content-Type: application/json');
require_once '../inc/functions.php';

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
    exit;
}

$bookingId = (int)$_POST['booking_id'];
if (approveBooking($bookingId)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при одобрении']);
}
