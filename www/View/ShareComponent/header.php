<?php
$input = "";
if (isset($_SESSION['user_login'])) {
    $username = unserialize($_SESSION['user_login']);
}
?>
<header>
    <nav class="cust_nav">
        <a id="logo" href="Allproductpage.php">
            <img src="/image/lazadalogo.png" alt="logo" width="100" height="50">
        </a>
        <div class="right_options">
            <a class="icon" href="Shoppingcart.php">
                <img src="/image/atclogo.png" alt="myaccountlogo" width="65" height="50">
                <a class="icon" href="">
                    <img src="/image/myaccountlogo.png" alt="myaccountlogo" width="50" height="50">
                </a>
            </a>
        </div>
    </nav>
</header>