<?php
session_start();
require '../require/check_auth.php';
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch customers and rooms for dropdown
$customers = selectData("customers", $mysqli);
$rooms = $mysqli->query("SELECT r.*, bt.name AS bed_type, GROUP_CONCAT(a.name SEPARATOR ', ') AS amenities
                        FROM rooms r
                        JOIN bed_types bt ON r.bed_type_id = bt.id
                        LEFT JOIN room_amenities ra ON r.id = ra.room_id
                        LEFT JOIN amenities a ON a.id = ra.amenity_id
                        GROUP BY r.id");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = (int)$_POST['room_id'];
    $customer_id = (int)$_POST['customer_id'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $is_extra_bed = isset($_POST['is_extra_bed']) ? 1 : 0;

    $check_in = new DateTime($check_in_date);
    $check_out = new DateTime($check_out_date);
    $interval = $check_in->diff($check_out);
    $total_days = $interval->days;

    if ($total_days <= 0) {
        echo "<script>alert('Check-out date must be after check-in date');</script>";
    } else {
        // Check for overlapping bookings for the same room
        $overlap_query = "SELECT COUNT(*) as cnt FROM bookings WHERE room_id = $room_id AND status = 1 AND (
            (check_in_date < '$check_out_date' AND check_out_date > '$check_in_date')
        )";
        $overlap_result = $mysqli->query($overlap_query);
        $overlap = $overlap_result ? $overlap_result->fetch_assoc()['cnt'] : 0;
        if ($overlap > 0) {
            echo "<script>Swal.fire({icon: 'error', title: 'Room Not Available', text: 'This room is already booked for the selected dates. Please choose another room or different dates.'});</script>";
        } else {
            $room_result = selectData("rooms", $mysqli, "*", "WHERE id = $room_id");
            if ($room_result && $room_result->num_rows > 0) {
                $room = $room_result->fetch_assoc();
                $price = ($room['price_per_day'] + ($is_extra_bed ? $room['extra_bed_price_per_day'] : 0)) * $total_days;

                $values = [
                    'room_id' => $room_id,
                    'customer_id' => $customer_id,
                    'check_in_date' => $check_in_date,
                    'check_out_date' => $check_out_date,
                    'is_extra_bed' => $is_extra_bed,
                    'price' => $price,
                    'status' => 1,
                    'booked_by_user_id' => isset($_SESSION['id']) ? $_SESSION['id'] : null
                ];

                if (insertData("bookings", $mysqli, $values)) {
                    echo "<script>alert('Booking created successfully'); window.location.href='booking_list.php';</script>";
                } else {
                    echo "<script>alert('Failed to create booking.');</script>";
                }
                exit();
            } else {
                echo "<script>alert('Room not found or invalid room ID.');</script>";
            }
        }
    }
}
?>

<?php require './layouts/header.php'; ?>
<div class="content-body">
    <div class="container-fluid">
        <h2>Create Booking</h2>
        <form method="POST" class="mt-4">
            <div class="form-group">
                <label for="room_id">Select Room</label>
                <select name="room_id" id="room_id" class="form-control" required>
                    <option value="">-- Select Room --</option>
                    <?php while($r = $rooms->fetch_assoc()): ?>
                        <option value='<?= $r['id'] ?>' data-price='<?= $r['price_per_day'] ?>' data-extra='<?= $r['extra_bed_price_per_day'] ?>' data-amenities='<?= $r['amenities'] ?? "None" ?>'>
                            <?= $r['name'] ?> (<?= $r['bed_type'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div id="roomDetails" class="mb-3" style="display: none;">
                <p><strong>Base Price per Day:</strong> <span id="roomPrice"></span> MMK</p>
                <p><strong>Amenities:</strong> <span id="roomAmenities"></span></p>
            </div>

            <div class="form-group">
                <label for="customer_id">Select Customer</label>
                <select name="customer_id" class="form-control" required>
                    <option value="">-- Select Customer --</option>
                    <?php while($c = $customers->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>"><?= $c['name'] ?> (<?= $c['phone'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Check In Date</label>
                <input type="date" name="check_in_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Check Out Date</label>
                <input type="date" name="check_out_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label><input type="checkbox" id="extraBed" name="is_extra_bed"> Add Extra Bed</label>
            </div>

            <div class="form-group">
                <label>Total Price</label>
                <input type="text" id="totalPrice" class="form-control" readonly>
            </div>

            <button type="submit" class="btn btn-primary">Book Now</button>
        </form>
    </div>
</div>

<script>
$(document).ready(function () {
    let base = 0;
    let extra = 0;

    // Set min date for check-in and check-out to today
    const today = new Date().toISOString().split('T')[0];
    $('input[name="check_in_date"]').attr('min', today);
    $('input[name="check_out_date"]').attr('min', today);

    // When check-in changes, set min for check-out
    $('input[name="check_in_date"]').on('change', function() {
        const checkIn = $(this).val();
        $('input[name="check_out_date"]').attr('min', checkIn);
        // If check-out is before new check-in, clear it
        if ($('input[name="check_out_date"]').val() < checkIn) {
            $('input[name="check_out_date"]').val('');
        }
        updateTotal();
    });

    $('#room_id').change(function () {
        const selected = $(this).find('option:selected');
        base = parseFloat(selected.data('price')) || 0;
        extra = parseFloat(selected.data('extra')) || 0;
        const amenities = selected.data('amenities') || 'None';

        $('#roomPrice').text(base);
        $('#roomAmenities').text(amenities);
        $('#roomDetails').show();

        updateTotal();
    });

    $('#extraBed, input[name="check_in_date"], input[name="check_out_date"]').change(function () {
        updateTotal();
    });

    function updateTotal() {
        let total = base;
        if ($('#extraBed').is(':checked')) {
            total += extra;
        }

        const inDate = new Date($('input[name="check_in_date"]').val());
        const outDate = new Date($('input[name="check_out_date"]').val());
        const diffDays = Math.ceil((outDate - inDate) / (1000 * 60 * 60 * 24));

        if (!isNaN(diffDays) && diffDays > 0) {
            $('#totalPrice').val((total * diffDays) + ' MMK');
        } else {
            $('#totalPrice').val('Select valid dates');
        }
    }

    // Intercept form submission to check room availability via AJAX
    $('form[method="POST"]').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const room_id = $('#room_id').val();
        const check_in_date = $('input[name="check_in_date"]').val();
        const check_out_date = $('input[name="check_out_date"]').val();

        if (!room_id || !check_in_date || !check_out_date) {
            Swal.fire({icon: 'warning', title: 'Missing Data', text: 'Please select room and dates.'});
            return;
        }

        $.post('check_room_availability.php', {
            room_id: room_id,
            check_in_date: check_in_date,
            check_out_date: check_out_date
        }, function(response) {
            let res = {};
            try { res = JSON.parse(response); } catch (e) {}
            if (res.available) {
                form.submit(); // Room is available, submit the form
            } else {
                Swal.fire({icon: 'error', title: 'Room Not Available', text: res.message || 'This room is already booked for the selected dates.'});
            }
        });
    });
});
</script>
<?php require './layouts/footer.php'; ?>
