<?php 
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

$res = selectData('hotel_settings', $mysqli, "*", "ORDER BY created_at DESC");
$delete_id = isset($_GET['delete_id']) ? $_GET['delete_id'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
$delete_success = isset($_GET['delete_success']) ? $_GET['delete_success'] : '';

if ($delete_id !== '') {
    $res = deleteData('hotel_settings', $mysqli, "id=$delete_id");
    if ($res) {
        $url = $admin_base_url . 'hotel_setting_list.php?delete_success=Deleted successfully!';
        header("Location: $url");
        exit();
    }
}
require './layouts/header.php';
?>

<!--**********************************
    Content body start
***********************************-->
<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Hotel Settings</h1>
            <a href="<?= $admin_base_url . 'hotel_setting_create.php' ?>" class="btn btn-primary">
                Create Hotel Setting
            </a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif ($delete_success): ?>
            <div class="alert alert-danger"><?= $delete_success ?></div>
        <?php endif; ?>

        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
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
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['phone']) ?></td>
                                        <td><?= date('Y-m-d g:i A', strtotime($row['created_at'])) ?></td>
                                        <td>
                                            <a href="<?= $admin_base_url . 'hotel_setting_edit.php?edit_id=' . $row['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                            <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php $i++; endwhile; else: ?>
                                <tr><td colspan="6" class="text-center">No hotel settings found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            
        
    </div>
</div>
<!--**********************************
    Content body end
***********************************-->

<!-- SweetAlert Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    $('.delete-btn').click(function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete the hotel setting.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'hotel_setting_list.php?delete_id=' + id;
            }
        });
    });
});
</script>

<?php require './layouts/footer.php'; ?>
