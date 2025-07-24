<?php 
    
    require '../require/db.php';
    require '../require/common.php';
    require '../require/common_function.php';

    $error = false;
    $name = $name_error = $error_msg =
    $percentage = $percentage_error = 
    $start_date = $start_date_error =
    $end_date   = $end_date_error    = '';

    if (isset($_GET['edit_id']) && $_GET['edit_id'] !== '') {
        $id = $_GET['edit_id'];
        $res = selectData('products',$mysqli,"*","WHERE id=$id");
        if ($res->num_rows > 0) {
            $data = $res->fetch_assoc();
            $name = $data['name'];
            $percentage = $data['percentage'];
            $start_date = $data['start_date'];
            $end_date = $data['end_date'];
            
        }else{
            $error = true;
            $error_msg = 'Invalid request';
            $url = $admin_base_url.'product_list.php';
            header("Location: $url");
            exit();
        }
        
    }else{
        $error = true;
        $error_msg = 'Invalid request';
        $url = $admin_base_url.'product_list.php';
        header("Location: $url");
        exit();
    }
    function validate_product($name,$mysqli){
            $query = "SELECT count(id) as count FROM `products` WHERE name='$name'";
            $res = $mysqli->query($query);
            $data = $res->fetch_assoc();
            return $data['count'];
            
    }
    if (isset($_POST['form_sub']) && $_POST['form_sub'] == '1') {
        $name = $mysqli->real_escape_string($_POST['name']);
        $percentage = $mysqli->real_escape_string($_POST['percentage']);
        $start_date = $mysqli->real_escape_string($_POST['start_date']);
        $end_date = $mysqli->real_escape_string($_POST['end_date']);

        if (strlen($name) === 0) {
            $error = true;
            $name_error = 'Please enter product name!';
        }
        else if(strlen($name) > 100){
            $error = true;
            $name_error = 'Product name must be less than 100 characters!';
        }
        else if(strlen($name)<3){
            $error = true;
            $name_error = 'Product name must be more than 3 characters!';
        }
        // else if (validate_product($name,$mysqli) > 0) {
        //     $error = true;
        //     $name_error = 'Product name already exist!';
        // }

         if (strlen($percentage) === 0 || $percentage === '') {
            $error = true;
            $percentage_error = 'Please enter product percentage!';
        }
        else if($percentage > 100){
            $error = true;
            $percentage_error = 'Product percent must be under 100 percent!';
        }

        if (strlen($start_date) === 0 || $start_date === '') {
            $error = true;
            $start_date_error = 'Please choose Start Date for product!';
        }
        if ($end_date < $start_date) {
            $error = true;
            $end_date_error = 'End date must be greater than Start Date!';
        }
        
        if (!$error){
            $data = [
                    'name' => $name,
                    'percentage' => $percentage,
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ];
            $where = [
              'id' => $id  
            ];
            $updateData = updateData('products', $mysqli, $data, $where);
            
            if ($updateData) {
                $url = $admin_base_url.'product_list.php?success=Product Updated successfully';
                header("Location: $url");
                exit();
            }else{
                $error = true;
                $error_msg = 'Something went wrong!';
            }
        }
    }
require './layouts/header.php';
?>

 <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">

            
            <!-- row -->

            <div class="container-fluid">
                <div class="d-flex justify-content-between">
                    <h1>Product Update</h1>
                    <div class="">
                        <a href="<?= $admin_base_url.'product_list.php' ?>" class="btn btn-dark">
                            Back 
                        </a>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <div class="col-md-6 col-sm-10 col-12">
                        <?php if($error && $error_msg):
                        ?>
                            <span class="text-danger"><?= $error_msg ?></span>
                        <?php
                        endif;
                        ?>
                        <div class="card">
                            <div class="card-body">
                                <form action="<?=$admin_base_url.'product_edit.php?edit_id='.$id?>" method="POST">
                                    <div class="form-group">
                                        <label for="" class="form-label">Name</label>
                                        <input type="text" name="name" class="form-control" value="<?= $name ?>">
                                        <?php if($error && $name_error):
                                        ?>
                                            <span class="text-danger"><?= $name_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="percentage" class="form-label">Percentage</label>
                                        <input type="number" name="percentage" id="percentage" class="form-control" placeholder="Please Enter Percentage" value="<?= $percentage ?>">
                                        <?php if($error && $percentage_error):
                                        ?>
                                            <span class="text-danger"><?= $percentage_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="text" name="start_date" id="start_date" class="form-control" placeholder="Please Enter Start Date" value="<?= $start_date ?>">
                                        <!-- <input type="text" name="start_date" id="start_date" class="form-control" value="<?= $start_date ?>"> -->
                                        <?php if($error && $start_date_error):
                                        ?>
                                            <span class="text-danger"><?= $start_date_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="text" name="end_date" id="end_date" class="form-control" placeholder="Please Enter End Date" value="<?= $end_date ?>">
                                        <!-- <input type="text" name="end_date" id="end_date" class="form-control" value="<?= $end_date ?>"> -->
                                        <?php if($error && $end_date_error):
                                        ?>
                                            <span class="text-danger"><?= $end_date_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <input type="hidden" name="form_sub" value="1" />
                                    <button type="submit" class="btn btn-primary w-100">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #/ container -->
        </div>
        <!--**********************************
            Content body end
        ***********************************-->

<?php
require './layouts/footer.php';

?> 
<script>
    $('#start_date').bootstrapMaterialDatePicker({
        weekStart: 0,
        time: false,
        minDate : moment()
    }).on('change',function(e,date){
        $('#end_date').bootstrapMaterialDatePicker('setMinDate',date);
    });
    $('#end_date').bootstrapMaterialDatePicker({
        weekStart: 0,
        time: false
    });
</script>