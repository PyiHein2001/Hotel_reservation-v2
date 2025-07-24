<?php 
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

// Initialize variables
$error = false;
$fields = [
    'name' => '', 'email' => '', 'address' => '', 
    'phone' => ''
];
$errors = array_fill_keys(array_keys($fields), '');
$logo_path = ''; // Default empty

function validate_hotel_setting($name, $mysqli) {
    $query = "SELECT COUNT(id) as count FROM `hotel_settings` WHERE name=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $data['count'];
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == '1') {
    // Sanitize inputs
    foreach ($fields as $key => $value) {
        $fields[$key] = $mysqli->real_escape_string($_POST[$key] ?? '');
    }

    // Validate name
    if (empty($fields['name'])) {
        $error = true;
        $errors['name'] = 'Please enter hotel name!';
    } else if (strlen($fields['name']) > 100) {
        $error = true;
        $errors['name'] = 'Hotel name must be less than 100 characters!';
    } else if (strlen($fields['name']) < 3) {
        $error = true;
        $errors['name'] = 'Hotel name must be more than 3 characters!';
    } else if (validate_hotel_setting($fields['name'], $mysqli) > 0) {
        $error = true;
        $errors['name'] = 'Hotel name already exists!';
    }

    // Validate email
    if (!empty($fields['email']) && !filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $errors['email'] = 'Please enter a valid email address!';
    }

    // Validate phone
    if (!empty($fields['phone']) && !preg_match('/^[0-9]{10,15}$/', $fields['phone'])) {
        $error = true;
        $errors['phone'] = 'Please enter a valid phone number (10-15 digits)!';
    }

    // Validate address
    if (strlen($fields['address']) > 500) {
        $error = true;
        $errors['address'] = 'Address must be less than 500 characters!';
    }

    // Handle logo upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/logos/';
        $file_tmp = $_FILES['logo']['tmp_name'];
        $file_name = basename($_FILES['logo']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed)) {
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $new_name = uniqid('logo_', true) . '.' . $file_ext;
            $target_path = $upload_dir . $new_name;

            if (move_uploaded_file($file_tmp, $target_path)) {
                $logo_path = 'uploads/logos/' . $new_name;
            } else {
                $error = true;
                $error_msg = 'Failed to upload logo!';
            }
        } else {
            $error = true;
            $error_msg = 'Invalid logo file type! Only jpg, jpeg, png, gif allowed.';
        }
    }

    if (!$error) {
        try {
            $data = [
                'name' => $fields['name'],
                'email' => $fields['email'],
                'address' => $fields['address'],
                'phone' => $fields['phone'],
                'check_in_time' => '13:00:00',
                'check_out_time' => '12:00:00',
                'logo_path' => $logo_path
            ];
            
            $result = insertData('hotel_settings', $mysqli, $data);
            
            if ($result) {
                header("Location: {$admin_base_url}hotel_setting_list.php?success=Hotel settings created successfully");
                exit();
            } else {
                $error = true;
                $error_msg = 'Something went wrong!';
            }
        } catch(Exception $e) {
            $error = true;
            $error_msg = 'Database error: ' . $e->getMessage();
        }
    }
}

require './layouts/header.php';
?>

<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Create Hotel Settings</h1>
            <a href="<?= $admin_base_url.'hotel_setting_list.php' ?>" class="btn btn-dark">
                <i class="fa fa-arrow-left"></i> Back to List
            </a>
        </div>
        
        <?php if($error && isset($error_msg)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Hotel Information</h4>
                    </div>
                    <div class="card-body">
                        <form action="<?= $admin_base_url.'hotel_setting_create.php' ?>" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Hotel Name *</label>
                                        <input type="text" name="name" id="name" class="form-control" 
                                               placeholder="Enter hotel name" 
                                               value="<?= htmlspecialchars($fields['name']) ?>">
                                        <?php if($error && $errors['name']): ?>
                                            <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['name']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Hotel Email</label>
                                        <input type="email" name="email" id="email" class="form-control" 
                                               placeholder="Enter hotel email" 
                                               value="<?= htmlspecialchars($fields['email']) ?>">
                                        <?php if($error && $errors['email']): ?>
                                            <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['email']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="address" class="form-label">Hotel Address</label>
                                <textarea name="address" id="address" class="form-control" 
                                          placeholder="Enter full address"><?= htmlspecialchars($fields['address']) ?></textarea>
                                <?php if($error && $errors['address']): ?>
                                    <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['address']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Check-in Time</label>
                                        <input type="text" class="form-control" value="1:00 PM" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Check-out Time</label>
                                        <input type="text" class="form-control" value="12:00 PM" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" name="phone" id="phone" class="form-control" 
                                       placeholder="Enter phone number" 
                                       value="<?= htmlspecialchars($fields['phone']) ?>">
                                <?php if($error && $errors['phone']): ?>
                                    <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['phone']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="logo" class="form-label">Hotel Logo</label>
                                <input type="file" name="logo" id="logo" class="form-control">
                            </div>
                            
                            <input type="hidden" name="form_sub" value="1">
                            <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">
                                <i class="fa fa-save"></i> Save Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require './layouts/footer.php'; ?>
