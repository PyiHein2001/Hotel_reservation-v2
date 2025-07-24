<?php
session_start();
require '../require/check_auth.php';
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

$res = selectData('amenities', $mysqli, "*", "ORDER BY created_at DESC");
$delete_id = isset($_GET['delete_id']) ? $_GET['delete_id'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';

if ($delete_id !== '') {
    $res = deleteData('amenities', $mysqli, "id=$delete_id");
    if ($res) {
        $url = $admin_base_url . 'amenity_list.php?success=Amenity Deleted Successfully!';
        header("Location: $url");
        exit();
    }
}

require './layouts/header.php';
?>
<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Amenity List</h1>
            <a href="<?= $admin_base_url . 'amenity_create.php' ?>" class="btn btn-primary">Create Amenity</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success mt-2">
                <?= $success ?>
            </div>
        <?php endif; ?>

        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($res->num_rows > 0): 
                                $i = 1;
                                while ($row = $res->fetch_assoc()): ?>
                                <tr id="row-<?= $row['id'] ?>">
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= $row['created_at'] ?></td>
                                    <td>
                                        <a href="<?= $admin_base_url ?>amenity_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>">Delete</button>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="4" class="text-center">No amenities found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            
    </div>
</div>
<!-- SweetAlert Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    $('.delete-btn').click(function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete the amenity.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'amenity_list.php?delete_id=' + id;
            }
        });
    });
});
</script>
<?php require './layouts/footer.php'; ?>
