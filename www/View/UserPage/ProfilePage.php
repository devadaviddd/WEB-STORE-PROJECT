<?php
include($_SERVER['DOCUMENT_ROOT'] . '/View/UserPage/session.php');
$system =  System::getInstance();
$userList = $system->getAccountList();
$user;
$pageTitle = "";
if (isset($_SESSION['login_customer']) && isset($userList[unserialize($_SESSION['login_customer'])['username']])) {
    $temp = $userList[unserialize($_SESSION['login_customer'])['username']];
    $user = array(
        'username' =>  $temp->username,
        'name' => $temp->name,
        'address' => $temp->deliveryAddress,
        "picURL" => $temp->getImageURL()
    );
    $pageTitle = "Customer Profile Page";
} else if (isset($_SESSION['login_vendor']) && isset($userList[unserialize($_SESSION['login_vendor'])['username']])) {
    $temp = $userList[unserialize($_SESSION['login_vendor'])['username']];
    $user = array(
        'username' =>  $temp->username,
        'busName' => $temp->businessName,
        'busAddress' => $temp->businessAddress,
        "picURL" => $temp->getImageURL()
    );
    $pageTitle = "Vendor Profile Page";
} else if (isset($_SESSION['login_shipper'])  && isset($userList[unserialize($_SESSION['login_shipper'])['username']])) {
    $temp = $userList[unserialize($_SESSION['login_shipper'])['username']];
    $user = array(
        'username' =>  $temp->username,
        'hubId' => $temp->hubId->hubId,
        'hubName' => $temp->hubId->hubName,
        "picURL" => $temp->getImageURL()
    );
    $pageTitle = "Shipper Profile Page";
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $target_img_location = 'image/';
        $fileName = $_FILES['product_image_file']['name'];
        $fileTempName = $_FILES['product_image_file']['tmp_name'];
        $fileError = $_FILES['product_image_file']['error'];
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt)); // change the name of the file to the uniq id in the system
        $target_img_location .= uniqid('') . "." . $fileActualExt;
        if ($fileError === 0) {

            if (isset($userList[$user['username']])) {
                $userList[$user['username']]->updatePictureURL('../../' . $target_img_location);
                $userBroker = UserBroker::getInstance();
                if ($userBroker->updateUserDB($userList) == true) {
                    move_uploaded_file($fileTempName, '../../' . $target_img_location);
                    $user['picURL'] = '../../' . $target_img_location;
                } else {
                    throw new Error("loading file error");
                }
            } else {
                throw new Error("user not found");
            }
            $error = "Image Is Changed  Successfully.";
        }
    } catch (ErrorException | Error $e) {
        $error = "Error! System Can't Change Image.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link href="/inc/profile.css" rel="stylesheet" />
    <title>My Profile</title>
</head>

<body>
    <header>
        <ul>
            <li><a href="../CustomerPage/Allproductpage.php"><img src="../../image/icon/store.png" alt="" width="25px" height="25px"> DAZALA STORE</a></li>
            <li><a href="../UserPage/ProfilePage.php"><?php echo $user['username'] ?></a> </li>
            <li><a href="/View/UserPage/Logout.php">LOG OUT</a></li>
        </ul>
    </header>

    <main>

        <div class="container">
            <div class="row mt-3 ">
                <p class="h1"><?php echo $pageTitle; ?></p>
            </div>
            <div class="row mt-4 d-flex justify-content-center">
                <div class="col-sm-2 col-md-3 ">
                    <image src="<?php echo $user['picURL']; ?>" class="img-thumbnail rounded" alt="User Profile Image">
                </div>
                <div class="col-sm-2  col-md-3 justify-content-center">
                    <p class="h2"><?php echo $user['username'] ?></p>
                </div>
            </div>
            <div class="row">
                <form method="post" enctype='multipart/form-data'>
                    <input type='file' name="product_image_file" id="product_image_file" required>
                    <button type="submit" class="mt-2 btn btn-outline-primary">Upload Image</button>
                </form>
            </div>
            <?php
            if (isset($user['address'])) {
            ?>
                <div class="row mt-4">
                    <label for="name" class="col-sm-2 labels">Name</label>
                    <div class="col-md-2"><input id="name" type="text" class="form-control" value="<?php echo $user['name']; ?>" disabled></div>
                </div>
                <div class="row mt-2">
                    <label for="d-address" class="col-sm-2 labels">Deliveried Address</label>
                    <div class="col-sm-4"><input id="d-address" type="text" class="form-control" value="<?php echo $user['address'] ?>" disabled></div>
                </div>
            <?php
            } else if (isset($user['busName']) || isset($user['busAddress'])) {
            ?>
                <div class="row mt-4">
                    <label for="b-name" class="col-sm-2 labels">Bussiness Name</label>
                    <div class="col-sm-5 col-md-4"><input id="b-name" type="text" class="form-control" value="<?php echo $user['busName'] ?>" disabled></div>
                </div>
                <div class="row mt-2">
                    <label for="b-address" class="col-sm-2 labels">Bussiness Address</label>
                    <div class="col-sm-5 col-lg-4"><input id="b-address" type="text" class="form-control" value="<?php echo $user['busAddress'] ?>" disabled> </div>
                </div>
            <?php
            } else if (isset($user['hubId']) || isset($user['hubName'])) {

            ?>
                <div class="row mt-4">
                    <label for="hub-id" class="col-sm-2 labels">Hub Id</label>
                    <div class="col-md-2">
                        <select class="form-select" id="hub-id" disabled>
                            <option selected><?php echo $user['hubId']; ?></option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <label for="hub-name" class=" col-sm-2 labels">Hub Name</label>
                    <div class="col-md-2"><input id="b-address" type="text" class="form-control" value="<?php echo $user['hubName'] ?>" disabled> </div>
                </div>
            <?php
            }
            ?>

        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/View/ShareComponent/footer.php'); ?>
</body>

</html>