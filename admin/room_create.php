<?php 
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

$error = false;
$error_msg = '';
$fields = [
    'name' => '', 'size' => '', 'occupancy' => '', 'bed_type_id' => '',
    'price_per_day' => '', 'extra_bed_price_per_day' => '',
    'description' => '', 'detail' => ''
];
$errors = array_fill_keys(array_keys($fields), '');

// Fetch bed types for dropdown
$bed_types = selectData('bed_types', $mysqli, '*', 'ORDER BY name ASC');

if (isset($_POST['form_sub']) && $_POST['form_sub'] == '1') {
    foreach ($fields as $key => $_) {
        $fields[$key] = $mysqli->real_escape_string($_POST[$key] ?? '');
    }

    // Validate fields
    if (empty($fields['name'])) {
        $error = true;
        $errors['name'] = 'Please enter room name!';
    }

    if (!is_numeric($fields['size']) || $fields['size'] <= 0) {
        $error = true;
        $errors['size'] = 'Enter valid room size!';
    }

    if (!is_numeric($fields['occupancy']) || $fields['occupancy'] <= 0) {
        $error = true;
        $errors['occupancy'] = 'Enter valid occupancy number!';
    }

    if (!is_numeric($fields['price_per_day']) || $fields['price_per_day'] <= 0) {
        $error = true;
        $errors['price_per_day'] = 'Enter valid price!';
    }

    if (!is_numeric($fields['extra_bed_price_per_day']) || $fields['extra_bed_price_per_day'] < 0) {
        $error = true;
        $errors['extra_bed_price_per_day'] = 'Enter valid extra bed price!';
    }

    if (!$error) {
        $data = [
            'name' => $fields['name'],
            'size' => $fields['size'],
            'occupancy' => $fields['occupancy'],
            'bed_type_id' => $fields['bed_type_id'],
            'description' => $fields['description'],
            'detail' => $fields['detail'],
            'price_per_day' => $fields['price_per_day'],
            'extra_bed_price_per_day' => $fields['extra_bed_price_per_day']
        ];

        $result = insertData('rooms', $mysqli, $data);

        if ($result) {
            header("Location: {$admin_base_url}room_list.php?success=Room created successfully");
            exit();
        } else {
            $error = true;
            $error_msg = 'Failed to create room!';
        }
    }
}

require './layouts/header.php';
?>

<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Create Room</h1>
            <a href="<?= $admin_base_url ?>room_list.php" class="btn btn-dark">Back</a>
        </div>

        <?php if ($error && $error_msg): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form action="<?= $admin_base_url ?>room_create.php" method="POST">
                    <div class="form-group">
                        <label for="name">Room Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($fields['name']) ?>">
                        <?php if ($errors['name']): ?><span class="text-danger"><?= $errors['name'] ?></span><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="size">Room Size (sqm)</label>
                        <input type="number" name="size" class="form-control" value="<?= htmlspecialchars($fields['size']) ?>" step="0.1">
                        <?php if ($errors['size']): ?><span class="text-danger"><?= $errors['size'] ?></span><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="occupancy">Occupancy</label>
                        <input type="number" name="occupancy" class="form-control" value="<?= htmlspecialchars($fields['occupancy']) ?>">
                        <?php if ($errors['occupancy']): ?><span class="text-danger"><?= $errors['occupancy'] ?></span><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="bed_type_id">Bed Type</label>
                        <select name="bed_type_id" class="form-control">
                            <option value="">-- Select Bed Type --</option>
                            <?php while ($bed = $bed_types->fetch_assoc()): ?>
                                <option value="<?= $bed['id'] ?>" <?= ($fields['bed_type_id'] == $bed['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($bed['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price_per_day">Price per Day</label>
                        <input type="number" name="price_per_day" class="form-control" value="<?= htmlspecialchars($fields['price_per_day']) ?>" step="0.01">
                        <?php if ($errors['price_per_day']): ?><span class="text-danger"><?= $errors['price_per_day'] ?></span><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="extra_bed_price_per_day">Extra Bed Price per Day</label>
                        <input type="number" name="extra_bed_price_per_day" class="form-control" value="<?= htmlspecialchars($fields['extra_bed_price_per_day']) ?>" step="0.01">
                        <?php if ($errors['extra_bed_price_per_day']): ?><span class="text-danger"><?= $errors['extra_bed_price_per_day'] ?></span><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="description">Short Description</label>
                        <textarea name="description" class="form-control"><?= htmlspecialchars($fields['description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="detail">Detailed Info</label>
                        <textarea name="detail" class="form-control" rows="4"><?= htmlspecialchars($fields['detail']) ?></textarea>
                    </div>

                    <input type="hidden" name="form_sub" value="1">
                    <button type="submit" class="btn btn-primary w-100">Create Room</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require './layouts/footer.php'; ?>
