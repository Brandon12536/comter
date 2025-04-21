<?php
header('Content-Type: text/plain');

function generateSecurePassword() {
    $length = 12;
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '0123456789';
    $special = '!@#$%^&*()_+';
    
    // Ensure at least one of each type
    $password = [
        $lowercase[random_int(0, strlen($lowercase) - 1)],
        $uppercase[random_int(0, strlen($uppercase) - 1)],
        $numbers[random_int(0, strlen($numbers) - 1)],
        $special[random_int(0, strlen($special) - 1)]
    ];
    
    // Fill the rest randomly
    $all = $lowercase . $uppercase . $numbers . $special;
    for ($i = count($password); $i < $length; $i++) {
        $password[] = $all[random_int(0, strlen($all) - 1)];
    }
    
    // Shuffle to avoid predictable pattern
    shuffle($password);
    
    return implode('', $password);
}

echo generateSecurePassword();
