<?php
session_start();
require '../require/check_auth.php';
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

$room_id = $_GET['room_id'] ?? '';
if (!$room_id) {
    header("Location: {$admin_base_url}room_gallery_list.php");
    exit();
}

$room_query = selectData('rooms', $mysqli, '*', "WHERE id = $room_id");
$room = $room_query->fetch_assoc();
$gallery_query = selectData('room_galleries', $mysqli, '*', "WHERE room_id = $room_id");

$success_msg = $error_msg = '';
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delete_ids = $_POST['delete_images'] ?? [];
    $images = $_FILES['images'] ?? null;

    // Delete selected images
    if (!empty($delete_ids)) {
        foreach ($delete_ids as $id) {
            $image_query = selectData('room_galleries', $mysqli, '*', "WHERE id = $id");
            if ($image_query->num_rows > 0) {
                $img = $image_query->fetch_assoc();
                $img_path = '../' . $img['image_path'];
                if (file_exists($img_path)) unlink($img_path);
                deleteData('room_galleries', $mysqli, "id=$id");
            }
        }
    }

    // Upload new images
    $target_dir = '../uploads/room_galleries/';
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    $allowed_ext = ['jpg', 'jpeg', 'png'];

    if (!empty($images['name'][0])) {
        foreach ($images['name'] as $key => $name) {
            $tmp_name = $images['tmp_name'][$key];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $size = $images['size'][$key];

            if (!in_array($ext, $allowed_ext)) {
                $error = true;
                $error_msg .= "$name has invalid format.<br>";
                continue;
            }
            if ($size > 10 * 1024 * 1024) {
                $error = true;
                $error_msg .= "$name is too large (max 2MB).<br>";
                continue;
            }

            $filename = uniqid() . '_' . time() . '.' . $ext;
            $dest_path = $target_dir . $filename;

            if (move_uploaded_file($tmp_name, $dest_path)) {
                insertData('room_galleries', $mysqli, [
                    'room_id' => $room_id,
                    'image_path' => 'uploads/room_galleries/' . $filename
                ]);
            } else {
                $error = true;
                $error_msg .= "Failed to upload $name.<br>";
            }
        }
    }

    if (!$error) {
        $success = 'Gallery updated successfully.';
        header("Location: {$admin_base_url}room_gallery_list.php?success=" . urlencode($success));
        exit();
    }
}

require './layouts/header.php';
?>

<div class="content-body">
    <div class="container-fluid">
        <h1>Edit Room Gallery - <?= htmlspecialchars($room['name']) ?></h1>
        <a href="<?= $admin_base_url ?>room_gallery_list.php" class="btn btn-dark mb-3">Back to List</a>

        <?php if ($error_msg): ?>
            <div class="alert alert-danger"><?= $error_msg ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Existing Images:</label>
                <div class="row">
                    <?php while ($img = $gallery_query->fetch_assoc()): ?>
                        <div class="col-md-3 text-center mb-3">
                            <img src="../<?= $img['image_path'] ?>" class="img-thumbnail" style="height:150px; object-fit:cover;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>" id="delete_<?= $img['id'] ?>">
                                <label class="form-check-label" for="delete_<?= $img['id'] ?>">Delete</label>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="images">Add New Images:</label>
                <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                <div class="row mt-3" id="image-preview"></div>
            </div>

            <button type="submit" class="btn btn-primary">Update Gallery</button>
        </form>
    </div>
</div>

<?php require './layouts/footer.php'; ?>

<script>
    document.getElementById('images').addEventListener('change', function () {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';
        [...this.files].forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const col = document.createElement('div');
                col.classList.add('col-md-3', 'mb-2');
                col.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="height:150px; object-fit:cover;">`;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    });
</script>
