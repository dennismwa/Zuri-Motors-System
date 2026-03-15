<?php
// api/inquiry.php - Handle inquiry submissions
require_once dirname(__DIR__) . '/functions.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { jsonResponse(['success'=>false,'message'=>'Method not allowed'], 405); }

$data = $_POST;
if (empty($data['name']) || empty($data['phone'])) {
    jsonResponse(['success'=>false,'message'=>'Name and phone are required.'], 400);
}

$result = saveInquiry($data);
jsonResponse($result, $result['success'] ? 200 : 500);
