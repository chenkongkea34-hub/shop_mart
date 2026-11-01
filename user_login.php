<?php
session_start();
require_once __DIR__ . '/config/database.php';

if (isset($_SESSION['user'])) {
    if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$loginError = '';
$loginSuccess = isset($_GET['registered']) ? 'Your account was created. You can now sign in.' : '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $loginError = 'Enter a valid email address and password.';
    } else {
        try {
            $database = ShopMate_db();

            $statement = $database->prepare('SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1');
            $statement->execute([$email]);
            $user = $statement->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => (int) $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'] 
                ];

          
                if ($user['role'] === 'admin') {
                    header('Location: admin/dashboard.php'); 
                } else {
                    header('Location: index.php'); // បើជា User ទៅកាន់គេហទំព័រដើម
                }
                exit;
            }
            $loginError = 'The email or password is incorrect.';
        } catch (PDOException $exception) {
            error_log('Login database error: ' . $exception->getMessage());
            $loginError = 'Unable to connect to the database or process the login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in | ShopMate</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=lock" />
</head>

<body class="min-h-screen bg-stone-900/90 antialiased">
    <main class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-stone-950/70 backdrop-blur-sm"></div>

        <section class="relative w-full max-w-md rounded-3xl bg-white p-8 shadow-2xl" aria-labelledby="login-title">
            <a href="index.php" class="absolute right-5 top-5 rounded-full p-2 text-2xl leading-none text-stone-400 transition hover:bg-stone-100 hover:text-stone-700" aria-label="Close login form">&times;</a>

            <div class="mb-7">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-lime-100 text-2xl text-lime-800" aria-hidden="true">&#128274;</div>
                <h1 id="login-title" class="text-2xl font-black text-stone-900">Welcome back</h1>
                <p class="mt-1 text-sm text-stone-500">Sign in to continue shopping with ShopMate.</p>
            </div>

            <?php if ($loginError !== ''): ?>
                <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert">
                    <?php echo htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($loginSuccess !== ''): ?>
                <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">
                    <?php echo htmlspecialchars($loginSuccess, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-4">
                <div>
                    <label for="login-email" class="mb-1.5 block text-sm font-semibold text-stone-700">Email address</label>
                    <input id="login-email" name="email" type="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="email" required class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-lime-600 focus:ring-2 focus:ring-lime-100" placeholder="you@example.com">
                </div>

                <div>
                    <label for="login-password" class="mb-1.5 block text-sm font-semibold text-stone-700">Password</label>
                    <input id="login-password" name="password" type="password" autocomplete="current-password" required class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-lime-600 focus:ring-2 focus:ring-lime-100" placeholder="Enter your password">
                </div>

                <button type="submit" class="w-full rounded-xl bg-lime-700 py-3.5 text-sm font-bold text-white shadow-lg shadow-lime-700/20 transition hover:bg-lime-800">Sign in</button>
            </form>

            <p class="mt-6 text-center text-sm text-stone-500">Do not have an account? <a href="user_register.php" class="font-bold text-lime-700 hover:text-lime-800">Create one</a></p>
        </section>
    </main>
</body>

</html>