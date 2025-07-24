<?php 
require '../require/db.php';
require '../require/common.php';
require '../require/common_function.php';

$res =selectData('products',$mysqli,"*","ORDER BY created_at DESC");
$delete_id = isset($_GET['delete_id'])? $_GET['delete_id'] : '';
$success = isset($_GET['success'])? $_GET['success']:'';

if ($delete_id !== ''){
    
    $res = deleteData('products',$mysqli,"id=$delete_id");
    if ($res){
        $url = $admin_base_url.'product_list.php?delete_success=Delete Product Successfully!';
        header("Location: $url");
        exit();
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
                    <h1>Product List</h1>
                    <div class="">
                        <a href="<?= $admin_base_url.'product_create.php' ?>" class="btn btn-primary">
                            Create Product 
                        </a>
                    </div>
                </div>
                <div class="row">
                    <?php if($success){?>
                    <div class="col-md-4 offset-md-8 col-sm-6 offset-sm-6">
                        <div class="alert alert-success">
                            <?= $success?>
                        </div>
                    </div>
                    <?php }?>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Category</th>
                                                <th>Discount</th>
                                                <th>Stock</th>
                                                <th>Sale Price</th>
                                                <th>Purchase Price</th>
                                                <th>Expire Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($res && $res->num_rows > 0):
                                                $i="1";
                                                while ($row = $res->fetch_assoc()): ?>
                                                <tr id="row-<?= $row['id'] ?>">
                                                    <td><?= $i ?></td>
                                                    <td><?= $row['name'] ?></td>
                                                    <td><?= $row['percentage'] ?>%</td>
                                                    <td><?= $row['start_date'] ?></td>
                                                    <td><?= $row['end_date'] ?></td>
                                                    <td><?= date ("Y-m-d g:i:s A", strtotime($row['updated_at'])) ?></td>
                                                    <td><?= date ("Y-m-d g:i:s A", strtotime($row['created_at'])) ?></td>
                                                    <td>
                                                        <a href="<?= $admin_base_url.'product_edit.php?edit_id='.$row['id'] ?>" class="btn btn-sm btn-primary">
                                                            Edit
                                                        </a>
                                                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>">
                                                            Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php $i++;} else: ?>
                                                <tr><td colspan="8" class="text-center">No products found.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
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
<!-- SweetAlert Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    $('.delete-btn').click(function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete the product.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'product_list.php?delete_id=' + id;
            }
        });
    });
});
</script>


    
<?php
require './layouts/footer.php';

?> 