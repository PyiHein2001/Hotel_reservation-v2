<?php 
    
    require '../require/db.php';
    require '../require/common.php';
    require '../require/common_function.php';

    $category_res = selectData('categories',$mysqli);
    $discount_res = selectData('discounts',$mysqli);

    $error          = false;
    $error_msg       =
    $name           = $name_error           = 
    $category       = $category_list_error  = 
    $discount       = $discount_list_error  = 
    $stock_count    = $stock_count_error    = 
    $sale_price     = $sale_price_error     = 
    $purchase_price = $purchase_price_error = 
    $description    = $description_error    = 
    $expire_date    = $expire_date_error    = '';


    function validate_product($name,$mysqli){
            $query = "SELECT count(id) as count FROM `products` WHERE name='$name'";
            $res = $mysqli->query($query);
            $data = $res->fetch_assoc();
            return $data['count'];
            
    }

    
    if (isset($_POST['form_sub']) && $_POST['form_sub'] == '1') {
        $name = $mysqli->real_escape_string($_POST['name']);
        $category = $mysqli->real_escape_string($_POST['category']);
        $discount = $mysqli->real_escape_string($_POST['discount']);
        $stock_count = $mysqli->real_escape_string($_POST['stock_count']);
        $sale_price = $mysqli->real_escape_string($_POST['sale_price']);
        $purchase_price = $mysqli->real_escape_string($_POST['purchase_price']);
        $description = $mysqli->real_escape_string($_POST['description']);
        $expire_date = $mysqli->real_escape_string($_POST['expire_date']);
        
        // name
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
        else if (validate_product($name,$mysqli) > 0) {
            $error = true;
            $name_error = 'Product name already exist!';
        }

        // description
        if (strlen($description) === 0) {
            $error = true;
            $description_error = 'Please enter product name!';
        }
        else if(strlen($description) > 3000){
            $error = true;
            $description_error = 'Product Description must be less than 3000 characters!';
        }
        
        // category
        if (strlen($category) === 0 || $percentage === '') {
            $error = true;
            $category_list_error = 'Please Choose Product Category!';
        }
        // stock count
        if (strlen($stock_count) === 0 || $stock_count === '') {
            $error = true;
            $stock_count_error = 'Please Choose Product Category!';
        }else if (is_numeric($stock_count) === false) {
            $error = true;
            $stock_count_error = 'Stock count must be a number!';
        }else if ($stock_count < 0) {
            $error = true;
            $stock_count_error = 'Stock count must be a positive number!';
        }else if ($stock_count > 100000) {
            $error =true;
            $stock_count_error = 'Stock count must be less than 100000!';
        }
        // sale price
        if (strlen($sale_price) === 0 || $sale_price === '') {
            $error = true;
            $sale_price_error = 'Please Choose Product Category!';
        }else if (is_numeric($sale_price) === false) {
            $error = true;
            $sale_price_error = 'Sale Price must be a number!';
        }else if ($sale_price < 0) {
            $error = true;
            $sale_price_error = 'Sale Price must be a positive number!';
        }
        // pruchase price
        if (strlen($purchase_price) === 0 || $purchase_price === '') {
            $error = true;
            $purchase_price_error = 'Please Choose Product Category!';
        }else if (is_numeric($purchase_price) === false) {
            $error = true;
            $purchase_price_error = 'Purchase Price must be a number!';
        }else if ($purchase_price < 0) {
            $error = true;
            $purchase_price_error = 'Purchase Price must be a positive number!';
        }
        // // expire date
        // if (strlen($expire_date) !== '' && $expire_date < date('Y-m-d')) {
        //     $error = true;
        //     $start_date_error = 'Please choose Start Date for product!';
        // }
        
        if (!$error){
                $data = [
                    'name'              => $name,
                    'category_id'       => $category,
                    'discount_id'       => $discount,
                    'stock_count'       => $stock_count,
                    'sale_price'        => $sale_price,
                    'purchase_price'    => $purchase_price,
                    'description'       => $description,
                    'expire_date'       => $expire_date
                ];
              
                $result =insertData('products',$mysqli, $data);
                
                if ($result) {
                    $url = $admin_base_url.'product_image.php?id='.$mysqli->insert_id;
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
                    <h1>Product Create</h1>
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
                                <form action="<?=$admin_base_url.'product_create.php'?>" method="POST">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Please Enter Name" value="<?= $name ?>">
                                        <?php if($error && $name_error):
                                        ?>
                                            <span class="text-danger"><?= $name_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" class="form-control" placeholder="Please Enter Description" value="<?= $description?>"></textarea>
                                        <?php if($error && $description_error):
                                        ?>
                                            <span class="text-danger"><?= $description_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="category" class="form-label">Category</label>
                                        
                                        <select name="category" id="category" class="form-control">
                                            <option value="">Please choose category</option>
                                            <?php if ($category_res-> num_rows >0 ){
                                                while($category_list = $category_res->fetch_assoc()){?>
                                            <option value="<?= $category_list['id']?>"><?= $category_list['name']?></option>
                                            <?php }}?>
                                        </select>
                                        <?php if($error && $category_list_error):
                                        ?>
                                            <span class="text-danger"><?= $category_list_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="discount" class="form-label">Discount</label>
                                        
                                        <select name="discount" id="discount" class="form-control">
                                            <option value="">Please choose Discount</option>
                                            <?php if ($discount_res-> num_rows >0 ){
                                                while($discount_list = $discount_res->fetch_assoc()){?>
                                            <option value="<?= $discount_list['id']?>"><?= $discount_list['name'].' ('.$discount_list['percentage'].'%)'?></option>
                                            <?php }}?>
                                        </select>
                                        <?php if($error && $discount_list_error):
                                        ?>
                                            <span class="text-danger"><?= $discount_list_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="stock_count" class="form-label">Stock Count</label>
                                        <input type="number" name="stock_count" id="stock_count" class="form-control" placeholder="Please Enter stock_count" value="<?= $stock_count ?>">
                                        <?php if($error && $stock_count_error):
                                        ?>
                                            <span class="text-danger"><?= $stock_count_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="sale_price" class="form-label">Sale Price</label>
                                        <input type="number" name="sale_price" id="sale_price" class="form-control" placeholder="Please Enter Sale Price" value="<?= $sale_price ?>">
                                        <?php if($error && $sale_price_error):
                                        ?>
                                            <span class="text-danger"><?= $sale_price_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="purchase_price" class="form-label">Purchase Price</label>
                                        <input type="number" name="purchase_price" id="purchase_price" class="form-control" placeholder="Please Enter Purchase Price" value="<?= $purchase_price ?>">
                                        <?php if($error && $purchase_price_error):
                                        ?>
                                            <span class="text-danger"><?= $purchase_price_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="expire_date" class="form-label">Expire Date</label>
                                        <input type="text" name="expire_date" id="expire_date" class="form-control" placeholder="Please Enter Expire Date" value="<?= $expire_date ?>">
                                        <?php if($error && $expire_date_error):
                                        ?>
                                            <span class="text-danger"><?= $expire_date_error ?></span>
                                        
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                    
                                    <input type="hidden" name="form_sub" value="1" />
                                    <button type="submit" class="btn btn-primary w-100">Create</button>
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
    $('#expire_date').bootstrapMaterialDatePicker({
        weekStart: 0,
        time: false,
        minDate : moment()
    });
   
</script>