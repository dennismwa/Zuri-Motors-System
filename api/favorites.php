<?php
require_once dirname(__DIR__) . '/functions.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$carId = (int)($input['car_id'] ?? 0);

if (!$carId) jsonResponse(['success'=>false,'message'=>'Invalid car ID'], 400);
if (!Auth::check()) jsonResponse(['success'=>false,'message'=>'Please login to save favorites.'], 401);

jsonResponse(toggleFavorite($carId, Auth::id()));
