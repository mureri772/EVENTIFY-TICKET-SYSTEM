<?php

require_once 'includes/header.php';

$eventId = intval($_GET['id'] ?? 0);
$event = $eventId > 0 ? getEventById($eventId) : null;

if (!$event) {
    setFlash('error', 'Event not found.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mpesa_pay'])) {
    if (!isLoggedIn()) {
        setFlash('error', 'Please login before making a payment.');
        header('Location: login.php');
        exit;
    }

    $quantity = max(1, min(10, intval($_POST['quantity'] ?? 1)));
    $phone = formatMpesaPhone(trim($_POST['phone_number'] ?? ''));

    if (!$phone) {
        setFlash('error', 'Enter a valid phone number in Kenyan format, for example 254712345678.');
        header("Location: event.php?id={$eventId}");
        exit;
    }

    $amount = $event['price'] * $quantity;
    $ticketCode = generateTicketCode();
    $description = 'Tickets for ' . $event['title'];

    if (!insertPendingTicket($_SESSION['user_id'], $event['id'], $quantity, $amount, $ticketCode)) {
        setFlash('error', 'Unable to initialize ticket booking. Please try again later.');
        header("Location: event.php?id={$eventId}");
        exit;
    }

    $result = initiateMpesaStkPush($phone, $amount, $ticketCode, $description);

    if ($result['success']) {
        if (!empty($result['checkout_request_id']) || !empty($result['merchant_request_id'])) {
            updateTicketStkIdentifiers($ticketCode, $result['checkout_request_id'] ?? null, $result['merchant_request_id'] ?? null);
        }
        setFlash('success', 'STK Push sent. Check your phone to complete payment.');
    } else {
        updateTicketStatusByTicketCode($ticketCode, 'failed');
        setFlash('error', 'MPesa payment failed: ' . $result['message']);
    }

    header("Location: event.php?id={$eventId}");
    exit;
}

function formatMpesaPhone($phone) {
    $phone = preg_replace('/\D+/', '', $phone);
    if (strlen($phone) === 10 && strpos($phone, '07') === 0) {
        return '254' . substr($phone, 1);
    }
    if (strlen($phone) === 12 && strpos($phone, '254') === 0) {
        return $phone;
    }
    return '';
}

function generateTicketCode() {
    return 'EVT' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
}

function ensureMpesaTicketColumns() {
    $db = getDB();
    if (!$db) {
        return;
    }

    try {
        $stmt = $db->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tickets' AND COLUMN_NAME IN ('mpesa_checkout_request_id','mpesa_merchant_request_id','mpesa_receipt_number')");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!in_array('mpesa_checkout_request_id', $columns, true)) {
            $db->exec("ALTER TABLE tickets ADD COLUMN mpesa_checkout_request_id VARCHAR(100) DEFAULT NULL");
        }
        if (!in_array('mpesa_merchant_request_id', $columns, true)) {
            $db->exec("ALTER TABLE tickets ADD COLUMN mpesa_merchant_request_id VARCHAR(100) DEFAULT NULL");
        }
        if (!in_array('mpesa_receipt_number', $columns, true)) {
            $db->exec("ALTER TABLE tickets ADD COLUMN mpesa_receipt_number VARCHAR(100) DEFAULT NULL");
        }
    } catch (PDOException $e) {
       
    }
}

