<?php
header('Content-Type: application/json');
require_once '../inc/functions.php';

$categoryId = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int)$_GET['category_id'] : null;
$rooms = filterRoomsByCategory($categoryId);
echo json_encode(['success' => true, 'rooms' => $rooms]);
