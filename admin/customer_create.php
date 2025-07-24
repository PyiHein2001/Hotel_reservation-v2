<?php
session_start();
require '../require/check_auth.php';
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

$error = false;
$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || $phone === '' || $email === '') {
        $error = true;
        $error_msg = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $error_msg = "Invalid email format.";
    } else {
        $values = [
            'name' => $name,
            'phone' => $phone,
            'email' => $email
        ];
        
        if (insertData('customers', $mysqli, $values)) {
            $success_msg = "Customer created successfully.";
        } else {
            $error = true;
            $error_msg = "Failed to create customer.";
        }
    }
}

require './layouts/header.php';
?>
<div class="content-body">
    <div class="container-fluid">
        <h2>Create Customer</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error_msg ?></div>
        <?php elseif ($success_msg): ?>
            <div class="alert alert-success"><?= $success_msg ?></div>
        <?php endif; ?>

        <form method="POST" class="mt-4">
            <div class="form-group">
                <label for="name">Customer Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
</div>
<?php require './layouts/footer.php'; ?>
