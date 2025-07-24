<?php 
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

$error = false;
$name = '';
$name_error = '';
$success = '';

// Function to check for duplicates
function is_duplicate_bed_type($name, $mysqli) {
    $query = "SELECT COUNT(*) AS count FROM `bed_types` WHERE name = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $data['count'] > 0;
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == '1') {
    $name = trim($mysqli->real_escape_string($_POST['name']));

    // Validation
    if (empty($name)) {
        $error = true;
        $name_error = 'Please enter a bed type name!';
    } elseif (strlen($name) < 2 || strlen($name) > 100) {
        $error = true;
        $name_error = 'Bed type name must be between 2 and 100 characters!';
    } elseif (is_duplicate_bed_type($name, $mysqli)) {
        $error = true;
        $name_error = 'This bed type already exists!';
    }

    // Insert if valid
    if (!$error) {
        $data = ['name' => $name];
        $result = insertData('bed_types', $mysqli, $data);

        if ($result) {
            $success = 'Bed type added successfully!';
            $name = ''; // Clear input
        } else {
            $error = true;
            $name_error = 'Something went wrong while inserting!';
        }
    }
}

require './layouts/header.php';
?>

<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Create Bed Type</h1>
            <a href="<?= $admin_base_url ?>bed_type_list.php" class="btn btn-dark">Back to List</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

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
                    <button type="submit" class="btn btn-primary w-100">Save Bed Type</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require './layouts/footer.php'; ?>
