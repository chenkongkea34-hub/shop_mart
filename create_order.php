<?php
session_start();
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json; charset=utf-8');

function respond_with_error(int $status, string $message): void
{
    http_response_code($status);
    echo json_encode(['error' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    respond_with_error(405, 'Method not allowed.');
}

$sessionToken = $_SESSION['csrf_token'] ?? '';
$requestToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if ($sessionToken === '' || !hash_equals($sessionToken, $requestToken)) {
    respond_with_error(403, 'Your session has expired. Refresh the page and try again.');
}
if (!isset($_SESSION['user']['id'])) {
    respond_with_error(401, 'Sign in before placing an order.');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    respond_with_error(400, 'Invalid order data.');
}

$name = trim(is_string($input['name'] ?? null) ? $input['name'] : '');
$phone = trim(is_string($input['phone'] ?? null) ? $input['phone'] : '');
$address = trim(is_string($input['address'] ?? null) ? $input['address'] : '');
$payment = $input['payment'] ?? '';
$items = $input['items'] ?? null;

if ($name === '' || mb_strlen($name) > 100) {
    respond_with_error(422, 'Enter a valid name (100 characters or fewer).');
}
if ($phone === '' || mb_strlen($phone) > 30) {
    respond_with_error(422, 'Enter a valid phone number (30 characters or fewer).');
}
if ($address === '' || mb_strlen($address) > 1000) {
    respond_with_error(422, 'Enter a valid delivery address (1000 characters or fewer).');
}
if (!in_array($payment, ['cod', 'qr'], true)) {
    respond_with_error(422, 'Select a valid payment method.');
}
if (!is_array($items) || count($items) < 1 || count($items) > 100) {
    respond_with_error(422, 'Your cart is empty or contains too many items.');
}

$validatedItems = [];
$subtotalCents = 0;
try {
    $database = ShopMate_db();
    $productStatement = $database->prepare('SELECT id, name, price FROM products WHERE id = ?');

    foreach ($items as $item) {
        if (!is_array($item)) {
            respond_with_error(422, 'Invalid item in your cart.');
        }

        $productId = filter_var($item['productId'] ?? null, FILTER_VALIDATE_INT);
        $quantity = filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT);
        if ($productId === false || $productId < 1 || $quantity === false || $quantity < 1 || $quantity > 99) {
            respond_with_error(422, 'Your cart contains an invalid item.');
        }

        $productStatement->execute([$productId]);
        $product = $productStatement->fetch();
        if (!$product) {
            respond_with_error(422, 'Your cart contains a product that is no longer available.');
        }

        $priceParts = explode('.', (string) $product['price'], 2);
        $unitPriceCents = ((int) $priceParts[0] * 100) + (int) str_pad($priceParts[1] ?? '0', 2, '0');
        $lineTotalCents = $unitPriceCents * $quantity;
        $subtotalCents += $lineTotalCents;
        if ($subtotalCents > 999999999849) {
            respond_with_error(422, 'The order total is too large.');
        }
        $validatedItems[] = [
            'product_id' => (int) $product['id'],
            'name' => $product['name'],
            'quantity' => $quantity,
            'unit_price' => number_format($unitPriceCents / 100, 2, '.', ''),
            'subtotal' => number_format($lineTotalCents / 100, 2, '.', ''),
        ];
    }
} catch (PDOException $exception) {
    error_log('Order product validation error: ' . $exception->getMessage());
    respond_with_error(500, 'Unable to verify your products. Please try again later.');
}

$deliveryFeeCents = 150;
$totalCents = $subtotalCents + $deliveryFeeCents;
$userId = (int) $_SESSION['user']['id'];

try {
    $database = shopMate_db();
    $database->beginTransaction();

    $orderNumber = 'SM-' . gmdate('YmdHis') . '-' . bin2hex(random_bytes(6));
    $createdAt = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s.u');
    $orderStatement = $database->prepare(
        'INSERT INTO orders
            (user_id, order_number, order_date, total_amount, status, customer_name, customer_phone,
             shipping_address, payment_method, payment_status, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $orderStatement->execute([
        $userId,
        $orderNumber,
        $createdAt,
        number_format($totalCents / 100, 2, '.', ''),
        'Pending',
        $name,
        $phone,
        $address,
        $payment,
        'Unpaid',
        $createdAt,
    ]);
    $orderId = (int) $database->lastInsertId();

    $itemStatement = $database->prepare(
        'INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, created_at)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    foreach ($validatedItems as $item) {
        $itemStatement->execute([
            $orderId,
            $item['product_id'],
            $item['name'],
            $item['quantity'],
            $item['unit_price'],
            $createdAt,
        ]);
    }

    $database->commit();
    echo json_encode(['orderId' => $orderId, 'orderNumber' => $orderNumber]);
} catch (PDOException $exception) {
    if (isset($database) && $database->inTransaction()) {
        $database->rollBack();
    }
    error_log('Order database error: ' . $exception->getMessage());
    respond_with_error(500, 'Unable to save your order. Please try again later.');
}
