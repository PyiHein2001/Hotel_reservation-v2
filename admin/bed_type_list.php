<?php 
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

// Handle delete request
$delete_id = isset($_GET['delete_id']) ? $mysqli->real_escape_string($_GET['delete_id']) : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
$delete_success = isset($_GET['delete_success']) ? $_GET['delete_success'] : '';

if ($delete_id !== '') {
    $res = deleteData('bed_types', $mysqli, "id = $delete_id");
    if ($res) {
        header("Location: {$admin_base_url}bed_type_list.php?delete_success=Bed type deleted successfully!");
        exit();
    }
}

// Fetch bed types
$res = selectData('bed_types', $mysqli, "*", "ORDER BY created_at DESC");

require './layouts/header.php';
?>

<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Bed Types</h1>
            <a href="<?= $admin_base_url ?>bed_type_create.php" class="btn btn-primary">Create Bed Type</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php elseif ($delete_success): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($delete_success) ?></div>
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
                            <?php if ($res && $res->num_rows > 0): 
                                $i = 1;
                                while ($row = $res->fetch_assoc()): ?>
                                <tr id="row-<?= $row['id'] ?>">
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= $row['created_at'] ?></td>
                                    <td>
                                        <a href="<?= $admin_base_url ?>bed_type_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>">Delete</button>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="4" class="text-center">No bed types found.</td></tr>
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
            text: 'This will permanently delete the bed type.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'bed_type_list.php?delete_id=' + id;
            }
        });
    });
});
</script>

<?php require './layouts/footer.php'; ?>
