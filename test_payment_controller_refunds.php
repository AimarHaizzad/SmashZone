<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Refund;
use App\Models\User;

echo "=== TESTING PAYMENT CONTROLLER REFUNDS ===\n";

// Get owner user (assuming user 1 is owner)
$owner = User::find(1);
if (!$owner) {
    echo "Owner user not found\n";
    exit;
}

echo "Testing for owner: {$owner->name} (ID: {$owner->id})\n";

// Test the PaymentController refunds query
try {
    $refunds = Refund::with(['user', 'booking.court', 'payment.booking.court'])
        ->orderBy('created_at', 'desc')
        ->get();
    
    echo "✅ SUCCESS: PaymentController refunds query works!\n";
    echo "Found {$refunds->count()} refunds for owner\n";
    
    foreach ($refunds as $refund) {
        echo "  - Refund ID: {$refund->id}\n";
        echo "    Status: {$refund->status}\n";
        echo "    Amount: {$refund->formatted_amount}\n";
        echo "    User: " . ($refund->user ? $refund->user->name : 'NULL') . "\n";
        echo "    Booking: " . ($refund->booking ? "Exists (ID: {$refund->booking->id})" : "NULL") . "\n";
        echo "    Payment: " . ($refund->payment ? "Exists (ID: {$refund->payment->id})" : "NULL") . "\n";
        echo "    Created: {$refund->created_at}\n";
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== DONE ===\n";
