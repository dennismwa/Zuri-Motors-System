<?php
require_once dirname(__DIR__) . '/functions.php';

// Handle GET requests (clear, remove via URL)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    if ($action === 'clear') { clearCompare(); Flash::success('Compare list cleared.'); redirect(BASE_URL . '/compare'); }
    if ($action === 'remove' && isset($_GET['car_id'])) { removeFromCompare((int)$_GET['car_id']); Flash::success('Removed.'); redirect(BASE_URL . '/compare'); }
    redirect(BASE_URL . '/compare');
}

// Handle POST (AJAX)
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $input['action'] ?? '';
$carId = (int)($input['car_id'] ?? 0);

if ($action === 'add' && $carId) { jsonResponse(addToCompare($carId)); }
if ($action === 'remove' && $carId) { removeFromCompare($carId); jsonResponse(['success'=>true]); }
if ($action === 'clear') { clearCompare(); jsonResponse(['success'=>true]); }

jsonResponse(['success'=>false,'message'=>'Invalid action'], 400);
