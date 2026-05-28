<?php
header('Content-Type: application/json');
require_once '../inc/functions.php';

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$rooms = getRandomRooms($limit);
echo json_encode(['success' => true, 'rooms' => $rooms]);
