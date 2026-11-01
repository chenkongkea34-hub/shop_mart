<?php
session_start();
require_once __DIR__ . '/config/database.php';

if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
$registerError = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '' || strlen($name) > 100) {
        $registerError = 'Enter your name using 100 characters or fewer.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 191) {
        $registerError = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $registerError = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $registerError = 'Passwords do not match.';
    } else {
        try {
            $database = ShopMate_db();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $statement = $database->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $statement->execute([$name, $email, $hashedPassword]);

            header('Location: user_login.php?registered=success');
            exit;
        } catch (PDOException $exception) {
            error_log('Registration database error: ' . $exception->getMessage());
            $registerError = 'Unable to create your account. Check your database connection or whether the email is already registered.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account | ShopMate</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-stone-900/90 antialiased">
    <main class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-stone-950/70 backdrop-blur-sm"></div>

        <section class="relative w-full max-w-md rounded-3xl bg-white p-8 shadow-2xl" aria-labelledby="register-title">
            <a href="index.php" class="absolute right-5 top-5 rounded-full p-2 text-2xl leading-none text-stone-400 transition hover:bg-stone-100 hover:text-stone-700" aria-label="Close registration form">&times;</a>

            <div class="mb-7">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-lime-100 text-2xl text-lime-800" aria-hidden="true">&#128100;</div>
                <h1 id="register-title" class="text-2xl font-black text-stone-900">Create your account</h1>
                <p class="mt-1 text-sm text-stone-500">Join ShopMate and start shopping today.</p>
            </div>

            <?php if ($registerError !== ''): ?>
                <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert">
                    <?php echo htmlspecialchars($registerError, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-4">
                <div>
                    <label for="register-name" class="mb-1.5 block text-sm font-semibold text-stone-700">Full name</label>
                    <input id="register-name" name="name" type="text" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="name" required class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-lime-600 focus:ring-2 focus:ring-lime-100" placeholder="Your name">
                </div>

                <div>
                    <label for="register-email" class="mb-1.5 block text-sm font-semibold text-stone-700">Email address</label>
                    <input id="register-email" name="email" type="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="email" required class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-lime-600 focus:ring-2 focus:ring-lime-100" placeholder="you@example.com">
                </div>

                <div>
                    <label for="register-password" class="mb-1.5 block text-sm font-semibold text-stone-700">Password</label>
                    <input id="register-password" name="password" type="password" minlength="8" autocomplete="new-password" required class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-lime-600 focus:ring-2 focus:ring-lime-100" placeholder="At least 8 characters">
                </div>

                <div>
                    <label for="confirm-password" class="mb-1.5 block text-sm font-semibold text-stone-700">Confirm password</label>
                    <input id="confirm-password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-lime-600 focus:ring-2 focus:ring-lime-100" placeholder="Repeat your password">
                </div>

                <button type="submit" class="w-full rounded-xl bg-lime-700 py-3.5 text-sm font-bold text-white shadow-lg shadow-lime-700/20 transition hover:bg-lime-800">Create account</button>
            </form>

            <p class="mt-6 text-center text-sm text-stone-500">Already have an account? <a href="user_login.php" class="font-bold text-lime-700 hover:text-lime-800">Sign in</a></p>
        </section>
    </main>
</body>

</html>