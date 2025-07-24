<?php
session_start();
require '../require/check_auth.php';
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

$error = false;
$name = $name_error = $error_msg = '';

if (isset($_POST['form_sub']) && $_POST['form_sub'] == '1') {
    $name = trim($mysqli->real_escape_string($_POST['name']));

    if (strlen($name) === 0) {
        $error = true;
        $name_error = 'Please enter amenity name!';
    } else if (strlen($name) > 100) {
        $error = true;
        $name_error = 'Amenity name must be less than 100 characters!';
    } else {
        $check_query = "SELECT COUNT(*) as count FROM amenities WHERE name = '$name'";
        $check_result = $mysqli->query($check_query);
        $row = $check_result->fetch_assoc();
        if ($row['count'] > 0) {
            $error = true;
            $name_error = 'Amenity name already exists!';
        }
    }

    if (!$error) {
        $data = ['name' => $name];
        $insert = insertData('amenities', $mysqli, $data);
        if ($insert) {
            $url = $admin_base_url . 'amenity_list.php?success=Amenity Created Successfully!';
            header("Location: $url");
            exit();
        } else {
            $error = true;
            $error_msg = 'Something went wrong while saving the data!';
        }
    }
}

require './layouts/header.php';
?>

<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Amenity Create</h1>
            <a href="<?= $admin_base_url . 'amenity_list.php' ?>" class="btn btn-dark">Back</a>
        </div>

        <div class="d-flex justify-content-center">
            <div class="col-md-6 col-sm-10 col-12">
                <?php if ($error && $error_msg): ?>
                    <div class="alert alert-danger"><?= $error_msg ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="name">Amenity Name</label>
                                <input type="text" class="form-control" name="name" value="<?= $name ?>">
                                <?php if ($error && $name_error): ?>
                                    <span class="text-danger"><?= $name_error ?></span>
                                <?php endif; ?>
                            </div>

                            <input type="hidden" name="form_sub" value="1" />
                            <button type="submit" class="btn btn-primary w-100">Create Amenity</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require './layouts/footer.php'; ?>
