<?php
session_start();

if (!isset($_SESSION['user']['id'])) {
    header('Location: user_login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$orders = [];
$loadError = '';
$actionError = '';
$notice = $_SESSION['order_history_notice'] ?? '';
unset($_SESSION['order_history_notice']);
$userId = (int) $_SESSION['user']['id'];

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $orderId = filter_var($_POST['order_id'] ?? null, FILTER_VALIDATE_INT);
    $requestToken = $_POST['csrf_token'] ?? '';

    if (!is_string($requestToken) || !hash_equals($_SESSION['csrf_token'], $requestToken)) {
        http_response_code(403);
        $actionError = 'Your session expired. Refresh the page and try again.';
    } elseif ($orderId === false || $orderId === null || $orderId < 1) {
        $actionError = 'The selected order is invalid.';
    } else {
        try {
            $database = ShopMate_db();
            $database->beginTransaction();

            $orderStatement = $database->prepare(
                'SELECT id FROM orders WHERE id = ? AND user_id = ? FOR UPDATE'
            );
            $orderStatement->execute([$orderId, $userId]);

            if ($orderStatement->fetchColumn() === false) {
                $database->rollBack();
                $actionError = 'That order could not be found.';
            } else {
                $itemStatement = $database->prepare('DELETE FROM order_items WHERE order_id = ?');
                $itemStatement->execute([$orderId]);

                $deleteStatement = $database->prepare('DELETE FROM orders WHERE id = ? AND user_id = ?');
                $deleteStatement->execute([$orderId, $userId]);
                $database->commit();

                $_SESSION['order_history_notice'] = 'Order deleted successfully.';
                header('Location: order_history.php');
                exit;
            }
        } catch (PDOException $exception) {
            if (isset($database) && $database->inTransaction()) {
                $database->rollBack();
            }
            error_log('Order deletion database error: ' . $exception->getMessage());
            $actionError = 'Unable to delete this order right now. Please try again.';
        }
    }
}

try {
    $database = shopMate_db();
    $statement = $database->prepare(
        'SELECT id, order_number, order_date, total_amount, status, payment_status
         FROM orders
         WHERE user_id = ?
         ORDER BY order_date DESC, id DESC'
    );
    $statement->execute([$userId]);
    $orders = $statement->fetchAll();
} catch (PDOException $exception) {
    error_log('Orders database error: ' . $exception->getMessage());
    $loadError = 'Unable to load your orders right now. Please try again later.';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order history | ShopMate</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        table {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        thead {
            background-color: #73dea7;
            color: #373a40;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
</head>

<body class="min-h-screen bg-stone-50 antialiased">
    <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-stone-900">Order history</h1>
                <p class="mt-2 text-sm text-stone-600">Review your recent ShopMate orders.</p>
            </div>
            <a href="index.php" class="rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-semibold text-stone-700 transition hover:border-lime-700 hover:text-lime-800">Continue shopping</a>
        </header>

        <?php if ($notice !== ''): ?>
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">
                <?php echo htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($actionError !== ''): ?>
            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert">
                <?php echo htmlspecialchars($actionError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($loadError !== ''): ?>
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert">
                <?php echo htmlspecialchars($loadError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php elseif ($orders === []): ?>
            <div class="rounded-2xl border border-stone-200 bg-white p-8 text-center shadow-sm">
                <p class="font-semibold text-stone-800">You have not placed any orders yet.</p>
                <a href="index.php" class="mt-4 inline-block font-bold text-lime-800 hover:text-lime-900">Browse products</a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-stone-200 text-left text-sm">
                    <thead class="text-xs uppercase tracking-wide text-stone-600">
                        <tr>
                            <th scope="col" class="px-5 py-4 font-bold">Order number</th>
                            <th scope="col" class="px-5 py-4 font-bold">Date</th>
                            <th scope="col" class="px-5 py-4 font-bold">Total</th>
                            <th scope="col" class="px-5 py-4 font-bold">Order status</th>
                            <th scope="col" class="px-5 py-4 font-bold">Payment status</th>
                            <th scope="col" class="px-5 py-4 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-stone-700">
                        <?php foreach ($orders as $order): ?>
                            <?php
                            // Format the date nicely
                            $formattedDate = date("M d, Y • h:i A", strtotime($order['order_date']));

                            // Define badge colors based on status
                            $status = strtolower($order['status']);
                            $statusClass = match ($status) {
                                'completed', 'paid', 'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                default => 'bg-rose-50 text-rose-700 border-rose-200'
                            };
                            ?>
                            <tr class="hover:bg-stone-50/80 transition">
                                <!-- Order ID -->
                                <td class="whitespace-nowrap px-5 py-4 font-bold text-stone-900">#
                                    <?php echo htmlspecialchars((string) $order['id'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <!-- Formatted Date -->
                                <td class="whitespace-nowrap px-5 py-4 text-stone-600 text-xs">
                                    <?php echo htmlspecialchars($formattedDate, ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <!-- Total Price -->
                                <td class="whitespace-nowrap px-5 py-4 font-semibold text-stone-900">
                                    $<?php echo number_format((float) $order['total_amount'], 2); ?>
                                </td>

                                <!-- Order Status Badge -->
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars((string) $order['status'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>

                                <!-- Payment Status -->
                                <td class="whitespace-nowrap px-5 py-4 text-stone-600">
                                    <?php echo htmlspecialchars((string) $order['payment_status'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <!-- Details Link -->
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="order_detail.php?id=<?php echo (int) $order['id']; ?>" class="text-xs font-bold text-lime-700 hover:text-lime-800 hover:underline">
                                            View details
                                        </a>
                                        <form method="post" onsubmit="return confirm('Delete this order and its items? This cannot be undone.');">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
                                            <button type="submit" name="delete_order" value="1" class="text-xs font-bold text-rose-600 hover:text-rose-800 hover:underline">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>