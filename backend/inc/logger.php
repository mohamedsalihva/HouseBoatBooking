<?php
/**
 * Simple logging utility for debugging payment issues
 */
class PaymentLogger {
    private static $log_file = __DIR__ . '/../../logs/payment.log';
    
    public static function log($message, $data = null) {
        // Create logs directory if it doesn't exist
        $log_dir = dirname(self::$log_file);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[{$timestamp}] {$message}";
        
        if ($data !== null) {
            $log_entry .= " | Data: " . json_encode($data);
        }
        
        $log_entry .= PHP_EOL;
        
        file_put_contents(self::$log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    public static function logPaymentAttempt($booking_data, $payment_data = null) {
        self::log("Payment attempt for booking", [
            'boat_id' => $booking_data['boat_id'] ?? null,
            'user_id' => $booking_data['user_id'] ?? null,
            'total_price' => $booking_data['total_price'] ?? null,
            'payment_method' => $booking_data['payment_method'] ?? null,
            'payment_data' => $payment_data
        ]);
    }
    
    public static function logPaymentSuccess($booking_id, $transaction_id) {
        self::log("Payment successful", [
            'booking_id' => $booking_id,
            'transaction_id' => $transaction_id
        ]);
    }
    
    public static function logPaymentError($error, $booking_data = null) {
        self::log("Payment error: {$error}", $booking_data);
    }
}
?>