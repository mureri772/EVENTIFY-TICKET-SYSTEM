<?php
require_once 'includes/header.php';


$input = file_get_contents('php://input');
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Empty request body']);
    exit;
}

$data = json_decode($input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON payload']);
    exit;
}

$callback = $data['Body']['stkCallback'] ?? null;
if (!$callback || !is_array($callback)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing stkCallback data']);
    exit;
}

$merchantRequestID = $callback['MerchantRequestID'] ?? null;
$checkoutRequestID = $callback['CheckoutRequestID'] ?? null;
$resultCode = $callback['ResultCode'] ?? null;
$resultDesc = $callback['ResultDesc'] ?? null;

$receiptNumber = null;
if (isset($callback['CallbackMetadata']['Item']) && is_array($callback['CallbackMetadata']['Item'])) {
    foreach ($callback['CallbackMetadata']['Item'] as $item) {
        if (($item['Name'] ?? '') === 'MpesaReceiptNumber') {
            $receiptNumber = $item['Value'];
            break;
        }
    }
}

$status = ($resultCode === 0) ? 'paid' : 'failed';
$updated = false;

if ($checkoutRequestID) {
    $updated = updateTicketStatusByCheckoutId($checkoutRequestID, $status, $receiptNumber);
}

if (!$updated && $merchantRequestID) {
    $updated = updateTicketStatusByMerchantId($merchantRequestID, $status, $receiptNumber);
}

if (!$updated) {
    error_log('MPesa callback could not find ticket for CheckoutRequestID=' . $checkoutRequestID . ' MerchantRequestID=' . $merchantRequestID);
}

header('Content-Type: application/json');
echo json_encode(['success' => true]);
exit;
