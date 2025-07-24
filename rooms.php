<?php

require 'require/db.php';
require 'require/common.php';
require 'require/common_function.php';


require_once 'UI/header.php';


$rooms = $mysqli->query("SELECT * FROM rooms");

?>

    <section class="site-hero inner-page overlay" style="background-image: url(images/hero_4.jpg)" data-stellar-background-ratio="0.5">
      <div class="container">
        <div class="row site-hero-inner justify-content-center align-items-center">
          <div class="col-md-10 text-center" data-aos="fade">
            <h1 class="heading mb-3">Rooms</h1>
            <ul class="custom-breadcrumbs mb-4">
              <li><a href="index.html">Home</a></li>
              <li>&bullet;</li>
              <li>Rooms</li>
            </ul>
          </div>
        </div>
      </div>

      <a class="mouse smoothscroll" href="#next">
        <div class="mouse-icon">
          <span class="mouse-wheel"></span>
        </div>
      </a>
    </section>
    <!-- END section -->

    <section class="section pb-4">
      <div class="container">
       
        <div class="row check-availabilty" id="next">
          <div class="block-32" data-aos="fade-up" data-aos-offset="-200">

            <form id="availabilityForm" action="#">
              <div class="row">
                <div class="col-md-6 mb-3 mb-lg-0 col-lg-3">
                  <label for="checkin_date" class="font-weight-bold text-black">Check In</label>
                  <div class="field-icon-wrap">
                    <div class="icon"><span class="icon-calendar"></span></div>
                    <input type="text" id="checkin_date" class="form-control">
                  </div>
                </div>
                <div class="col-md-6 mb-3 mb-lg-0 col-lg-3">
                  <label for="checkout_date" class="font-weight-bold text-black">Check Out</label>
                  <div class="field-icon-wrap">
                    <div class="icon"><span class="icon-calendar"></span></div>
                    <input type="text" id="checkout_date" class="form-control">
                  </div>
                </div>
                <div class="col-md-6 mb-3 mb-md-0 col-lg-3">
                  <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                      <label for="adults" class="font-weight-bold text-black">Adults</label>
                      <div class="field-icon-wrap">
                        <div class="icon"><span class="ion-ios-arrow-down"></span></div>
                        <select name="" id="adults" class="form-control">
                          <option value="">1</option>
                          <option value="">2</option>
                          <option value="">3</option>
                          <option value="">4+</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6 mb-3 mb-md-0">
                      <label for="children" class="font-weight-bold text-black">Children</label>
                      <div class="field-icon-wrap">
                        <div class="icon"><span class="ion-ios-arrow-down"></span></div>
                        <select name="" id="children" class="form-control">
                          <option value="">1</option>
                          <option value="">2</option>
                          <option value="">3</option>
                          <option value="">4+</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-lg-3 align-self-end">
                  <button class="btn btn-primary btn-block text-white">Check Availabilty</button>
                </div>
              </div>
            </form>
          </div>


        </div>
      </div>
    </section>

    
    <section class="section">
      <div class="container">

        <div class="row">
          <?php while ($room = $rooms->fetch_assoc()): ?>
            <?php
              // Fetch gallery images for this room
              $gallery = $mysqli->query("SELECT image_path FROM room_galleries WHERE room_id = {$room['id']}");
              $images = [];
              while ($img = $gallery->fetch_assoc()) {
                $images[] = $img['image_path'];
              }
              $main_img = !empty($images) ? $images[0] : 'images/img_1.jpg';
              $carousel_id = 'carouselRoom' . $room['id'];
            ?>
            <div class="col-md-6 col-lg-4 mb-5" data-aos="fade-up">
              <a href="#" class="room">
                <figure class="img-wrap">
                  <?php if (!empty($images)): ?>
                  <div id="<?= $carousel_id ?>" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                      <?php foreach ($images as $index => $img_path): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                          <img src="<?= htmlspecialchars($img_path) ?>" class="d-block w-100" style="height:250px; object-fit:cover;" alt="Room Image">
                        </div>
                      <?php endforeach; ?>
                    </div>
                    <?php if (count($images) > 1): ?>
                    <a class="carousel-control-prev" href="#<?= $carousel_id ?>" role="button" data-slide="prev">
                      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                      <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#<?= $carousel_id ?>" role="button" data-slide="next">
                      <span class="carousel-control-next-icon" aria-hidden="true"></span>
                      <span class="sr-only">Next</span>
                    </a>
                    <?php endif; ?>
                  </div>
                  <?php else: ?>
                  <img src="<?= htmlspecialchars($main_img) ?>" alt="Room Image" class="img-fluid mb-3">
                  <?php endif; ?>
                </figure>
                <div class="p-3 text-center room-info">
                  <h2><?= htmlspecialchars($room['name']) ?></h2>
                  <span class="text-uppercase letter-spacing-1"><?= number_format($room['price_per_day']) ?> MMK / per night</span>
                </div>
              </a>
            </div>
          <?php endwhile; ?>
        </div>
        
      </div>
    </section>
    
    <section class="section bg-light">

      <div class="container">
        <div class="row justify-content-center text-center mb-5">
          <div class="col-md-7">
            <h2 class="heading" data-aos="fade">Great Offers</h2>
            <p data-aos="fade">Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.</p>
          </div>
        </div>
      
        <div class="site-block-half d-block d-lg-flex bg-white" data-aos="fade" data-aos-delay="100">
          <a href="#" class="image d-block bg-image-2" style="background-image: url('images/img_1.jpg');"></a>
          <div class="text">
            <span class="d-block mb-4"><span class="display-4 text-primary">$199</span> <span class="text-uppercase letter-spacing-2">/ per night</span> </span>
            <h2 class="mb-4">Family Room</h2>
            <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.</p>
            <p><a href="#" class="btn btn-primary text-white">Book Now</a></p>
          </div>
        </div>
        <div class="site-block-half d-block d-lg-flex bg-white" data-aos="fade" data-aos-delay="200">
          <a href="#" class="image d-block bg-image-2 order-2" style="background-image: url('images/img_2.jpg');"></a>
          <div class="text order-1">
            <span class="d-block mb-4"><span class="display-4 text-primary">$299</span> <span class="text-uppercase letter-spacing-2">/ per night</span> </span>
            <h2 class="mb-4">Presidential Room</h2>
            <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.</p>
            <p><a href="#" class="btn btn-primary text-white">Book Now</a></p>
          </div>
        </div>

      </div>
    </section>

    <section class="section bg-image overlay" style="background-image: url('images/hero_4.jpg');">
      <div class="container" >
        <div class="row align-items-center">
          <div class="col-12 col-md-6 text-center mb-4 mb-md-0 text-md-left" data-aos="fade-up">
            <h2 class="text-white font-weight-bold">A Best Place To Stay. Reserve Now!</h2>
          </div>
          <div class="col-12 col-md-6 text-center text-md-right" data-aos="fade-up" data-aos-delay="200">
            <a href="reservation.html" class="btn btn-outline-white-primary py-3 text-white px-5">Reserve Now</a>
          </div>
        </div>
      </div>
    </section>
    
<?php require_once 'UI/footer.php'; ?>

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