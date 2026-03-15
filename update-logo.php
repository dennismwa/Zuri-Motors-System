<?php
/**
 * One-time script: Set site logo path in settings table.
 * Run once via: php update-logo.php
 * Or run in browser (remove after use).
 */
require_once __DIR__ . '/config.php';

$logoPath = 'assets/images/ZuriMotors -logo.png';
if (Settings::set('logo', $logoPath, 'general')) {
    Settings::clearCache();
    echo "Logo set to: " . htmlspecialchars($logoPath) . "\n";
} else {
    echo "Failed to update logo.\n";
}
