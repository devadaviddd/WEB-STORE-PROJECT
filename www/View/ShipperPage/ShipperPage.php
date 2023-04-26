<?php
include($_SERVER['DOCUMENT_ROOT'] . '/View/UserPage/session.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/System.php');


$login_shipper = unserialize($_SESSION['login_shipper']);
$hubOrderList = array();

$system = System::getInstance();
$error = "";

function getHubOrderList()
{
    $tmp_orderList =  $GLOBALS['system']->getOrderList();
    $hubId = $GLOBALS['login_shipper']['hubId'];
    foreach ($tmp_orderList as $order) {
        if ($order->hubId == $hubId && $order->getStatus()->value == 'A') {
            array_push($GLOBALS['hubOrderList'], $order);
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    try {
        getHubOrderList();
    } catch (Error | ErrorException $e) {
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $submit = file_get_contents('php://input');
    if (isset($submit)) {
        $object = json_decode($submit);
        if (isset($object->cancel_order_id)) {
            $oid = $object->cancel_order_id;
            if ($system->cancelOrder($oid)) {
                header("Status: 200 Success");
            } else {
                header("Status: 400 Success");
            }
        } elseif (isset($object->delivered_order_id)) {
            $oid = $object->delivered_order_id;
            if ($system->deliveriedOrder($oid)) {
                header("Status: 200 Success");
            } else {
                header("Status: 400 Success");
            }
        } else {
            header("Status: 400 fail");
        }
    }
}
?>

<?php

function loadOrderList()
{
    foreach ($GLOBALS['hubOrderList'] as $order) {
?>
        <div class="col" id="<?php echo $order->orderId . '-item-card'; ?>">
            <div class="card" name="item">
                <div class="card-header text-center">
                    <h5 class="card-title"><?php echo $order->orderId; ?></h5>

                </div>
                <div class="card-body ">

                    <p class="card-text">
                        <strong>Shipping Address:</strong> <?php echo $order->shippingAddress; ?>
                    </p>
                    <div class="row">
                        <p class="card-text col"><strong>Total: </strong>$<?php echo $order->totalPrice; ?></p>
                        <p class="card-text col"><strong>Status: </strong><?php echo $order->getStatus()->value == 'A' ? "Active" : "Passive"; ?></p>
                    </div>
                    <button type="button" data-bs-toggle="modal" data-bs-target="<?php echo '#' . $order->orderId . '-modal';  ?>" class="btn btn-outline-primary ">View Detail</button>
                </div>
            </div>
        </div>

<?php
    }
}
?>

<?php
// learn from https://getbootstrap.com/docs/5.0/components/modal/
function loadOrderDetails()
{
    foreach ($GLOBALS['hubOrderList'] as $order) {
?>

        <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" id="<?php echo $order->orderId . '-modal';  ?>">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="staticBackdropLabel"><?php echo $order->orderId; ?></h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="modal-title" id="staticBackdropLabel"><strong>Shipping Address: </strong><?php echo $order->shippingAddress; ?></p>
                        <hr>
                        <?php
                        $pCount = array_count_values(array_column($order->getProductList(), 'productId'));
                        $pl = $order->getProductList();
                        try {
                            $nonDupPList = array_unique($pl, SORT_REGULAR);
                        } catch (Error | ErrorException $e) {
                            $a = $e;
                        }
                        ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">PID</th>
                                    <th scope="col" colspan="2">Name</th>
                                    <th scope="col">Unit Price($)</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Price($)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($nonDupPList as $product) {
                                ?>


                                    <tr>
                                        <th><?php echo $product->productId; ?></th>
                                        <td colspan="2"><?php echo $product->productName; ?></td>
                                        <td><?php echo number_format($product->price, 2); ?></td>
                                        <td><?php echo $pCount[$product->productId]; ?></td>
                                        <td><?php echo number_format($pCount[$product->productId] * $product->price, 2); ?></td>
                                    </tr>

                                <?php
                                }
                                echo '</tbody>';
                                echo '</table>';
                                echo '</div>';
                                ?>
                                <div class="modal-footer text-start">
                                    <p class="col 2"> <strong>Total Price: </strong>$<?php echo $order->totalPrice; ?></p>
                                    <button type="button" onclick="cancelOrder('<?php echo $order->orderId; ?>')" class="btn btn-danger" data-bs-dismiss="modal">Cancel Order</button>
                                    <button type="button" onclick="deliveriedOrder('<?php echo $order->orderId; ?>')" class="btn btn-success" data-bs-dismiss="modal">Deliveried</button>
                                </div>
                        <?php

                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
                        ?>
                        <!DOCTYPE html>
                        <html lang="en">

                        <head>
                            <meta charset="UTF-8">
                            <meta http-equiv="X-UA-Compatible" content="IE=edge">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Shipper page</title>
                            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
                            <link href="/inc/shipper.css" rel="stylesheet" />
                        </head>

                        <header>
                            <ul>
                                <li><a href="#"><img src="../../image/icon/store.png" alt="" width="25px" height="25px"> DAZALA STORE</a></li>
                                <li><a href="../UserPage/ProfilePage.php"><?php echo $login_shipper['username'] ?></a> </li>
                                <li><a href="/View/UserPage/Logout.php">LOG OUT</a></li>
                            </ul>
                        </header>

                        <body>


                            <div class="container row-ml-1">
                                <p class="d-block col-md h2 " id="sub_title"><?php echo $login_shipper['hubName'];  ?></p>
                            </div>
                            <div class="row row-cols-1 row-cols-md-3 g-3 mt-3 px-5">
                                <?php
                                loadOrderList();
                                ?>
                            </div>
                            <?php loadOrderDetails(); ?>



                            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
                            <script src="ShipperPage.js"></script>
                            <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/View/ShareComponent/footer.php'); ?>
                        </body>
                        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                            <div id="announceToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="d-flex">
                                    <div class="toast-body" id="announce">
                                    </div>
                                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>

                            </div>
                        </div>

                        </html>