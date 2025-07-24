<?php
session_start();
require '../require/check_auth.php';
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

// Handle delete request if ID is passed in query
$delete_id = $_GET['delete_id'] ?? '';
if ($delete_id !== '') {
    $res = deleteData('customers', $mysqli, "id = $delete_id");
    if ($res) {
        $url = $admin_base_url . 'customer_list.php?delete_success=Customer deleted successfully!';
        header("Location: $url");
        exit();
    }
}

// Fetch customers
$customers = selectData("customers", $mysqli);
require './layouts/header.php';
?>

<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Customer List</h2>
            <a href="<?= $admin_base_url ?>customer_create.php" class="btn btn-primary">Add Customer</a>
        </div>

        <?php if (isset($_GET['delete_success'])): ?>
            <div class="alert alert-success"><?= $_GET['delete_success'] ?></div>
        <?php endif; ?>

        <?php if ($customers->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $customers->fetch_assoc()): ?>
                            <tr id="row-<?= $row['id'] ?>">
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= $row['created_at'] ?></td>
                                <td>
                                    <a href="<?= $admin_base_url ?>customer_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No customers found.</div>
        <?php endif; ?>
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
            text: 'This will permanently delete the customer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'customer_list.php?delete_id=' + id;
            }
        });
    });
});
</script>

<?php require './layouts/footer.php'; ?>
