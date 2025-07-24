<?php 
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

$error = false;
$name = '';
$name_error = '';
$success = '';

// Get bed type ID
if (!isset($_GET['edit_id']) || !is_numeric($_GET['edit_id'])) {
    header("Location: {$admin_base_url}bed_type_list.php");
    exit();
}

$id = intval($_GET['edit_id']);

// Fetch existing data
$res = selectData('bed_types', $mysqli, "*", "WHERE id = $id");
if ($res && $res->num_rows > 0) {
    $data = $res->fetch_assoc();
    $name = $data['name'];
} else {
    header("Location: {$admin_base_url}bed_type_list.php?error=Invalid bed type ID");
    exit();
}

// Function to check for duplicates (excluding current)
function is_duplicate_bed_type_edit($name, $id, $mysqli) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) AS count FROM bed_types WHERE name = ? AND id != ?");
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'] > 0;
}

// Form submission
if (isset($_POST['form_sub']) && $_POST['form_sub'] == '1') {
    $name = trim($mysqli->real_escape_string($_POST['name']));

    // Validate
    if (empty($name)) {
        $error = true;
        $name_error = 'Please enter a bed type name!';
    } elseif (strlen($name) < 2 || strlen($name) > 100) {
        $error = true;
        $name_error = 'Bed type name must be between 2 and 100 characters!';
    } elseif (is_duplicate_bed_type_edit($name, $id, $mysqli)) {
        $error = true;
        $name_error = 'This bed type already exists!';
    }

    // Update
    if (!$error) {
        $update = updateData('bed_types', $mysqli, ['name' => $name], ['id' => $id]);
        if ($update) {
            header("Location: {$admin_base_url}bed_type_list.php?success=Bed type updated successfully");
            exit();
        } else {
            $error = true;
            $name_error = 'Something went wrong while updating!';
        }
    }
}

require './layouts/header.php';
?>

<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Edit Bed Type</h1>
            <a href="<?= $admin_base_url ?>bed_type_list.php" class="btn btn-dark">Back to List</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="name">Bed Type Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" placeholder="e.g., King Bed">
                        <?php if ($name_error): ?>
                            <span class="text-danger"><?= $name_error ?></span>
                        <?php endif; ?>
                    </div>

                    <input type="hidden" name="form_sub" value="1">
                    <button type="submit" class="btn btn-primary w-100">Update Bed Type</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require './layouts/footer.php'; ?>