function insertPendingTicket($userId, $eventId, $quantity, $totalPrice, $ticketCode) {
    $db = getDB();
    if (!$db) {
        return false;
    }
    ensureMpesaTicketColumns();

    try {
        $stmt = $db->prepare("INSERT INTO tickets (user_id, event_id, quantity, total_price, ticket_code, payment_status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        return $stmt->execute([$userId, $eventId, $quantity, $totalPrice, $ticketCode]);
    } catch (PDOException $e) {
        return false;
    }
}

function updateTicketStkIdentifiers($ticketCode, $checkoutRequestId, $merchantRequestId) {
    $db = getDB();
    if (!$db) {
        return false;
    }
    try {
        $stmt = $db->prepare("UPDATE tickets SET mpesa_checkout_request_id = ?, mpesa_merchant_request_id = ? WHERE ticket_code = ?");
        return $stmt->execute([$checkoutRequestId, $merchantRequestId, $ticketCode]);
    } catch (PDOException $e) {
        return false;
    }
}

function updateTicketStatusByTicketCode($ticketCode, $status) {
    $db = getDB();
    if (!$db) {
        return false;
    }
    try {
        $stmt = $db->prepare("UPDATE tickets SET payment_status = ? WHERE ticket_code = ?");
        return $stmt->execute([$status, $ticketCode]);
    } catch (PDOException $e) {
        return false;
    }
}

function updateTicketStatusByCheckoutId($checkoutRequestId, $status, $receiptNumber = null) {
    $db = getDB();
    if (!$db) {
        return false;
    }
    try {
        $stmt = $db->prepare("UPDATE tickets SET payment_status = ?, mpesa_receipt_number = ? WHERE mpesa_checkout_request_id = ?");
        return $stmt->execute([$status, $receiptNumber, $checkoutRequestId]);
    } catch (PDOException $e) {
        return false;
    }
}

function getMpesaConfig() {
    return [
        'consumer_key' => 'PTcfOiuvZyveUJeQVWwhKbohdyg1y4MAo38HcSu3jDLUNwWm',
        'consumer_secret' => 'ju5aIaPB0dxS5lLJv0gLd3WUE0IgFuoA08JBJBW4vXansLQA72ATcQ01oXnuwbbq',
        'short_code' => '174379',
        'passkey' => 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919',
        'callback_url' => 'https://webhook.site/24752df6-015a-4861-ad1d-eb3ea85171cf',
        'sandbox_url' => 'https://sandbox.safaricom.co.ke'
    ];
}

function validateMpesaConfig(array $config) {
    if (empty($config['consumer_key']) || empty($config['consumer_secret'])) {
        return ['success' => false, 'message' => 'M-Pesa sandbox consumer key/secret are missing. Add them in getMpesaConfig() or your config file.'];
    }
    if (empty($config['passkey'])) {
        return ['success' => false, 'message' => 'M-Pesa sandbox passkey is missing. Add it in getMpesaConfig() or your config file.'];
    }
    if (empty($config['callback_url']) || strpos($config['callback_url'], 'yourdomain.com') !== false) {
        return ['success' => false, 'message' => 'Set a valid M-Pesa callback URL in getMpesaConfig() before using the payment gateway.'];
    }
    return ['success' => true];
}

function getMpesaAccessToken($consumerKey, $consumerSecret) {
    if (empty($consumerKey) || empty($consumerSecret)) {
        return ['success' => false, 'message' => 'M-Pesa consumer key or secret is missing. Please configure your sandbox credentials.'];
    }

    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $ch = curl_init($url);
    if (!function_exists('curl_init') || !$ch) {
        return ['success' => false, 'message' => 'cURL is not available or could not be initialized.'];
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        return ['success' => false, 'message' => 'cURL error while requesting token: ' . $curlError];
    }

    $data = json_decode($response, true);
    $body = is_array($data) ? json_encode($data) : $response;

    if ($httpStatus !== 200) {
        return ['success' => false, 'message' => 'Token request HTTP ' . $httpStatus . ': ' . ($body ?: 'Check your credentials and sandbox account configuration.')] ;
    }

    if (!is_array($data) || !isset($data['access_token'])) {
        return ['success' => false, 'message' => 'Invalid token response: ' . ($body ?: 'No response body returned. Verify your credentials.')];
    }

    return ['success' => true, 'access_token' => $data['access_token']];
}

function getMpesaPassword($shortCode, $passkey, $timestamp) {
    return base64_encode($shortCode . $passkey . $timestamp);
}

function initiateMpesaStkPush($phone, $amount, $accountReference, $transactionDesc) {
    $config = getMpesaConfig();
    $validate = validateMpesaConfig($config);
    if (!$validate['success']) {
        return ['success' => false, 'message' => $validate['message']];
    }

    $consumerKey = $config['consumer_key'];
    $consumerSecret = $config['consumer_secret'];
    $shortCode = $config['short_code'];
    $passkey = $config['passkey'];
    $callbackUrl = $config['callback_url'];
    $timestamp = gmdate('YmdHis');

    $tokenResult = getMpesaAccessToken($consumerKey, $consumerSecret);
    if (!$tokenResult['success']) {
        return $tokenResult;
    }

    $payload = [
        'BusinessShortCode' => $shortCode,
        'Password' => getMpesaPassword($shortCode, $passkey, $timestamp),
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => $shortCode,
        'PhoneNumber' => $phone,
        'CallBackURL' => $callbackUrl,
        'AccountReference' => $accountReference,
        'TransactionDesc' => $transactionDesc
    ];

    $ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
    if (!$ch) {
        return ['success' => false, 'message' => 'Unable to initialize cURL for STK Push.'];
    }
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $tokenResult['access_token']
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ['success' => false, 'message' => 'cURL error during STK Push: ' . $curlError];
    }

    $result = json_decode($response, true);
    if (!is_array($result)) {
        return ['success' => false, 'message' => 'Invalid STK Push response.'];
    }

    if (isset($result['ResponseCode']) && $result['ResponseCode'] === '0') {
        return ['success' => true, 'message' => 'STK push successfully initiated. CheckoutRequestID: ' . ($result['CheckoutRequestID'] ?? 'unknown')];
    }

    return ['success' => false, 'message' => $result['errorMessage'] ?? ($result['errorMessage'] ?? json_encode($result))];
}

