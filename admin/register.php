<?php 
    require '../require/db.php';
    require '../require/common.php';
    $error = FALSE;
    $error_msg ='';
    $name = 
        $email = 
        $phone = 
        $gender = 
        $password = 
        $confirm_password =
        $name_error=
        $email_error=
        $phone_error =
        $password = $confirm_password = '';

    function emailUnique($value, $mysqli){
        $sql = "SELECT count(id) as count FROM `users` WHERE email='$value'";
        $res = $mysqli->query($sql);
        $data = $res->fetch_assoc();
        return $data['count'];
    }
   

    if (isset($_POST['form_sub']) && $_POST['form_sub'] == '1'){
        $name = $mysqli->real_escape_string($_POST['name']);
        $email = $mysqli->real_escape_string($_POST['email']);
        $phone = $mysqli->real_escape_string($_POST['phone']);
        $gender = isset($_POST['gender']) ? $mysqli->real_escape_string($_POST['gender']) : '';
        $password = $mysqli->real_escape_string($_POST['password']);
        $confirm_password = $mysqli->real_escape_string($_POST['confirm_password']);

        //name validation
        if (strlen($name) === 0){
            $error =TRUE;
            $name_error = 'Please enter your Name!';
        }else if(strlen($name) >100){
            $error =TRUE;
            $name_error = 'Name should not be more than 100 characters!';
        }else if (strlen($name) <4 ){
            $error =TRUE;
            $name_error = 'Name should be more than 3 characters!';
        }

        //Email validation
        if (strlen($email) === 0){
            $error =TRUE;
            $email_error = 'Please enter your Name!';
        }else if(strlen($email) >100){
            $error =TRUE;
            $email_error = 'Email should not be more than 100 characters!';
        }else if (strlen($email) <4 ){
            $error =TRUE;
            $email_error = 'Email should be more than 3 characters!';
        }else if (emailUnique($email, $mysqli)>0){
            $error =TRUE;
            $email_error = 'Email already exists!';
        }
        //Phone validation
        if (strlen($phone) === 0){
            $error =TRUE;
            $phone_error = 'Please enter your Phone Number!';
        }else if (strlen($phone) > 50){
            $error =TRUE;
            $phone_error = 'Phone Number should not be more than 50 characters!';
        }
        else if (strlen($phone) <=5){
            $error = TRUE;
            $phone_error = 'Phone Number should be more than 5 characters!';
        }
        //psssword
        if (strlen($password) === 0){
            $error =TRUE;
            $password_error = 'Please enter your Password!';
        }else if (strlen($password) < 8){
            $error =TRUE;
            $password_error = 'Password should be more than 8 characters!';
        }else if (strlen($password) >100){
            $error =TRUE;
            $password_error = 'Password should not be more than 100 characters!';
        }else{
            $byScript_password = md5($password);
        }

        if ($password !== $confirm_password){
            $error =TRUE;
            $confirm_password_error = 'Password and Confirm Password should be same!';
        }
        
        if (!$error){
            
            $sql = "INSERT INTO `users` 
                    (`name`, `email`,`password` ,`role`,`phone`,`gender`) VALUES 
                    ('$name','$email','$byScript_password','user','$phone','$gender')";

            $result = $mysqli->query($sql);
            if ($result){
                $url = $admin_base_url.'login.php?success=Register Success';
                header("Location: $url");
                exit();
            }
        }
    }

    
?>
<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Register Form</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/favicon.png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet">
    
</head>

<body class="h-100">
    
    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    



    <div class="login-form-bg h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100">
                <div class="col-xl-6">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-5">
                                
                                    <a class="text-center" href="index.html"> 
                                        <h1>Register Form</h1>
                                    </a>
        
                                <form class="mt-5 mb-5 login-input" method="POST">
                                    <div class="form-group">
                                        <input type="text" class="form-control"  placeholder="Name" name="name" />
                                        <?php if($error && $name_error):
                                        ?>
                                            <span class="text-danger"><?= $name_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" class="form-control"  placeholder="Email" name="email" />
                                        <?php if($error && $email_error):
                                        ?>
                                            <span class="text-danger"><?= $email_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control"  placeholder="Phone" name="phone" />
                                        <?php if($error && $phone_error):
                                        ?>
                                            <span class="text-danger"><?= $phone_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="gender" id="exampleRadios1" value="male">
                                            <label for="gender" class="form-check-label">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="gender" id="exampleRadios1" value="female">
                                            <label for="gender" class="form-check-label">Female</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control" placeholder="Password" name="password" />
                                        <?php if($error && $password_error):
                                        ?>
                                            <span class="text-danger"><?= $password_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password" />
                                        <?php if($error && $confirm_password_error):
                                        ?>
                                            <span class="text-danger"><?= $confirm_password_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <input type="hidden" name="form_sub" value="1" />
                                    <button class="btn login-form__btm_submit btn-primary w-100">Sign Up</button>
                                </form>
                                    <p class="mt-5 login-form__footer">Have account <a href="<?= $admin_base_url ?>login.php" class="text-primary">Sign in </a> now</p>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    

    <!--**********************************
        Scripts
    ***********************************-->
    <script src="plugins/common/common.min.js"></script>
    <script src="js/custom.min.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/gleek.js"></script>
    <script src="js/styleSwitcher.js"></script>
</body>
</html>





