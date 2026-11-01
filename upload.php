<?php
session_start();
require_once __DIR__ . '/db.php';

// ឆែកមើលថាជារបស់ Admin ដែរឬទេ
if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../user_login.php");
    exit();
}

$message = '';

// ១. ដំណើរការនៅពេលចុចប៊ូតុង "Add Product"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $description = trim($_POST['description']);

    // ការបញ្ជូនរូបភាព (Image Upload)
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $upload_directory = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
    $image_name = basename($image_name);
    $image_folder = $upload_directory . DIRECTORY_SEPARATOR . $image_name;

    if (!empty($name) && !empty($price) && !empty($image_name)) {
        // បញ្ជូនរូបភាពទៅទម្លាក់ក្នុង folder 'uploads'
        if ((!is_dir($upload_directory) && mkdir($upload_directory, 0755, true)) || is_dir($upload_directory)) {
          if (move_uploaded_file($image_tmp, $image_folder)) {
            // បញ្ចូលទិន្នន័យទៅក្នុង Database (តារាង products)
            $stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $price, $description, $image_name])) {
                $message = "Add product successfuly";
            } else {
                $message = "មានបញ្ហាក្នុងការបញ្ចូលទៅ Database!";
            }
          } else {
            $message = "you can't uploat product! pleace cheack Folder uploads";
          }
        } else {
            $message = "Unable to create the uploads folder.";
        }
    } else {
        $message = "សូមបំពេញព័ត៌មានចាំបាច់ឲ្យបានគ្រប់!";
    }
}

// ២. ទាញយកបញ្ជីផលិតផលមកបង្ហាញក្នុងតារាង
$products = $conn->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container my-5">
        <a href="admin/dashboard.php" class="btn btn-secondary mb-3">← ត្រឡប់ទៅ Dashboard</a>

        <h2>Manage Products</h2>

        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Form បន្ថែមផលិតផល (Add Product Form) -->
        <div class="card mb-5">
            <div class="card-header bg-primary text-white">
                <h5 class="m-0">Add New Product</h5>
            </div>
            <div class="card-body">
                <!-- ចាំបាច់ត្រូវមាន enctype="multipart/form-data" ដើម្បីអាច Upload រូបភាពបាន -->
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ឈ្មោះផលិតផល</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">តម្លៃ ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">រូបភាពផលិតផល</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ការពិពណ៌នា (Description)</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <button type="submit" name="add_product" class="btn btn-success">រក្សាទុក (Save Product)</button>
                </form>
            </div>
        </div>

        <!-- តារាងបង្ហាញបញ្ជីផលិតផលដែលមានស្រាប់ -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="m-0">បញ្ជីផលិតផលទាំងអស់</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>រូបភាព</th>
                            <th>ឈ្មោះ</th>
                            <th>តម្លៃ</th>
                            <th>ការពិពណ៌នា</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <img src="uploads/<?php echo rawurlencode($product['image']); ?>" width="60" height="60" style="object-fit:cover;">
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($product['description']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>