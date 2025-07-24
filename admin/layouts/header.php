<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../require/check_auth.php';
require '../require/common.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Hotel Management System</title>
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <link href="./plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet">
    <link href="./plugins/clockpicker/dist/jquery-clockpicker.min.css" rel="stylesheet">
    <link href="./plugins/jquery-asColorPicker-master/css/asColorPicker.css" rel="stylesheet">
    <link href="./plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="./plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="./plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="js/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="js/jQuery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
</head>

<body>
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>

    <div id="main-wrapper">
        <div class="nav-header">
            <div class="brand-logo">
                <a href="index.html">
                    <b class="logo-abbr"><img src="images/logo.png" alt=""> </b>
                    <span class="logo-compact"><img src="./images/logo-compact.png" alt=""></span>
                    <span class="brand-title">
                        <img src="images/logo-text.png" alt="">
                    </span>
                </a>
            </div>
        </div>

        <div class="header">
            <div class="header-content clearfix">
                <div class="nav-control">
                    <div class="hamburger">
                        <span class="toggle-icon"><i class="icon-menu"></i></span>
                    </div>
                </div>
                <div class="header-left">
                    <div class="input-group icons">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-transparent border-0 pr-2 pr-sm-3" id="basic-addon1"><i class="mdi mdi-magnify"></i></span>
                        </div>
                        <input type="search" class="form-control" placeholder="Search Dashboard" aria-label="Search Dashboard">
                    </div>
                </div>
                <div class="header-right">
                    <ul class="clearfix">
                        <li class="icons dropdown">
                            <a href="javascript:void(0)" data-toggle="dropdown">
                                <i class="mdi mdi-email-outline"></i>
                                <span class="badge gradient-1 badge-pill badge-primary">3</span>
                            </a>
                            <div class="drop-down animated fadeIn dropdown-menu">
                                <div class="dropdown-content-body">
                                    <ul>
                                        <li><a href="#">New message from Admin</a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="icons dropdown">
                            <a href="javascript:void(0)" data-toggle="dropdown">
                                <i class="mdi mdi-bell-outline"></i>
                                <span class="badge badge-pill gradient-2 badge-primary">3</span>
                            </a>
                            <div class="drop-down animated fadeIn dropdown-menu">
                                <div class="dropdown-content-body">
                                    <ul>
                                        <li><a href="#">New booking alert</a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="icons dropdown">
                            <a class="user-img c-pointer position-relative dropdown-toggle" href="#" id="profileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button">
                                
                                <img src="images/user/1.png" height="40" width="40" alt="">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-profile animated fadeIn" aria-labelledby="profileDropdown">
                                <div class="dropdown-content-body">
                                    <ul>
                                        <li><a href="<?= $admin_base_url ?>profile_setting.php"><i class="icon-user"></i> <span>Profile</span></a></li>
                                        <li><a href="<?= $admin_base_url ?>logout.php"><i class="icon-key"></i> <span>Logout</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="nk-sidebar">
            <div class="nk-nav-scroll">
                <ul class="metismenu" id="menu">
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="icon-home menu-icon"></i><span class="nav-text">Rooms</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="<?= $admin_base_url . 'room_list.php' ?>">Room List</a></li>
                            <li><a href="<?= $admin_base_url . 'room_create.php' ?>">Room Create</a></li>
                        </ul>
                    </li>
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="icon-tag menu-icon"></i><span class="nav-text">Bed Type</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="<?= $admin_base_url . 'bed_type_list.php' ?>">Bed Type List</a></li>
                            <li><a href="<?= $admin_base_url . 'bed_type_create.php' ?>">Bed Type Create</a></li>
                        </ul>
                    </li>
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="icon-graph menu-icon"></i><span class="nav-text">Hotel Setting</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="<?= $admin_base_url . 'hotel_setting_list.php' ?>">Hotel Setting</a></li>
                            <li><a href="<?= $admin_base_url . 'hotel_setting_create.php' ?>">Hotel Setting Create</a></li>
                        </ul>
                    </li>
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="icon-check menu-icon"></i><span class="nav-text">Amenities</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="<?= $admin_base_url . 'amenity_list.php' ?>">Amenity List</a></li>
                            <li><a href="<?= $admin_base_url . 'amenity_create.php' ?>">Amenity Create</a></li>
                        </ul>
                    </li>
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="icon-picture menu-icon"></i><span class="nav-text">Room Galleries</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="<?= $admin_base_url . 'room_gallery_list.php' ?>">Room Gallery List</a></li>
                            <li><a href="<?= $admin_base_url . 'room_gallery_create.php' ?>">Upload Room Images</a></li>
                        </ul>
                    </li>
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="icon-note menu-icon"></i><span class="nav-text">Bookings</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="<?= $admin_base_url . 'booking_list.php' ?>">Booking List</a></li>
                            <li><a href="<?= $admin_base_url . 'booking_create.php' ?>">Booking Create</a></li>
                        </ul>
                    </li>
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="icon-people menu-icon"></i><span class="nav-text">Customers</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="<?= $admin_base_url . 'customer_list.php' ?>">Customer List</a></li>
                            <li><a href="<?= $admin_base_url . 'customer_create.php' ?>">Customer Create</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?= $admin_base_url . 'reports.php' ?>">
                            <i class="icon-chart menu-icon"></i><span class="nav-text">Reports</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $admin_base_url . 'profile_setting.php' ?>">
                            <i class="icon-user menu-icon"></i><span class="nav-text">Profile Setting</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Sidebar end -->
