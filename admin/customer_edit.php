<?php
session_start();
require '../require/check_auth.php';
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

$error = false;
$error_msg = '';
$success_msg = '';

$customer_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$customer_id) {
    die('Invalid customer ID.');
}

// Fetch customer data
$customer_res = selectData('customers', $mysqli, '*', "WHERE id = $customer_id");
if (!$customer_res || $customer_res->num_rows === 0) {
    die('Customer not found.');
}
$customer = $customer_res->fetch_assoc();

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
        $where = ['id' => $customer_id];
        if (updateData('customers', $mysqli, $values, $where)) {
            $success_msg = "Customer updated successfully.";
            // Refresh data
            $customer = array_merge($customer, $values);
        } else {
            $error = true;
            $error_msg = "Failed to update customer.";
        }
    }
}

require './layouts/header.php';
?>
<div class="content-body">
    <div class="container-fluid">
        <h2>Edit Customer</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error_msg ?></div>
        <?php elseif ($success_msg): ?>
            <div class="alert alert-success"><?= $success_msg ?></div>
        <?php endif; ?>
        <form method="POST" class="mt-4">
            <div class="form-group">
                <label for="name">Customer Name</label>
                <input type="text" name="name" id="name" class="form-control" required value="<?= htmlspecialchars($customer['name']) ?>">
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" class="form-control" required value="<?= htmlspecialchars($customer['phone']) ?>">
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required value="<?= htmlspecialchars($customer['email']) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
<?php require './layouts/footer.php'; ?>