$userLikes = isLoggedIn() ? getUserLikes($_SESSION['user_id']) : [];
$isLiked = in_array($event['id'], $userLikes);

$pageTitle = htmlspecialchars($event['title']) . ' - Eventify';
// Get related events
$db = getDB();
$stmt = $db->prepare("
    SELECT
        e.*,
        c.name AS category_name,
        c.slug AS category_slug,
        c.icon AS category_icon
    FROM events e
    LEFT JOIN categories c
        ON e.category_id = c.id
    WHERE e.category_id = ?
      AND e.id != ?
      AND e.status = 'active'
    LIMIT 3
");
$stmt->execute([$event['category_id'], $event['id']]);
$relatedEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.event-detail-hero {
  position: relative;
  padding: 140px 0 60px;
  overflow: hidden;
}
.event-detail-hero::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, var(--bg-body) 100%);
  z-index: 1;
}
.event-detail-bg {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background-size: cover;
  background-position: center;
  filter: blur(20px);
  opacity: 0.4;
  transform: scale(1.1);
}
.event-detail-container {
  position: relative;
  z-index: 2;
}
.event-detail-grid {
  display: grid;
  grid-template-columns: 1.2fr 0.8fr;
  gap: 40px;
  align-items: start;
}
.event-detail-image {
  width: 100%;
  aspect-ratio: 16/10;
  object-fit: cover;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.5);
}
.event-detail-info {
  display: flex;
  flex-direction: column;
  gap: 24px;
}
.event-detail-category {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--primary-light);
  color: var(--primary);
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 700;
  width: fit-content;
  border: 1px solid rgba(37, 99, 235, 0.2);
}
.event-detail-title {
  font-size: 2.5rem;
  font-weight: 800;
  line-height: 1.2;
}
.event-detail-meta {
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.meta-item {
  display: flex;
  align-items: center;
  gap: 12px;
  color: var(--text-secondary);
  font-size: 1rem;
}
.meta-item svg {
  color: var(--primary);
  flex-shrink: 0;
}
.event-detail-price {
  display: flex;
  align-items: baseline;
  gap: 12px;
  margin-top: 8px;
}
.price-main {
  font-size: 2rem;
  font-weight: 800;
  color: var(--accent);
  text-shadow: 0 0 15px rgba(249, 115, 22, 0.3);
}
.price-label-detail {
  color: var(--text-muted);
  font-size: 0.9rem;
}
.event-detail-actions {
  display: flex;
  gap: 12px;
  margin-top: 8px;
}
.event-detail-description {
  padding: 60px 0;
}
.desc-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 16px;
}
.desc-text {
  font-size: 1.05rem;
  line-height: 1.7;
  color: var(--text-secondary);
  max-width: 700px;
}
.related-section {
  padding: 60px 0 100px;
}
.related-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 32px;
}
.booking-card {
  background: rgba(17, 24, 39, 0.8);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 20px;
  padding: 32px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  backdrop-filter: blur(20px);
  position: sticky;
  top: 100px;
}
.booking-card-title {
  font-size: 1.1rem;
  font-weight: 700;
}
.qty-selector {
  display: flex;
  align-items: center;
  gap: 16px;
}
.qty-btn {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  color: var(--text-primary);
  font-size: 1.2rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.qty-btn:hover { background: rgba(255,255,255,0.1); }
.qty-value {
  font-size: 1.2rem;
  font-weight: 700;
  min-width: 30px;
  text-align: center;
}
.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 16px;
  border-top: 1px solid rgba(255,255,255,0.06);
}
.total-label { color: var(--text-muted); font-size: 0.9rem; }
.total-value { font-size: 1.4rem; font-weight: 800; color: var(--accent); }
@media (max-width: 1024px) {
  .event-detail-grid { grid-template-columns: 1fr; }
  .booking-card { position: static; }
  .event-detail-title { font-size: 2rem; }
}
@media (max-width: 768px) {
  .event-detail-title { font-size: 1.6rem; }
  .event-detail-hero { padding: 120px 0 40px; }
}
</style>

