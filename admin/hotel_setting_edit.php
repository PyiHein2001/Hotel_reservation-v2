<?php 
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

$error = false;
$error_msg = '';
$fields = [
    'name' => '', 'email' => '', 'address' => '', 'phone' => '', 'logo_path' => ''
];
$errors = array_fill_keys(array_keys($fields), '');

if (isset($_GET['edit_id']) && $_GET['edit_id'] !== '') {
    $id = $mysqli->real_escape_string($_GET['edit_id']);
    $res = selectData('hotel_settings', $mysqli, '*', "WHERE id=$id");

    if ($res && $res->num_rows > 0) {
        $data = $res->fetch_assoc();
        foreach ($fields as $key => $_) {
            $fields[$key] = $data[$key] ?? '';
        }
    } else {
        $error = true;
        $error_msg = 'Invalid request';
        header("Location: {$admin_base_url}hotel_setting_list.php");
        exit();
    }
} else {
    $error = true;
    $error_msg = 'Invalid request';
    header("Location: {$admin_base_url}hotel_setting_list.php");
    exit();
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == '1') {
    foreach (['name', 'email', 'address', 'phone'] as $key) {
        $fields[$key] = $mysqli->real_escape_string($_POST[$key] ?? '');
    }

    // Validation
    if (empty($fields['name'])) {
        $error = true;
        $errors['name'] = 'Please enter hotel name!';
    } else if (strlen($fields['name']) < 3) {
        $error = true;
        $errors['name'] = 'Hotel name must be more than 3 characters!';
    } else if (strlen($fields['name']) > 100) {
        $error = true;
        $errors['name'] = 'Hotel name must be less than 100 characters!';
    }

    if (!empty($fields['email']) && !filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $errors['email'] = 'Please enter a valid email address!';
    }

    if (!empty($fields['phone']) && !preg_match('/^[0-9]{10,15}$/', $fields['phone'])) {
        $error = true;
        $errors['phone'] = 'Please enter a valid phone number!';
    }

    if (!$error) {
        // Handle new logo upload if exists
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/logos/';
            $file_tmp = $_FILES['logo']['tmp_name'];
            $file_name = basename($_FILES['logo']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($file_ext, $allowed)) {
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $new_name = uniqid('logo_', true) . '.' . $file_ext;
                $target_path = $upload_dir . $new_name;

                if (move_uploaded_file($file_tmp, $target_path)) {
                    $fields['logo_path'] = 'uploads/logos/' . $new_name;
                } else {
                    $error = true;
                    $error_msg = 'Logo upload failed!';
                }
            } else {
                $error = true;
                $error_msg = 'Invalid logo type! (jpg, png, gif)';
            }
        }

        if (!$error) {
            $fields['check_in_time'] = '13:00:00';
            $fields['check_out_time'] = '12:00:00';
            $where = ['id' => $id];
            $updated = updateData('hotel_settings', $mysqli, $fields, $where);

            if ($updated) {
                header("Location: {$admin_base_url}hotel_setting_list.php?success=Hotel setting updated successfully");
                exit();
            } else {
                $error = true;
                $error_msg = 'Something went wrong!';
            }
        }
    }
}

require './layouts/header.php';
?>

<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-4">
            <h1>Edit Hotel Setting</h1>
            <a href="<?= $admin_base_url . 'hotel_setting_list.php' ?>" class="btn btn-dark">Back</a>
        </div>

        <?php if ($error && $error_msg): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8 col-sm-10">
                <div class="card">
                    <div class="card-body">
                        <form action="<?= $admin_base_url . 'hotel_setting_edit.php?edit_id=' . $id ?>" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name">Hotel Name *</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($fields['name']) ?>">
                                <?php if ($error && $errors['name']): ?>
                                    <span class="text-danger"><?= $errors['name'] ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="email">Hotel Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($fields['email']) ?>">
                                <?php if ($error && $errors['email']): ?>
                                    <span class="text-danger"><?= $errors['email'] ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="phone">Hotel Phone</label>
                                <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($fields['phone']) ?>">
                                <?php if ($error && $errors['phone']): ?>
                                    <span class="text-danger"><?= $errors['phone'] ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="address">Hotel Address</label>
                                <textarea name="address" class="form-control"><?= htmlspecialchars($fields['address']) ?></textarea>
                                <?php if ($error && $errors['address']): ?>
                                    <span class="text-danger"><?= $errors['address'] ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="logo">Hotel Logo</label>
                                <input type="file" name="logo" class="form-control">
                                <?php if (!empty($fields['logo_path'])): ?>
                                    <div class="mt-2">
                                        <img src="../<?= $fields['logo_path'] ?>" width="100" alt="Hotel Logo">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <input type="hidden" name="form_sub" value="1">
                            <button type="submit" class="btn btn-primary w-100">Update Hotel Setting</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require './layouts/footer.php'; ?>
