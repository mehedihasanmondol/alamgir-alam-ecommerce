<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Livewire Components...\n\n";

// Test UserStatusToggle
try {
    $component = new App\Livewire\User\UserStatusToggle();
    echo "✓ UserStatusToggle: OK\n";
} catch (Exception $e) {
    echo "✗ UserStatusToggle: " . $e->getMessage() . "\n";
}

// Test UserSearch
try {
    $component = new App\Livewire\User\UserSearch();
    echo "✓ UserSearch: OK\n";
} catch (Exception $e) {
    echo "✗ UserSearch: " . $e->getMessage() . "\n";
}

// Test GlobalUserSearch
try {
    $component = new App\Livewire\Admin\GlobalUserSearch();
    echo "✓ GlobalUserSearch: OK\n";
} catch (Exception $e) {
    echo "✗ GlobalUserSearch: " . $e->getMessage() . "\n";
}

echo "\nLivewire component test complete!\n";
