<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/Broker/SystemBroker.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Broker/VendorBroker.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Broker/CustomerBroker.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Broker/UserBroker.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Broker/ShipperBroker.php');


class System
{
    private static $systemObject;
    private static $hubList = array();
    private static $accountList = array();
    private static $vendorBroker;
    private static $customerBroker;
    private static $systemBroker;
    private static $shipperBroker;
    private static $userBroker;
    private static $orderList = array();
    private const ADDRESS_PATTERN_REG = "/^.{5,}$/";
    private const USERNAME_PATTERN_REG = "/^\S*(?=\S{8,15})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/";
    private const PASSWORD_PATTERN_REG = "/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[!,@,#,$,%,^,&,*])\S*$/";
    private const PRODUCT_NAME_REG = "/^[\w ]{10,20}+$/";
    private const PRODUCT_PRICE_REG  = "/^[0-9]*[\.]?[0-9]{0,2}$/";
    private const PRODUCT_DES_REG = "/^[\w ]{1,500}+$/";

    /**
     * 
     */
    function __construct()
    {
        self::$vendorBroker = VendorBroker::getInstance();
        self::$systemBroker = SystemBroker::getInstance();
        self::$customerBroker = CustomerBroker::getInstance();
        self::$userBroker = UserBroker::getInstance();
        self::$shipperBroker = ShipperBroker::getInstance();
        self::$hubList = self::$systemBroker->readHubdb();
        $productList = self::$systemBroker->readProductdb();

        self::$orderList = self::$systemBroker->readOrderdb(self::unpacked2DArray($productList));
        self::$accountList = self::$systemBroker->readAccountdb(self::$orderList, $productList, self::$hubList);
    }


    public static function getInstance()
    {
        // check if the System is instantiated yet
        if (!isset(self::$systemObject)) {
            self::$systemObject = new System();
        }
        return self::$systemObject;
    }

    public static function addNewProduct($vendorId, $pName, $pPrice, $pDesc, $pImageURL)
    {
        try {
            if (!isset(self::$accountList['V'][$vendorId])) {
                return null;
            }

            if (!preg_match(self::PRODUCT_NAME_REG, $pName)) {
                return null;
            }
            if (!preg_match(self::PRODUCT_PRICE_REG, $pPrice)) {
                return null;
            }
            if (!preg_match(self::PRODUCT_DES_REG, $pDesc)) {
                return null;
            }
            if (!isset($pImageURL)) {
                return null;
            }
            $pPrice = (float) $pPrice;
            $newProduct =  new Product(self::generateNewProductId(), $pName, $pDesc, $pImageURL, $pPrice, $vendorId);
            if (!self::$accountList['V'][$vendorId]->addNewProduct($newProduct)) {
                throw new Error();
            }
            if (!self::$vendorBroker->addNewProduct($newProduct)) {
                throw new Error();
            }

            return $newProduct;
        } catch (Error | ErrorException $e) {
            return null;
        }
    }

    public static function addNewOrder($ownerId, $orderItems): bool
    {
        try {

            if (!isset(self::$accountList['C'][$ownerId])) {
                return false;
            }
            $hubId =  self::selectRandomHub();
            $newOrder = new Order(self::generateNewOrderId(), $ownerId, OrderStatus::Active,  $hubId);
            $pList = self::getProductList();
            foreach ($orderItems as $pId) {
                if (!isset($pList[$pId])) {
                    return false;
                } else {
                    $newOrder->addProduct($pList[$pId]);
                }
            }
            self::$accountList['C'][$ownerId]->makeNewOrder($newOrder);
            self::$hubList[$hubId]->addOrder($newOrder);
            // add to customer object
            self::$customerBroker->addNewOrder($newOrder);
        } catch (Error | ErrorException $e) {
            return false;
        }
        return true;
    }

    public static function getProductList(): array
    {
        $productArray = array();
        foreach (self::$accountList['V'] as $key) {
            $productArray += $key->getProductList();
        }
        return $productArray;
    }

    public static function getOrderList(): array
    {
        $allOrders = array();
        foreach (self::$orderList as $orders) {
            $allOrders = array_merge($allOrders, $orders);
        }
        return $allOrders;
    }

    public static function getHubList(): array
    {
        $clone_hub = self::$hubList;
        return $clone_hub;
    }

    public static function login($inputUsername, $inputPassword)
    {
        $accountList = self::getAccountList();
        $currentAccount = isset($accountList[$inputUsername]) ? $accountList[$inputUsername] : null;
        if ($currentAccount == null) {
            return null;
        }
        if (!password_verify($inputPassword, $currentAccount->password)) {
            return null;
        }

        return $currentAccount;
    }

    public static function searchProductByName(string $inputName): array
    {

        $resultArray = array();
        $productList = self::getProductList();
        foreach ($productList as $potentialProduct) {
            if (str_contains($potentialProduct->productName, $inputName)) {
                $resultArray[$potentialProduct->productId] = $potentialProduct;
            }
        }

        return $resultArray;
    }

