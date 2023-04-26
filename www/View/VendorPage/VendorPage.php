<?php
define('SITE_ROOT', realpath(dirname(__FILE__)));
include($_SERVER['DOCUMENT_ROOT'] . '/View/UserPage/session.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/System.php');
$login_vendor = unserialize($_SESSION['login_vendor']);
$vendorProductList = array();
$system = System::getInstance();
$error = "";

function getVendorProductList()
{
    $tempList = $GLOBALS['system']->getProductList();
    foreach ($tempList as $key => $product) {
        if ($product->vendorUsername == $GLOBALS['login_vendor']['username']) {
            array_push($GLOBALS['vendorProductList'], $product);
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    try {
        getVendorProductList();
    } catch (Error | ErrorException $e) {
    }
}

?>

<?php

function loadProductList()
{
    foreach ($GLOBALS['vendorProductList'] as $product) {
?>
        <div class="col">
            <div class="card">
                <img src="<?php echo $product->imageURL; ?>" class="card-img-top" alt="product image">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $product->productId; ?></h5>
                    <h5 class="card-title"><?php echo $product->productName; ?></h5>
                    <p class="card-text"><?php echo $product->description; ?></p>
                </div>
                <div class="card-footer d-flex flex-row-reverse">
                    <h5>$<?php echo $product->price; ?></h5>
                </div>
            </div>
        </div>
<?php
    }
}
?>



<!DOCTYPE html>
<html lang=" en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link href="/inc/vendor_product.css" rel="stylesheet" />
    <title>Vendor View Page</title>
</head>



<body>
    <header>
        <ul>
            <li><a href="#"><img src="../../image/icon/store.png" alt="" width="25px" height="25px"> DAZALA STORE</a></li>
            <li><a href="../UserPage/ProfilePage.php"><?php echo $login_vendor['username']; ?></a> </li>
            <li><a href="/View/UserPage/Logout.php">LOG OUT</a></li>
        </ul>
    </header>

    <p class="d-block col-md h2 " id="sub_title"><?php echo $login_vendor['busName'];  ?></p>

    <div class="container row-ml-1">
        <div class="col-md d-flex flex-row-reverse">
            <a href="/View/VendorPage/addProductPage.php" role="button" class="btn btn-outline-primary">Add New Product</a>
        </div>
    </div>
    <div class="container mt-5">
        <div id='announce'>
            <?php
            if (count($vendorProductList) == 0) {
                echo '<p>' . 'Your product list is empty' . '</p>';
            }
            ?>
        </div>
        <div id="v_product_container" class="row row-cols-sm-1 row-cols-md-2 row-cols-lg-3 g-3">
            <?php
            loadProductList();
            ?>
        </div>


        <?php ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/View/ShareComponent/footer.php'); ?>
</body>

</html>