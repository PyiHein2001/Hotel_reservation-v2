<?php
session_start();
require '../require/check_auth.php';
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

$error = false;
$name = $name_error = $error_msg = '';

if (!isset($_GET['edit_id']) || $_GET['edit_id'] === '') {
    $url = $admin_base_url . 'amenity_list.php?error=Invalid Amenity ID';
    header("Location: $url");
    exit();
}

$id = $_GET['edit_id'];
$res = selectData('amenities', $mysqli, "*", "WHERE id=$id");

if ($res->num_rows == 0) {
    $url = $admin_base_url . 'amenity_list.php?error=Amenity not found';
    header("Location: $url");
    exit();
}

$data = $res->fetch_assoc();
$name = $data['name'];

if (isset($_POST['form_sub']) && $_POST['form_sub'] == '1') {
    $name = $mysqli->real_escape_string(trim($_POST['name']));

    if (strlen($name) === 0) {
        $error = true;
        $name_error = 'Please enter amenity name!';
    }

    if (!$error) {
        $data = ['name' => $name];
        $where = ['id' => $id];
        $updated = updateData('amenities', $mysqli, $data, $where);

        if ($updated) {
            $url = $admin_base_url . 'amenity_list.php?success=Amenity Updated Successfully';
            header("Location: $url");
            exit();
        } else {
            $error = true;
            $error_msg = 'Something went wrong!';
        }
    }
}

require './layouts/header.php';
?>
<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Edit Amenity</h1>
            <a href="<?= $admin_base_url . 'amenity_list.php' ?>" class="btn btn-dark">Back</a>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-md-6">
                <?php if ($error && $error_msg): ?>
                    <div class="alert alert-danger">
                        <?= $error_msg ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form action="<?= $admin_base_url . 'amenity_edit.php?edit_id=' . $id ?>" method="POST">
                            <div class="form-group">
                                <label for="name">Amenity Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($name) ?>">
                                <?php if ($error && $name_error): ?>
                                    <span class="text-danger small"><?= $name_error ?></span>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="form_sub" value="1">
                            <button type="submit" class="btn btn-primary w-100">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require './layouts/footer.php'; ?>