    public static function RegisterCustomerAccount(
        string $username,
        string $password,
        string $imageUrl,
        string $deliveryAddress,
        string $fullname
    ): bool {
        $currentAccount = self::getAccountList();
        if (
            !(preg_match(self::USERNAME_PATTERN_REG, $username))
            || isset($currentAccount[$username])
            || !(preg_match(self::PASSWORD_PATTERN_REG, $password))
            || $imageUrl == null
            || !(preg_match(self::ADDRESS_PATTERN_REG, $deliveryAddress))
            || !isset($fullname)
        ) {
            // throw new Error('Invalid register input');
            return false;
        }
        try {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $newUser = new Customer($username, $password, $imageUrl, $deliveryAddress, array(), $fullname);
            self::$userBroker->addUser($newUser);
            return true;
        } catch (Error | ErrorException $e) {
            return false;
        }
    }

    public static function RegisterVendorAccount(
        string $username,
        string $password,
        string $imageUrl,
        string $busName,
        string $busAddress
    ): bool {
        $currentAccount = self::getAccountList();
        if (
            !(preg_match(self::USERNAME_PATTERN_REG, $username))
            || isset($currentAccount[$username])
            || $imageUrl == null
            || !(preg_match(self::ADDRESS_PATTERN_REG, $busAddress))
            || !(preg_match(self::PASSWORD_PATTERN_REG, $password))
        ) {
            return false;
        }
        try {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $newUser = new Vendor($username, $password, $imageUrl, $busName, $busAddress, array());
            self::$userBroker->addUser($newUser);
            return true;
        } catch (Error | ErrorException $e) {
            return false;
        }
    }

    public static function RegisterShipperAccount(
        string $username,
        string $password,
        string $imageUrl,
        string $hubId
    ): bool {
        $currentAccount = self::getAccountList();
        if (
            !(preg_match(self::USERNAME_PATTERN_REG, $username))
            || !(preg_match(self::PASSWORD_PATTERN_REG, $password))
            || isset($currentAccount[$username])
            || $imageUrl == null
            || !isset(self::$hubList[$hubId])
        ) {
            return false;
        }
        try {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $newUser = new Shipper($username, $password, $imageUrl, self::$hubList[$hubId]);
            self::$userBroker->addUser($newUser);
            return true;
        } catch (Error | ErrorException $e) {
            return false;
        }
    }

    public static function unpacked2DArray($array): array
    {
        $new1DArray = array();
        foreach ($array as $key => $value) {
            foreach ($value as $subkey => $svalue) {
                $new1DArray[$subkey] = $svalue;
            }
        }
        return $new1DArray;
    }

    public static function cancelOrder($orderId): bool
    {
        try {
            $count = 0;
            $orderArray = self::getOrderList();
            foreach ($orderArray as $order) {
                if ($order->orderId == $orderId) {
                    $orderArray[$count]->updateStatus(OrderStatus::Cancelled);
                    break;
                }
                $count++;
            }
            if ($count == sizeof($orderArray)) {
                throw new Error('Order Not found');
            } else {
                self::$shipperBroker->updateOrderDb($orderArray);
                return true;
            }
        } catch (ErrorException | Error $e) {
            return false;
        }
    }
    public static function deliveriedOrder($orderId): bool
    {
        try {
            $count = 0;
            $orderArray = self::getOrderList();
            foreach ($orderArray as $order) {
                if ($order->orderId == $orderId) {
                    $orderArray[$count]->updateStatus(OrderStatus::Deliveried);
                    break;
                }

                $count++;
            }
            if ($count == sizeof($orderArray)) {
                throw new Error('Order Not Found');
            } else {
                self::$shipperBroker->updateOrderDb($orderArray);
                return true;
            }
        } catch (ErrorException | Error $e) {
            return false;
        }
    }

    public static function getAccountList(): array
    {
        $accounts = array();
        foreach (self::$accountList as $account) {
            $accounts += $account;
        }
        return $accounts;
    }

    private static function selectRandomHub(): string
    {
        $randHubOrder =  rand(1, 3);
        $hubname = 'hub0' . strval($randHubOrder);
        return $hubname;
    }
    private static function generateNewOrderId(): string
    {
        $orderList = self::getOrderList();
        arsort($orderList);
        reset($orderList);
        $num = (int)substr($orderList[key($orderList)]->orderId, 1, 3);
        $num += 1;
        $newOrderId = 'O' . str_pad(strval($num), 3, '0', STR_PAD_LEFT);
        return $newOrderId;
    }
    private static function generateNewProductId(): string
    {
        $productList = self::getProductList();
        arsort($productList);
        reset($productList);
        $num = (int)substr(key($productList), 1, 3);
        $num++;
        $newProductId = 'P' . str_pad(strval($num), 3, '0', STR_PAD_LEFT);
        return $newProductId;
    }
    public static function directFileImage($image_file): string
    {
        $target_img_location = 'image/';
        $fileName = $image_file['name'];
        $fileTempName = $image_file['tmp_name'];
        $fileError = $image_file['error'];
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt)); // change the name of the file to the uniq id in the system
        $target_img_location .= uniqid('') . "." . $fileActualExt;
        if ($fileError === 0) {
            move_uploaded_file($fileTempName, '../../../' . $target_img_location);
            return $target_img_location;
        }
        return "";
    }
}