<!-- Event Hero -->
<section class="event-detail-hero">
  <div class="event-detail-bg" style="background-image: url('<?php echo htmlspecialchars($event['image_url']); ?>')"></div>
  <div class="container event-detail-container">
    <div class="event-detail-grid">
      <div>
        <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-detail-image">
      </div>
      <div class="event-detail-info">
        <span class="event-detail-category">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
          <?php echo htmlspecialchars($event['category_name'] ?? $event['category']); ?>
        </span>

        <h1 class="event-detail-title"><?php echo htmlspecialchars($event['title']); ?></h1>

        <div class="event-detail-meta">
          <div class="meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <?php echo date('l, F j, Y', strtotime($event['date'])); ?> at <?php echo date('g:i A', strtotime($event['time'])); ?>
          </div>
          <div class="meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            <?php echo htmlspecialchars($event['location']); ?>
          </div>
          <div class="meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            Organized by <?php echo htmlspecialchars($event['organizer_name'] ?? 'Eventify'); ?>
          </div>
        </div>

        <div class="event-detail-price">
          <span class="price-main">KES <?php echo number_format($event['price']); ?></span>
          <span class="price-label-detail">per ticket</span>
        </div>

        <div class="event-detail-actions">
          <a href="#booking" class="btn btn-primary" style="flex:1; justify-content:center">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path><path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path></svg>
            Get Tickets
          </a>
          <?php if (isLoggedIn()): ?>
          <button class="btn btn-secondary like-btn-toggle <?php echo $isLiked ? 'liked' : ''; ?>" data-event-id="<?php echo $event['id']; ?>" style="width:48px; padding:0; justify-content:center">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="<?php echo $isLiked ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
          </button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Booking Section -->
