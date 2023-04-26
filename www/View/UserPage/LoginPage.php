<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/System.php');
session_start();
$error = "";



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $system = System::getInstance();
    $credential = $system->login($_POST['username'], $_POST['password']);
    if ($credential == null) {
        $error = "username or password is not correct";
    }

    if ($credential != null && get_class($credential) == 'Customer') {
        $userData = serialize(array(
            "username" => $credential->username,
            "name" => $credential->name,
            "picURL" => $credential->getImageURL(),
            "address" => $credential->deliveryAddress
        ));
        $_SESSION['login_customer'] = $userData;
        header("location:/View/CustomerPage/AllProductPage.php");
    } else if ($credential != null && get_class($credential) == 'Vendor') {
        // $productList = serialize($credential->getProductList());
        $userData = serialize(array(
            "username" => $credential->username,
            "picURL" => $credential->getImageURL(),
            "busName" => $credential->businessName,
            "busAddress" => $credential->businessAddress,
            // "productList" => $productList
        ));
        $_SESSION['login_vendor'] = $userData;
        header("location:/View/VendorPage/VendorPage.php");
    } else if ($credential != null && get_class($credential) == 'Shipper') {
        $userData = serialize(array(
            "username" => $credential->username,
            "picURL" => $credential->getImageURL(),
            "hubId" => $credential->hubId->hubId,
            "hubName" => $credential->hubId->hubName
        ));
        $_SESSION['login_shipper'] = $userData;
        header("location:/View/ShipperPage/ShipperPage.php");
    }
}



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lazada Login</title>
    <link rel="stylesheet" href="/inc/login.css">
</head>

<header>
</header>

<body>

    <div class="container">
        <h1>Please Login In</h1>
        <form action="" method="post">
            <div class="form-control">
                <input type="text" name="username" readonly onfocus="this.removeAttribute('readonly');" required>
                <label>Username</label>
            </div>

            <div class="form-control">
                <input type="password" name="password" readonly onfocus="this.removeAttribute('readonly');" required>
                <label>Password</label>
            </div>

            <button type="submit" class="btn">Login</button>
            <p class="text">Don't have an account? <a href="../RegisterPage/RegisterPage.php">Register</a></p>

        </form>
        <div class="error-message"><?php echo $error; ?></div>
    </div>

    <div id="snackbar">Some text some message..</div>

    <script src="LoginPage.js"></script>
</body>

<footer></footer>
</html>


<?php
if (isset($_SESSION['newCustomer'])) {
?>
    <script>
        var notification = document.getElementById("snackbar");
        notification.innerHTML = "Successfully Create Customer Account!!";
        notification.className = "show";
        setTimeout(function() {
            notification.className = notification.className.replace("show", "");
        }, 3000);
    </script>
<?php
    unset($_SESSION['newCustomer']);
} else if (isset($_SESSION['newVendor'])) {
?>
    <script>
        var notification = document.getElementById("snackbar");
        notification.innerHTML = "Successfully Create Vendor Account!!";
        notification.className = "show";
        setTimeout(function() {
            notification.className = notification.className.replace("show", "");
        }, 3000);
    </script>
<?php
    unset($_SESSION['newVendor']);
} else if (isset($_SESSION['newShipper'])) {
?>
    <script>
        var notification = document.getElementById("snackbar");
        notification.innerHTML = "Successfully Create Shipper Account!!";
        notification.className = "show";
        setTimeout(function() {
            notification.className = notification.className.replace("show", "");
        }, 3000);
    </script>
<?php
    unset($_SESSION['newShipper']);
}

?>