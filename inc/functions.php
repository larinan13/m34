<?php
// inc/functions.php
require_once __DIR__ . '/config.php';

function getRandomRooms($limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT r.*, c.name as category_name 
        FROM rooms r 
        JOIN categories c ON r.category_id = c.id 
        ORDER BY RANDOM() 
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getCategories() {
    global $pdo;
    return $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
}

function filterRoomsByCategory($categoryId = null) {
    global $pdo;
    if ($categoryId) {
        $stmt = $pdo->prepare("
            SELECT r.*, c.name as category_name 
            FROM rooms r 
            JOIN categories c ON r.category_id = c.id 
            WHERE r.category_id = ?
        ");
        $stmt->execute([$categoryId]);
    } else {
        $stmt = $pdo->prepare("
            SELECT r.*, c.name as category_name 
            FROM rooms r 
            JOIN categories c ON r.category_id = c.id 
        ");
        $stmt->execute();
    }
    return $stmt->fetchAll();
}

function getPendingBookings() {
    global $pdo;
    return $pdo->query("
        SELECT b.*, r.title as room_title, r.price_per_person 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        WHERE b.status = 'pending' 
        ORDER BY b.created_at DESC
    ")->fetchAll();
}

function approveBooking($bookingId) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'approved' WHERE id = ?");
    return $stmt->execute([$bookingId]);
}

function deleteBooking($bookingId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    return $stmt->execute([$bookingId]);
}

function createBooking($data) {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO bookings (room_id, first_name, last_name, phone, email, check_in, check_out, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    return $stmt->execute([
        $data['room_id'],
        $data['first_name'],
        $data['last_name'],
        $data['phone'],
        $data['email'],
        $data['check_in'],
        $data['check_out']
    ]);
}

function validateBookingData($data) {
    $errors = [];
    
    if (empty($data['first_name']) || mb_strlen($data['first_name']) < 2) {
        $errors['first_name'] = 'Имя должно содержать минимум 2 символа';
    }
    
    if (empty($data['last_name']) || mb_strlen($data['last_name']) < 2) {
        $errors['last_name'] = 'Фамилия должна содержать минимум 2 символа';
    }
    
    if (empty($data['phone']) || !preg_match('/^\+?[0-9\s\-\(\)]{10,}$/', $data['phone'])) {
        $errors['phone'] = 'Введите корректный номер телефона';
    }
    
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите корректный email адрес';
    }
    
    if (empty($data['check_in']) || !strtotime($data['check_in'])) {
        $errors['check_in'] = 'Выберите дату заезда';
    }
    
    if (empty($data['check_out']) || !strtotime($data['check_out'])) {
        $errors['check_out'] = 'Выберите дату выезда';
    }
    
    if (strtotime($data['check_in']) >= strtotime($data['check_out'])) {
        $errors['check_out'] = 'Дата выезда должна быть позже даты заезда';
    }
    
    if (strtotime($data['check_in']) < strtotime(date('Y-m-d'))) {
        $errors['check_in'] = 'Дата заезда не может быть в прошлом';
    }
    
    return $errors;
}
