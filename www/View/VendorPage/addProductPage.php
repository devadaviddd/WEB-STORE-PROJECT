<?php
include($_SERVER['DOCUMENT_ROOT'] . '/View/UserPage/session.php');
$login_vendor = unserialize($_SESSION['login_vendor']);
$system = System::getInstance();
$errorMessage  = "";
$messagetype = "";
$messageon = "";
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
            $insertStatus = $system->addNewProduct(
                $login_vendor['username'],
                $_POST['product_name'],
                $_POST['product_price'],
                $_POST['product_description'],
                '../../' . $target_img_location
            );

            if ($insertStatus == null) {
                throw new Error();
            }
            move_uploaded_file($fileTempName, '../../' . $target_img_location);
            $messagetype = "bg-success ";
            $messageon = "show";
            $errorMessage = "Server add product success.";
        }
    } catch (ErrorException | Error $e) {
        $messagetype = "bg-danger ";
        $messageon = "show";
        $errorMessage = "Server can't add product.";
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
    <link href="/inc/vendor_product.css" rel="stylesheet" />
    <title>Vendor Add Product Page</title>
</head>

<body class="px-5">
    <div class="container row-ml-1">
        <p class="d-block col-md h2 " id="sub_title"><?php echo $login_vendor['busName'];  ?></p>
        <div class="col-md d-flex flex-row-reverse">
            <a href="/View/VendorPage/VendorPage.php" role="button" class="btn btn-outline-primary">Back to View Product</a>
        </div>
    </div>
    <div id="v_input_form" class="container d-flex justify-content-center">
        <form method="post" id="new_product_form" enctype='multipart/form-data'>
            <label class="label" for="product_name">Product name:</label>
            <input class="form-control" type='text' placeholder="Enter product name" name='product_name' pattern="[A-Za-z0-9 ]{10,20}" required /> <br>
            <label class="label" for="product_price">Price:</label>
            <input class="form-control" type='numner' name='product_price' placeholder='Enter price in $' pattern="^[0-9]*[\.]?[0-9]{0,2}$" required /> <br>
            <label class="label" for="product_description">Product Description:</label>
            <input class="form-control" type="text" name="product_description" placeholder="Enter most 500 words" pattern="[A-Za-z0-9 .]{1,500}" required /> <br>
            <input class="form-control" type='file' name="product_image_file" id="product_image_file" required> <br>
            <input class="btn btn-outline-primary mt-3" type="submit" name="submit" value="Add New Product" />
            <input class="btn btn-outline-dark ml-1 mt-3" type=' reset' value='Clear Form' />
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    <script src="./addProductPage.js"></script>
    <div class="position-fixed bottom-0 end-0 p" style="z-index: 11">
        <div id="announceToast" class="toast align-items-center text-white  border-0 <?php echo $messagetype;
                                                                                        echo $messageon; ?>" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="serverResponds">
                    <?php echo $errorMessage; ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>

        </div>
</body>

</html>