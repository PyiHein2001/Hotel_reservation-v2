<?php
session_start();
require '../require/check_auth.php';
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

$user_id = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0;
if (!$user_id) {
    die('User not logged in.');
}

// Fetch user data
$user_res = selectData('users', $mysqli, '*', "WHERE id = $user_id");
if (!$user_res || $user_res->num_rows === 0) {
    die('User not found.');
}
$user = $user_res->fetch_assoc();

$error = false;
$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $phone === '' || $gender === '') {
        $error = true;
        $error_msg = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $error_msg = "Invalid email format.";
    } elseif ($password !== '' && strlen($password) < 8) {
        $error = true;
        $error_msg = "Password must be at least 8 characters.";
    } else {
        $values = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'gender' => $gender
        ];
        if ($password !== '') {
            $values['password'] = md5($password);
        }
        $where = ['id' => $user_id];
        if (updateData('users', $mysqli, $values, $where)) {
            $success_msg = "Profile updated successfully.";
            $user = array_merge($user, $values);
        } else {
            $error = true;
            $error_msg = "Failed to update profile.";
        }
    }
}

require './layouts/header.php';
?>
<div class="content-body">
    <div class="container-fluid">
        <h2>Profile Setting</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error_msg ?></div>
        <?php elseif ($success_msg): ?>
            <div class="alert alert-success"><?= $success_msg ?></div>
        <?php endif; ?>
        <form method="POST" class="mt-4">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" required value="<?= htmlspecialchars($user['name']) ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required value="<?= htmlspecialchars($user['email']) ?>">
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control" required value="<?= htmlspecialchars($user['phone']) ?>">
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="">-- Select Gender --</option>
                    <option value="male" <?= $user['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= $user['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">New Password (leave blank to keep current)</label>
                <input type="password" name="password" id="password" class="form-control" minlength="8" placeholder="Enter new password">
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
</div>
<?php require './layouts/footer.php'; ?> 