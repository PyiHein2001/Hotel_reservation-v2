<?php
session_start();
require '../require/check_auth.php';
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

// Fetch bookings with related customer and room data
$query = "SELECT b.*, c.name AS customer_name, c.phone, r.name AS room_name, r.price_per_day, r.extra_bed_price_per_day
          FROM bookings b
          JOIN customers c ON b.customer_id = c.id
          JOIN rooms r ON b.room_id = r.id
          ORDER BY b.id DESC";
$result = $mysqli->query($query);

require './layouts/header.php';
?>
<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Booking List</h2>
            <a href="<?= $admin_base_url . 'booking_create.php' ?>" class="btn btn-primary">New Booking</a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Room</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Extra Bed</th>
                            <th>Total Price (MMK)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; while($row = $result->fetch_assoc()): ?>
                            <tr id="row-<?= $row['id'] ?>">
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td><?= htmlspecialchars($row['room_name']) ?></td>
                                <td><?= $row['check_in_date'] ?></td>
                                <td><?= $row['check_out_date'] ?></td>
                                <td><?= $row['is_extra_bed'] ? 'Yes' : 'No' ?></td>
                                <td><?= number_format($row['price'], 0) ?></td>
                                <td><?= $row['status'] == 1 ? 'Booked' : 'Cancelled' ?></td>
                                <td>
                                    <a href="booking_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                    <form method="POST" action="booking_delete.php" class="delete-booking-form" style="display:inline-block;">
                                        <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                                        <button type="button" class="btn btn-danger btn-sm delete-booking-btn delete-btn" data-id="<?= $row['id'] ?>">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No bookings found.</div>
        <?php endif; ?>
    </div>
</div>
<?php require './layouts/footer.php'; ?>

<!-- SweetAlert Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    $('.delete-btn').click(function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete the booking.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                // For booking, submit the form
                $(this).closest('form').submit();
            }
        });
    });
});
</script>
