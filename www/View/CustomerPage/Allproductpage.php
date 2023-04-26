<?php
include($_SERVER['DOCUMENT_ROOT'] . '/View//UserPage/session.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/System.php');
define('newline', "<br><br>");
$system = System::getInstance();
$input = "";
$currentCustomer = unserialize($_SESSION['login_customer']);

$productList = $system->getProductList();
if (isset($_GET['keyword'])) {
    $productList =  $system->searchProductByName($_GET['keyword'], $productList);
    $input = $_GET['keyword'];
}


function loadProducts($key, $productList)
{
?>
    <!-- <div class="viewproducts">
        <div class="product_container"> -->
    <div></div>
    <div class="card rounded" style="margin: 1rem !important;">
        <a href="Productdetails.php?productId=<?php echo $key ?>&productName=<?php echo $productList[$key]->productName ?>&description=<?php echo $productList[$key]->description ?>&imageURL=<?php echo $productList[$key]->imageURL ?>&price=<?php echo $productList[$key]->price ?>&vendorUsername=<?php echo $productList[$key]->vendorUsername ?>">
            <img class="responsive" src="<?php echo $productList[$key]->imageURL ?>" width="100%" height="400px">
        </a>
        <div class="card-body bg-light  d-flex flex-column align-item-center justify-item-center text-center">
            <h4><?php echo $productList[$key]->productName ?></h4>


            <div class="d-flex  justify-content-center">
                <h4>$<?php echo $productList[$key]->price ?></h4>
                <span class="strike-text ms-2">$99.99</span>
            </div>
            <h6 class="text-success">Free shipping</h6>

            <a class="btn btn-outline-primary btn-sm p-3" href="Productdetails.php?productId=<?php echo $key ?>&productName=<?php echo $productList[$key]->productName ?>&description=<?php echo $productList[$key]->description ?>&imageURL=<?php echo $productList[$key]->imageURL ?>&price=<?php echo $productList[$key]->price ?>&vendorUsername=<?php echo $productList[$key]->vendorUsername ?>" data-abc="true"> View Products</a>
        </div>
    </div>
    <!-- </div>
    </div> -->
<?php
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <link rel="stylesheet" href="/inc/Navbar.css">
    <link rel="stylesheet" href="/inc/Allproductpage.css">
    <title>ViewProduct</title>
</head>

<body>

    <header>
        <ul>
            <li><a href=""><img src="../../image/icon/store.png" alt="" width="25px" height="25px"> DAZALA STORE</a></li>
            <li><a href="../UserPage/ProfilePage.php"><?php echo $currentCustomer['username']; ?></a> </li>
            <li><a href="Shoppingcart.php">VIEW CART</a></li>
        </ul>
    </header>


    <!-- <?php include($_SERVER['DOCUMENT_ROOT'] . '/View/ShareComponent/header.php') ?> -->
    <div class="Infobar">
        <p>ALL PRODUCTS</p>
    </div>

    <div class="row">
        <div class="col-4">
            <form name="form" action="#" method="post" id="priceform">
                <input class="pricebox" type="number" placeholder="Minimum Price" name="minprice">
                <input class="pricebox" type="number" placeholder="Maximum Price" name="maxprice">
                <input type="submit" class="btn btn-dark" value="sort">
            </form>
        </div>

        <div class="col-8">
            <form action="Allproductpage.php" method="GET" class="d-flex fullWidth justify-content-end">
                <input class="form-control rounded" id="inputElement" type="text" placeholder="Search Anything" name="keyword" value=<?php echo $input ?>>
                <button id="searchBtn" type="submit" name="search" value="searchBtn" class="rounded "><img src="../../image/icon/search.png" alt="" width="35px" height="35px"></button>
            </form>
        </div>
    </div>

    <main class="align-item-center">
        <?php

        if (count($productList) != 0) {
            foreach (array_keys($productList) as $key) {
                if ((!isset($_POST['minprice']) && !isset($_POST['maxprice']))) {
                    loadProducts($key, $productList);
                } else {

                    if ($productList[$key]->price >= (float)($_POST['minprice'])  && $productList[$key]->price <= (float)($_POST['maxprice'])) {
                        loadProducts($key, $productList);
                    } else if ($productList[$key]->price <= (float)($_POST['maxprice']) && ($_POST['minprice']) === "") {
                        loadProducts($key, $productList);
                    } else if ($productList[$key]->price >= (float)($_POST['minprice']) && ($_POST['maxprice']) === "") {
                        loadProducts($key, $productList);
                    }
                }
            }
        } else {
            print("There is no products at this time");
        }
        ?>
    </main>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/View/ShareComponent/footer.php'); ?>
</body>
<script src="AllProductPage.js"></script>

</html>