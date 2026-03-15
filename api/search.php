<?php
require_once dirname(__DIR__) . '/functions.php';
header('Content-Type: application/json');
$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) jsonResponse(['results'=>[]]);
$cars = getCars(['search' => $q], 8);
$results = array_map(fn($c) => [
    'id'=>$c['id'],'title'=>$c['title'],'slug'=>$c['slug'],'price'=>formatPrice($c['price']),
    'year'=>$c['year'],'brand'=>$c['brand_name'],'location'=>$c['location'],
    'image'=>$c['primary_image'] ? resolveUrl($c['primary_image']) : BASE_URL.'/assets/images/car-placeholder.jpg',
    'url'=>BASE_URL.'/car/'.$c['slug']
], $cars);
jsonResponse(['results'=>$results,'total'=>count($results)]);
