<?php 
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

// Handle delete
$delete_id = isset($_GET['delete_id']) ? $mysqli->real_escape_string($_GET['delete_id']) : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
$delete_success = isset($_GET['delete_success']) ? $_GET['delete_success'] : '';

if ($delete_id !== '') {
    $res = deleteData('rooms', $mysqli, "id = $delete_id");
    if ($res) {
        header("Location: {$admin_base_url}room_list.php?delete_success=Room deleted successfully!");
        exit();
    }
}

// Fetch rooms
$sql = "SELECT r.*, b.name AS bed_type 
        FROM rooms r 
        JOIN bed_types b ON r.bed_type_id = b.id 
        ORDER BY r.created_at DESC";
$res = $mysqli->query($sql);

require './layouts/header.php';
?>

<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Room List</h1>
            <a href="<?= $admin_base_url ?>room_create.php" class="btn btn-primary btn-sm">Create Room</a>
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
                                <th>No</th>
                                <th>Room Name</th>
                                <th>Bed Type</th>
                                <th>Occupancy</th>
                                <th>Size (sqm)</th>
                                <th>Price/Day</th>
                                <th>Extra Bed Price</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($res && $res->num_rows > 0): 
                                $i = 1;
                                while ($row = $res->fetch_assoc()): ?>
                                <tr id="row-<?= $row['id'] ?>">
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['bed_type']) ?></td>
                                    <td><?= $row['occupancy'] ?></td>
                                    <td><?= $row['size'] ?></td>
                                    <td><?= number_format($row['price_per_day']) ?> Ks</td>
                                    <td><?= number_format($row['extra_bed_price_per_day']) ?> Ks</td>
                                    <td><?= date('Y-m-d g:i A', strtotime($row['created_at'])) ?></td>
                                    <td><?= date('Y-m-d g:i A', strtotime($row['updated_at'])) ?></td>
                                    <td>
                                        <a href="<?= $admin_base_url . 'room_edit.php?edit_id=' . $row['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>">Delete</button>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="10" class="text-center">No rooms found.</td></tr>
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
            text: 'This will permanently delete the room.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'room_list.php?delete_id=' + id;
            }
        });
    });
});
</script>

<?php require './layouts/footer.php'; ?>
