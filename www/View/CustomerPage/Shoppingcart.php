<?php
include($_SERVER['DOCUMENT_ROOT'] . '/View/UserPage/session.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/System.php');

$currentCustomer = unserialize($_SESSION['login_customer']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $productList = array();
        $array = $_POST;
        foreach ($array as $key => $item) {
            $quantity = (int)$item;
            for ($i = 0; $i < $quantity; $i++) {
                array_push($productList, $key);
            }
        }
        $system = System::getInstance();
        $check = $system->addNewOrder($currentCustomer['username'], $productList);
        if ($check == true) {
            header("Status: 200 Success");
        }
    } catch (Error | Exception $error) {
        header("Status: 400 error");
    }
}

?>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inc/Navbar.css">
    <link rel="stylesheet" href="/inc/ShoppingCart.css">
    <link rel="stylesheet" href="/inc/footer.css">

    <title>ViewProduct</title>

</head>

<body>
    <header>
        <ul>
            <li><a href="Allproductpage.php"><img src="../../image/icon/store.png" alt="" width="25px" height="25px"> DAZALA STORE</a></li>
            <li><a href="../UserPage/ProfilePage.php"><?php echo $currentCustomer['username']; ?></a> </li>
            <li><a href="Shoppingcart.php">VIEW CART</a></li>
        </ul>
    </header>

    <div class="Infobar">
        <p>MY SHOPPING CART</p>
    </div>
    <main>
        <div class="container">
            <div class="productsCard">
                <div class="noContent" id="noContent">
                    <p id="announce">Your Cart is Empty!</p>
                </div>
                <div class="item new-box">
                    <p id="productId"></p>
                    <div class="buttons">
                        <span class="delete-btn"></span>
                    </div>

                    <div class="image">
                        <img src="../../image/P001.png" alt="img" />
                    </div>

                    <div class="infor">
                        <p><b></b></p>
                        <span></span>

                    </div>

                    <div class="quantity">
                        <p><b>quanlity: </b></p>
                        <input type="text" name="name" value="1" readonly>
                    </div>

                    <div class="total-price">$549</div>
                </div>

            </div>
            <div class="checkoutCard">
                <div class="content">
                    <p>Address</p>
                    <i><?php echo $currentCustomer['address']; ?></i>
                    <hr>
                    <p>Receiver Name</p>
                    <i><?php echo $currentCustomer['name']; ?></i>
                    <hr>
                    <div class="price">
                        <p>Shipping Free</p>
                        <h3>Free</h3>
                    </div>
                    <hr>
                    <div class="price">
                        <p>Sale Tax</p>
                        <h3>Free</h3>
                    </div>

                    <hr>
                    <div class="price">
                        <p><b>Total</b>
                        <p>
                        <h2 id="totalPrice">$0</h2>
                    </div>

                    <div class="box">
                        <select>
                            <option>Payment method:</option>
                            <option>Cash</option>
                        </select>
                    </div>

                    <div class="control">
                        <form action="" method="post" id="cart_items_form">
                            <input type="button" onclick="pay()" value="Pay Now">
                        </form>

                        <button onclick="window.location.href='Allproductpage.php'">
                            Continue Shopping
                        </button>
                    </div>

                </div>
            </div>
        </div>
        </div>
    </main>

    <script src="Shoppingcart.js"></script>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/View/ShareComponent/footer.php'); ?>
</body>

</html>