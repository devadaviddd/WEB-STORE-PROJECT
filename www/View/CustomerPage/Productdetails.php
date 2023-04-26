<?php
include($_SERVER['DOCUMENT_ROOT'] . '/View//UserPage/session.php');
$product = array(
    "productId" => $_GET['productId'],
    "productName" => $_GET['productName'],
    "description" => $_GET['description'],
    "imageURL" => $_GET['imageURL'],
    "price" => (float) $_GET['price'],
    "vendorUsername" => $_GET['vendorUsername']
);
$currentCustomer = unserialize($_SESSION['login_customer']);
$system = System::getInstance();
$input = "";

$productList = $system->getProductList();
if (isset($_GET['keyword'])) {
    $productList =  $system->searchProductByName($_GET['keyword'], $productList);
    $input = $_GET['keyword'];
}

?>

<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inc/Navbar.css">
    <link rel="stylesheet" href="/inc/Productdetails.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
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
    <main>

        <div class="row d-flex flex-column control-box">
            <div class="Infobar">
                <p>PRODUCT DETAILS</p>
            </div>
            <form action="Allproductpage.php" method="GET" class="d-flex  justify-content-center mt-2">
                <input class="form-control rounded" id="inputElement" type="text" placeholder="Search Anything" name="keyword" value=<?php echo $input ?>>
                <button id="searchBtn" type="submit" name="search" value="searchBtn" class="rounded "><img src="../../image/icon/search.png" alt="" width="35px" height="35px"></button>
            </form>

        </div>

        <div class="container ">
            <div class="card">
                <div class="card-body ">

                    <div class="wrapper row">
                        <div class="product-image col-md-6">
                            <div class="image-display">
                                <div class="tab-pane active" id="pic"><img src=<?php echo $_GET['imageURL'] ?> width="400px" height="400px"></div>
                            </div>

                            <ul class="thumbnail nav nav-tabs">
                                <li><img src=<?php echo $_GET['imageURL'] ?>></li>
                                <li><img src=<?php echo $_GET['imageURL'] ?>></li>
                                <li><img src=<?php echo $_GET['imageURL'] ?>></li>
                                <li><img src=<?php echo $_GET['imageURL'] ?>></li>
                                <li><img src=<?php echo $_GET['imageURL'] ?>></li>
                            </ul>

                        </div>
                        <div class="details col-md-6">
                            <h3 class="product-name mb-3"><?php echo $_GET['productName'] ?></h3>
                            <div class="rating ">
                                <div class="stars  mb-3">
                                    <span><img src="../../image/icon/star.png" alt=""></span>
                                    <span><img src="../../image/icon/star.png" alt=""></span>
                                    <span><img src="../../image/icon/star.png" alt=""></span>
                                    <span><img src="../../image/icon/star.png" alt=""></span>
                                    <span><img src="../../image/icon/star.png" alt=""></span>
                                </div>
                                <span class="review-no  mb-3">32 reviews</span>
                            </div>
                            <p class="product-description  mb-3"><?php echo $_GET['description'] ?></p>
                            <h4 class="price  mb-3">current price: <span><?php echo $_GET['price'] ?> $</span></h4>

                            <p class="vote  mb-3"><strong>100%</strong> of buyers enjoy this product! <strong>(32 votes)</strong></p>

                            <h5 class="owner  mb-3">Sell by: <b><?php echo $_GET['vendorUsername'] ?> </b></h5>

                            <div class="action">
                                <button class="add-to-cart btn btn-primary" type="button" id="cartBtn" onclick="successAdd()">ADD TO CART</button>
                                <a class="order-now btn btn-success" role="button" id="orderBtn" href="Shoppingcart.php">ORDER NOW</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="announceToast" class="toast align-items-center text-white bg-success border-0 aria-live=" assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="announce">
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
        <script src="Productdetails.js"></script>

    </main>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/View/ShareComponent/footer.php'); ?>
</body>
<script>
    cartBtn = document.querySelector('#cartBtn');
    orderBtn = document.querySelector('#orderBtn');
    const product = {
        productName: `<?php echo $_GET['productName'] ?>`,
        description: `<?php echo $_GET['description'] ?>`,
        imageURL: `<?php echo $_GET['imageURL'] ?>`,
        price: `<?php echo $_GET['price'] ?>`,
        vendorUsername: `<?php echo $_GET['vendorUsername'] ?>`,
        quantity: localStorage.getItem(`<?php echo $_GET['productId'] ?>`) === null ? 0 : Number(JSON.parse(localStorage.getItem(`<?php echo $_GET['productId'] ?>`))['quantity']),
    }
    console.log(product);

    cartBtn.addEventListener('click', () => {
        product['quantity']++;
        localStorage.setItem(`<?php echo $_GET['productId'] ?>`, JSON.stringify(product));
    })

    orderBtn.addEventListener('click', () => {
        product['quantity']++;
        localStorage.setItem(`<?php echo $_GET['productId'] ?>`, JSON.stringify(product));
    })
</script>

</html>