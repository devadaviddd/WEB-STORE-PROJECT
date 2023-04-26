<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/System.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Customer.php');
session_start();

$system = System::getInstance();

$accountList = array($system->getAccountList()); // this is username database
$namesList = array_keys($accountList[0]);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $image_file = $system->directFileImage($_FILES["image"]);
    if(isset($image_file) && $image_file !== "") {
        $image_file = "../../" . $image_file;
        $hubId = 'hub0'. $_POST['hub'];
        if( $system->RegisterShipperAccount($_POST['username'], $_POST['password'], $image_file , $hubId)) {
             // register success
            $userData = serialize(array(
                "username" => $_POST['username'],
                "password" => $_POST['password'],
                "picURL" =>  $image_file,
                "hubId" => $hubId
            ));
            $_SESSION['login_shipper'] = $userData;
            $_SESSION['newShipper'] = "New Shipper";
            header("location:/View/UserPage/LoginPage.php");

        }
            
    }
}

?>




<html>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="/inc/registerPage.css">

        <link href="https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,400;0,500;0,600;1,300;1,400;1,700&display=swap" rel="stylesheet">


    </head>
    <body>
        <header>
            <ul>
                <li><a href="#"><img src="../../../image/icon/store.png" alt="" width="25px" height="25px"> DAZALA STORE</a></li>
                <li><a href="../../UserPage/LoginPage.php">Login</a> </li>
                <li><a href="../RegisterPage.php">Register</a></li>
            </ul>
        </header>

        <main>
            <div class="display">
                <form action="shipperRegister.php" method="post"  enctype="multipart/form-data">
                    <h1>Sign Up As Shipper</h1>
                    <div class="signUp">
                        <div class="Column1">
                            <label for="username">Username:</label>
                            <div class="inputField">
                                <input type="username" placeholder="Enter Username" id="username" name="username"  required>
                                <img src="../../../image/icon/name.png" class="icon" alt="">
                            </div>
                       
                            
                            <div class="dropdown" id="usernameValid">
                                <p>Contain aleast one lower letters</p>
                                <p>Contain aleast one uppercase letters</p>
                                <p>Contain aleast one digits</p>
                                <p>Length from 8 to 15</p>
                            </div>
                        </div>
                

                        <!-- Input password -->
                                            
                        <div class="Column1">
                            <label for="password">Password: </label>
                            <div class="inputField">
                                <input type="password" placeholder="Enter Password" name="password" id="password" required>
                                <img src="../../../image/icon/password.png" class="icon" alt="">
                            </div>

                            <div class="dropdown" id="passwordValid">
                                <p>Contain aleast one lower letters</p>
                                <p>Contain aleast one uppercase letters</p>
                                <p>Contain aleast one digits</p>
                                <p>Contain aleast one special letter</p>
                                <p>Length from 8 to 20</p>
                            </div>
                        </div>
                
                        <div class="Column1">
                            <label for="password-repeat" ><b>Repeat Password:</b></label>
                            <div class="inputField">
                                <input type="password" placeholder="Repeat Password" name="password-repeat" id="password-repeat" required>
                                <img src="../../../image/icon/password.png" class="icon" alt="">
                            </div>
                            <div class="dropdown">
                            </div>
                        </div>

                        <div class="Column1">
                            <label for="hub"><b>Hub Selection:</b></label>
                            <select class="form-select" aria-label="Default select example" name="hub" id="selector">
                                <option selected disabled="disabled" hidden>Open this to select the Hub Distribution</option>
                                <option value="1">Hub 01</option>
                                <option value="2">Hub 02</option>
                                <option value="3">Hub 03</option>
                            </select>
                            <div class="dropdown">
                            </div>
                        </div>
    
                        <!-- Input Image -->
                        <div class="Column2">
                            <label for="profile-picture">Upload Profile Picture: </label>
                            <input type="file" name="image"  accept="image/png, image/jpg" class="btnUpload" id="profile-picture">
                        </div>
        
                        <div class="display_image">
                            <div class="image"></div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="btnSubmit">
                        <button type="submit" id="submitBtn"><span>Submit</span>  </button>
                        <p>Already have an account? <a href="../../UserPage/LoginPage.php">Log In</a></p>
                    </div>
                    
                </form>
            </div>                                                                                                                                                                                                                                                            
        </main>

        <div id="snackbar">Some text some message..</div>
        

        <script src="shipperRegister.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    </body>
    </html>


</html>

<?php
    if(isset($_POST['username'])) {
        if(in_array($_POST['username'], $namesList) && $_FILES['image'] !=="") {
            ?>
                <script>
                    var notification = document.getElementById("snackbar");
                    notification.innerHTML = "The username is already existed!";
                    notification.className = "show";
                    setTimeout(function(){ notification.className = notification.className.replace("show", ""); }, 3000);
                </script>
            <?php
            echo "name exist";
    
            // register fail
        } else {
            ?>
            <script>
                var notification = document.getElementById("snackbar");
                notification.innerHTML = "Please Fill the form!";
                notification.className = "show";
                setTimeout(function(){ notification.className = notification.className.replace("show", ""); }, 3000);
            </script>
            <?php
        }
    }
    
?>