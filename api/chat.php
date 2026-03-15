<?php
require_once dirname(__DIR__) . '/functions.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$sessionId = $input['session_id'] ?? '';
$message = $input['message'] ?? '';
$name = $input['name'] ?? 'Visitor';
$email = $input['email'] ?? '';

if (!$sessionId || !$message) jsonResponse(['success'=>false,'message'=>'Missing data'], 400);

$result = sendChatMessage($sessionId, $message, 'visitor', $name, $email);
jsonResponse(['success'=>$result]);
