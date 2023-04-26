<?php

// require all models
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/System.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Customer.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Vendor.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Shipper.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Hub.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Broker/DataFile.php');


class SystemBroker
{
    private static $sbObject;
    private const HUB_ID_PATTERN = "/^hub[0-9]{2}$/i";
    private const ORDER_ID_PATTERN = "/^O[0-9]{3}$/i";
    private const PRODUCT_ID_PATTERN = "/^P[0-9]{3}$/i";
    private function __construct()
    {
    }

    public static function getInstance()
    {
        // check if the System Broker is instantiated yet
        if (!isset(self::$sbObject)) {
            self::$sbObject = new SystemBroker();
        }
        return self::$sbObject;
    }

    private static function distributeOrderToHubs(&$hubList, $orderList)
    {
        foreach ($orderList as $value) {
            $value_temp = clone $value;
            $hubList[$value->hubId]->addOrder($value_temp);
        }
    }



    public static function readAccountdb($ordersList, $productsList, &$hubList): array
    {
        global $ACCOUNT_DB_ADDRESS;
        $userList = array(Role::Customer->value => array(), Role::Vendor->value => array(), Role::Shipper->value => array());
        $fp = fopen($ACCOUNT_DB_ADDRESS, 'r');
        flock($fp, LOCK_SH);
        while ($line = fgetcsv($fp, 0, ";")) {
            $accountInfos = $line;
            try {
                $role = Role::from($accountInfos[0]);
                $username = $accountInfos[1];
                $password = $accountInfos[2];
                $imageURL = $accountInfos[3];

                // intantiate the user class
                if ($role === Role::Customer) {
                    $deliveryAddress = $accountInfos[4];
                    $name = $accountInfos[5];
                    $orderList = $ordersList[$username] ?? array();
                    if (sizeof($orderList) > 0) {
                        foreach ($ordersList[$username] as $o) {
                            $o->updateShipAddress($deliveryAddress);
                        }
                    }
                    $userList[Role::Customer->value][$username] =  new Customer($username, $password, $imageURL, $deliveryAddress, $orderList, $name);
                    self::distributeOrderToHubs($hubList, $orderList);
                } else if ($role === Role::Vendor) {
                    $businessName = $accountInfos[4];
                    $businessAddress = $accountInfos[5];
                    $productList = $productsList[$username] ?? array();
                    $userList[Role::Vendor->value][$username] =  new Vendor($username, $password, $imageURL, $businessAddress, $businessName, $productList);
                } else if ($role === Role::Shipper) {
                    $hubId = $accountInfos[4];
                    $hub = $hubList[$hubId] ?? $hubList['hub01'];
                    $userList[Role::Shipper->value][$username] = new Shipper($username, $password, $imageURL, $hub);
                }
            } catch (Error  $e) {
                continue;
            }
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        return $userList;
    }

    public static function readHubdb(): array
    {
        global $HUB_DB_ADDRESS;
        $hubList = array();
        $fp = fopen($HUB_DB_ADDRESS, 'r');
        flock($fp, LOCK_SH);
        while ($line = fgetcsv($fp, 0, ";")) {
            $hubInfos = $line;
            $hubList[$hubInfos[0]] = new Hub($hubInfos[0], $hubInfos[1]);
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        return $hubList;
    }

    public static function readProductdb(): array
    {
        global $PRODUCT_DB_ADDRESS;
        $productList = array();
        $fp = fopen($PRODUCT_DB_ADDRESS, 'r');
        flock($fp, LOCK_SH);
        while ($line = fgetcsv($fp, 0, ";")) {
            try {
                $productInfos = $line;
                if (preg_match_all(self::PRODUCT_ID_PATTERN, $productInfos[0])) {
                    if (!isset($productList[$productInfos[5]])) {
                        $productList[$productInfos[5]] = array();
                    }

                    if (!isset($productList[$productInfos[5]][$productInfos[0]])) {
                        $productList[$productInfos[5]][$productInfos[0]] = new Product(
                            $productInfos[0],
                            $productInfos[1],
                            $productInfos[3],
                            $productInfos[4],
                            $productInfos[2],
                            $productInfos[5]
                        );
                    } else {
                        array_push($productList[$productInfos[5]][$productInfos[0]], new Product(
                            $productInfos[0],
                            $productInfos[1],
                            $productInfos[3],
                            $productInfos[4],
                            $productInfos[2],
                            $productInfos[5]
                        ));
                    }
                }
            } catch (Error $e) {
                continue;
            }
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        return $productList;
    }

    public static function readOrderdb($productList): array
    {
        global $ORDER_DB_ADDRESS;
        $orderList = array();
        $counter = 0;
        $username = "";
        $fp = fopen($ORDER_DB_ADDRESS, 'r');
        flock($fp, LOCK_SH);
        while ($line = fgetcsv($fp, 0, ";")) {
            try {
                $orderInfos = $line;
                if (preg_match_all(self::ORDER_ID_PATTERN, $orderInfos[0])) {
                    $username = $orderInfos[1];
                    $status = OrderStatus::from($orderInfos[2]);
                    if (!isset($orderList[$orderInfos[1]])) {
                        $orderList[$orderInfos[1]] = array(new Order(
                            orderId: $orderInfos[0],
                            customerId: $orderInfos[1],
                            status: $status,
                            hubId: preg_match_all(self::HUB_ID_PATTERN, $orderInfos[3]) ? $orderInfos[3] : 'hub01'
                        ));
                        $counter++;
                    } else {
                        array_push($orderList[$orderInfos[1]], new Order(
                            orderId: $orderInfos[0],
                            customerId: $orderInfos[1],
                            status: $status,
                            hubId: preg_match_all(self::HUB_ID_PATTERN, $orderInfos[3]) ? $orderInfos[3] : 'hub01'
                        ));
                        $counter++;
                    }
                } else if (preg_match_all(self::PRODUCT_ID_PATTERN, $orderInfos[0])) {
                    // end($orderList); # set the pointer to the end
                    // check for invalid Item id in the order db file
                    if (is_null($productList[$orderInfos[0]])) {
                        throw new ErrorException('Invalid product id in ' . key($orderList));
                    }
                    #add item to order
                    // $username = key($orderList);
                    $orderList[$username][count($orderList[$username]) - 1]->addProduct($productList[$orderInfos[0]]);
                } else {

                    throw new ErrorException("Invalid line of data in order.db");
                }
            } catch (Error | ErrorException $e) {
                continue;
            }
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        // System::getInstance()->nextOrderId = 'O' . str_pad(strval($counter), 3, '0', STR_PAD_LEFT);
        return $orderList;
    }
}