<section class="event-detail-description" id="booking">
  <div class="container">
    <div class="event-detail-grid">
      <div>
        <h2 class="desc-title">About This Event</h2>
        <p class="desc-text"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>

        <h2 class="desc-title" style="margin-top: 40px;">Location</h2>
        <p class="desc-text"><?php echo htmlspecialchars($event['location']); ?></p>
        <div id="googleMap"
           style="width:100%;
            height:400px;
            border-radius:16px;
            overflow:hidden;
            margin-top:16px;">
        </div>

         <div style="margin-top:15px;">
         <a
          class="btn btn-primary"
          target="_blank"
          href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($event['location']); ?>">
          📍 Get Directions
         </a>
         </div>
    </div>

      <div class="booking-card">
        <h3 class="booking-card-title">Book Tickets</h3>

        <form id="bookingForm" method="POST" action="event.php?id=<?php echo $event['id']; ?>">
          <input type="hidden" name="mpesa_pay" value="1">
          <input type="hidden" name="quantity" id="quantityInput" value="1">

          <div style="margin-bottom: 18px;">
            <label style="display:block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 10px; font-weight: 600;">Phone Number</label>
            <input id="phoneNumber" name="phone_number" type="tel" placeholder="2547XXXXXXXX" value="<?php echo htmlspecialchars($_SESSION['user_phone'] ?? ''); ?>" style="width:100%; padding:14px 16px; border-radius:14px; border:1px solid rgba(255,255,255,0.12); background:rgba(255,255,255,0.04); color:var(--text-primary);" required>
          </div>

          <div>
            <label style="display:block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 10px; font-weight: 600;">Quantity</label>
            <div class="qty-selector">
              <button type="button" class="qty-btn" onclick="updateQty(-1)">-</button>
              <span class="qty-value" id="qtyValue">1</span>
              <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
            </div>
          </div>

          <div class="total-row">
            <span class="total-label">Total</span>
            <span class="total-value" id="totalPrice">KES <?php echo number_format($event['price']); ?></span>
          </div>

          <button type="button" class="btn btn-primary btn-full" onclick="bookNow()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path><path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path></svg>
            Proceed to Payment
          </button>
        </form>

        <p style="text-align: center; font-size: 0.8rem; color: var(--text-muted); margin-top: 8px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg>
          Secure M-Pesa payments
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Related Events -->
<?php if (!empty($relatedEvents)): ?>
<section class="related-section">
  <div class="container">
    <h2 class="related-title">You Might Also Like</h2>
    <div class="events-grid">
      <?php foreach ($relatedEvents as $index => $evt):
        $glowClass = $index % 2 === 0 ? 'glow-blue' : 'glow-orange';
      ?>
      <article class="event-card <?php echo $glowClass; ?>">
        <div class="event-image-container">
          <img src="<?php echo htmlspecialchars($evt['image_url']); ?>" alt="<?php echo htmlspecialchars($evt['title']); ?>" class="event-image" loading="lazy">
          <span class="event-category-tag"><?php echo htmlspecialchars($evt['category_name']); ?></span>
        </div>
        <div class="event-content">
          <div class="event-meta-row">
            <div class="event-meta-item">
              <svg class="event-meta-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              <?php echo date('F j, Y', strtotime($evt['date'])); ?>
            </div>
          </div>
          <h3 class="event-title"><?php echo htmlspecialchars($evt['title']); ?></h3>
          <div class="event-meta-item">
            <svg class="event-meta-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            <span class="event-location"><?php echo htmlspecialchars($evt['location']); ?></span>
          </div>
          <div class="event-footer">
            <div style="display:flex;flex-direction:column">
              <span class="price-label">Tickets from</span>
              <span class="price-value">KES <?php echo number_format($evt['price']); ?></span>
            </div>
            <a href="event.php?id=<?php echo $evt['id']; ?>" class="details-btn btn btn-primary btn-sm">View Details</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<script>
const ticketPrice = <?php echo $event['price']; ?>;
let qty = 1;

function updateQty(change) {
  qty = Math.max(1, Math.min(10, qty + change));
  document.getElementById('qtyValue').textContent = qty;
  document.getElementById('totalPrice').textContent = 'KES ' + (ticketPrice * qty).toLocaleString();
  document.getElementById('quantityInput').value = qty;
}

function bookNow() {
  <?php if (!isLoggedIn()): ?>
  window.location.href = 'login.php?redirect=event.php?id=<?php echo $event['id']; ?>';
  return;
  <?php endif; ?>

  const phoneField = document.getElementById('phoneNumber');
  const phone = phoneField.value.trim();

  if (!phone) {
    alert('Please enter your phone number in the format 2547XXXXXXXX.');
    phoneField.focus();
    return;
  }

  document.getElementById('quantityInput').value = qty;
  document.getElementById('bookingForm').submit();
}
</script>

<?php require_once 'includes/footer.php'; ?>