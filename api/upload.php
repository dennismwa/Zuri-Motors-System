<?php
require_once dirname(__DIR__) . '/functions.php';
header('Content-Type: application/json');

if (!Auth::check()) jsonResponse(['success'=>false,'message'=>'Unauthorized'], 401);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success'=>false,'message'=>'Method not allowed'], 405);

if (empty($_FILES['file'])) jsonResponse(['success'=>false,'message'=>'No file uploaded'], 400);

$type = $_POST['type'] ?? 'car';
$destinations = ['car'=>CARS_UPLOAD_PATH, 'avatar'=>AVATARS_UPLOAD_PATH, 'logo'=>LOGOS_UPLOAD_PATH];
$dest = $destinations[$type] ?? UPLOAD_PATH;

$result = ImageUpload::upload($_FILES['file'], $dest, $type);
jsonResponse($result, $result['success'] ? 200 : 400);
