<?php
session_start();
require '../require/check_auth.php';
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

// Fetch room gallery groups
$query = "SELECT r.id AS room_id, r.name AS room_name, GROUP_CONCAT(rg.id) AS gallery_ids, GROUP_CONCAT(rg.image_path) AS images
          FROM rooms r
          LEFT JOIN room_galleries rg ON r.id = rg.room_id
          GROUP BY r.id
          ORDER BY r.id DESC";
$result = $mysqli->query($query);

require './layouts/header.php';
?>
<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Room Gallery List</h1>
            <a href="<?= $admin_base_url . 'room_gallery_create.php' ?>" class="btn btn-primary">Upload New Images</a>
        </div>

        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()):
                    $images = explode(',', $row['images']);
                    $gallery_ids = explode(',', $row['gallery_ids']);
                    $room_id = $row['room_id'];
                ?>
                    <div class="col-md-4 col-sm-6 mb-4 card-room" id="card-room-<?= $room_id ?>">
                        <div class="card">
                            <div id="carouselRoom<?= $room_id ?>" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <?php foreach ($images as $index => $img_path): ?>
                                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                            <img src="../<?= htmlspecialchars($img_path) ?>" class="d-block w-100" style="height:250px; object-fit:cover;" alt="Room Image">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <a class="carousel-control-prev" href="#carouselRoom<?= $room_id ?>" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselRoom<?= $room_id ?>" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Room: <?= htmlspecialchars($row['room_name']) ?></h5>
                                <button class="btn btn-danger btn-sm delete-room-gallery" data-room-id="<?= $room_id ?>">Delete All Images</button>
                                <a href="<?= $admin_base_url . 'room_gallery_edit.php?room_id=' . $room_id ?>" class="btn btn-secondary btn-sm ml-2">Edit</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">No images uploaded yet.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require './layouts/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('.delete-room-gallery').click(function () {
            const roomId = $(this).data('room-id');
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will delete all images for this room!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('room_gallery_delete.php', { room_id: roomId }, function(response) {
                        if (response.trim() === 'success') {
                            Swal.fire('Deleted!', 'Images deleted successfully.', 'success');
                            $('#card-room-' + roomId).remove();
                        } else {
                            Swal.fire('Error!', 'Failed to delete from database.', 'error');
                        }
                    }).fail(function() {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    });
                }
            });
        });
    });
</script>
