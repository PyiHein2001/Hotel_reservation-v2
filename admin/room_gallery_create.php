<?php
session_start();
require '../require/check_auth.php';
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

$error = false;
$error_msg = '';
$success_msg = '';

// Fetch rooms
$room_result = selectData('rooms', $mysqli);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'] ?? '';
    $images = $_FILES['images'] ?? null;

    if ($room_id === '' || !$images || empty($images['name'][0])) {
        $error = true;
        $error_msg = 'Please select a room and upload at least one image.';
    } else {
        $target_dir = '../uploads/room_galleries/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $allowed_ext = ['jpg', 'jpeg', 'png'];
        foreach ($images['name'] as $key => $name) {
            $tmp_name = $images['tmp_name'][$key];
            $size = $images['size'][$key];
            $error_code = $images['error'][$key];

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if ($error_code === UPLOAD_ERR_OK && in_array($ext, $allowed_ext)) {
                if ($size > 10 * 1024 * 1024) {
                    $error = true;
                    $error_msg .= "$name is too large (max 2MB).<br>";
                    continue;
                }

                $new_filename = uniqid() . '_' . time() . '.' . $ext;
                $destination = $target_dir . $new_filename;

                if (move_uploaded_file($tmp_name, $destination)) {
                    $db_path = 'uploads/room_galleries/' . $new_filename;
                    insertData('room_galleries', $mysqli, [
                        'room_id' => $room_id,
                        'image_path' => $db_path
                    ]);
                } else {
                    $error = true;
                    $error_msg .= "Failed to upload $name.<br>";
                }
            } else {
                $error = true;
                $error_msg .= "$name has invalid format or upload error.<br>";
            }
        }

        if (!$error) {
            $success_msg = 'Images uploaded successfully!';
        }
    }
}

require './layouts/header.php';
?>
<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Upload Room Gallery Images</h1>
            <a href="<?= $admin_base_url . 'room_gallery_list.php' ?>" class="btn btn-dark">Back to List</a>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-md-6">
                <?php if ($error && $error_msg): ?>
                    <div class="alert alert-danger mt-3"><?= $error_msg ?></div>
                <?php elseif ($success_msg): ?>
                    <div class="alert alert-success mt-3"><?= $success_msg ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="room_id">Select Room</label>
                                <select name="room_id" id="room_id" class="form-control">
                                    <option value="">-- Choose Room --</option>
                                    <?php while ($row = $room_result->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>">Room <?= $row['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="images">Upload Images</label>
                                <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                                <div id="preview" class="d-flex flex-wrap mt-3"></div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Script -->
<script>
    document.getElementById('images').addEventListener('change', function (event) {
        const preview = document.getElementById('preview');
        preview.innerHTML = '';

        Array.from(event.target.files).forEach(file => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.height = '100px';
                img.style.marginRight = '10px';
                img.style.marginBottom = '10px';
                img.style.borderRadius = '6px';
                img.style.objectFit = 'cover';
                img.style.boxShadow = '0 0 4px rgba(0,0,0,0.2)';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
</script>

<?php require './layouts/footer.php'; ?>
