<?php
session_start();

if (!isset($_SESSION['user']['id'])) {
    header('Location: user_login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$orderId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$order = null;
$items = [];
$loadError = '';

if ($orderId === false || $orderId === null || $orderId < 1) {
    http_response_code(404);
} else {
    try {
        $database = shopMate_db();
        $orderStatement = $database->prepare(
            'SELECT id, order_number, order_date, total_amount, status, payment_status,
                    customer_name, customer_phone, shipping_address, payment_method
             FROM orders
             WHERE id = ? AND user_id = ?'
        );
        $orderStatement->execute([$orderId, (int) $_SESSION['user']['id']]);
        $order = $orderStatement->fetch();

        if ($order === false) {
            $order = null;
            http_response_code(404);
        } else {
            $itemsStatement = $database->prepare(
                'SELECT product_name, quantity, unit_price, subtotal
                 FROM order_items
                 WHERE order_id = ?
                 ORDER BY id'
            );
            $itemsStatement->execute([$orderId]);
            $items = $itemsStatement->fetchAll();
        }
    } catch (PDOException $exception) {
        error_log('Order detail database error: ' . $exception->getMessage());
        http_response_code(500);
        $loadError = 'Unable to load this order right now. Please try again later.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order details | ShopMate</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-stone-50 antialiased">
    <main class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <header class="mb-8">
            <a href="order_history.php" class="text-sm font-semibold text-lime-800 hover:text-lime-900">&larr; Back to order history</a>
            <h1 class="mt-4 text-3xl font-black text-stone-900">Order details</h1>
        </header>

        <?php if ($loadError !== ''): ?>
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert">
                <?php echo htmlspecialchars($loadError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php elseif ($order === null): ?>
            <div class="rounded-2xl border border-stone-200 bg-white p-8 text-center shadow-sm">
                <p class="font-semibold text-stone-800">This order could not be found.</p>
                <p class="mt-2 text-sm text-stone-600">It may not exist or may not belong to your account.</p>
            </div>
        <?php else: ?>
            <section class="mb-6 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm" aria-labelledby="order-summary">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 id="order-summary" class="text-lg font-bold text-stone-900">
                            Order #<?php echo htmlspecialchars((string) $order['id'], ENT_QUOTES, 'UTF-8'); ?>
                        </h2>
                        <p class="mt-1 text-sm text-stone-600">
                            Placed <?php echo htmlspecialchars(date('M j, Y g:i A', strtotime((string) $order['order_date'])), ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                    </div>
                    <p class="text-sm text-stone-700">
                        <span class="font-semibold">Status:</span>
                        <?php echo htmlspecialchars((string) $order['status'], ENT_QUOTES, 'UTF-8'); ?>
                        <span class="px-1 text-stone-400">|</span>
                        <span class="font-semibold">Payment:</span>
                        <?php echo htmlspecialchars((string) $order['payment_status'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                </div>

                <dl class="mt-6 grid gap-5 border-t border-stone-100 pt-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-stone-500">Delivery details</dt>
                        <dd class="mt-2 text-sm text-stone-700">
                            <?php if (!empty($order['customer_name'])): ?>
                                <span class="block"><?php echo htmlspecialchars((string) $order['customer_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($order['customer_phone'])): ?>
                                <span class="block"><?php echo htmlspecialchars((string) $order['customer_phone'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                            <span class="block whitespace-pre-line"><?php echo htmlspecialchars((string) $order['shipping_address'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-stone-500">Payment method</dt>
                        <dd class="mt-2 text-sm text-stone-700"><?php echo htmlspecialchars((string) ($order['payment_method'] ?? 'Not specified'), ENT_QUOTES, 'UTF-8'); ?></dd>
                    </div>
                </dl>
            </section>

            <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm" aria-labelledby="items-heading">
                <h2 id="items-heading" class="px-5 py-4 text-lg font-bold text-stone-900">Items</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200 text-left text-sm">
                        <thead class="bg-stone-100 text-xs uppercase tracking-wide text-stone-600">
                            <tr>
                                <th scope="col" class="px-5 py-3 font-bold">Product</th>
                                <th scope="col" class="px-5 py-3 text-right font-bold">Unit price</th>
                                <th scope="col" class="px-5 py-3 text-right font-bold">Quantity</th>
                                <th scope="col" class="px-5 py-3 text-right font-bold">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-stone-700">
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="px-5 py-4 font-medium text-stone-900"><?php echo htmlspecialchars((string) $item['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right">$<?php echo number_format((float) $item['unit_price'], 2); ?></td>
                                    <td class="px-5 py-4 text-right"><?php echo (int) $item['quantity']; ?></td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right">$<?php echo number_format((float) $item['subtotal'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($items === []): ?>
                                <tr>
                                    <td colspan="4" class="px-5 py-6 text-center text-stone-600">No items are listed for this order.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="border-t-2 border-stone-200 bg-stone-50">
                            <tr>
                                <th scope="row" colspan="3" class="px-5 py-4 text-right font-bold text-stone-900">Order total</th>
                                <td class="whitespace-nowrap px-5 py-4 text-right font-bold text-stone-900">$<?php echo number_format((float) $order['total_amount'], 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>
        <?php endif; ?>
    </main>
</body>

</html>