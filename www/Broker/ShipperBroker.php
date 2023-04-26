<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Customer.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Vendor.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Shipper.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Hub.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Product.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Broker/DataFile.php');

class ShipperBroker
{
    public static $spObject;
    private const DELIMITER = ';';
    private function __construct()
    {
    }

    public static function getInstance()
    {
        // check if the System Broker is instantiated yet
        if (!isset(self::$spObject)) {
            self::$spObject = new ShipperBroker();
        }
        return self::$spObject;
    }

    public static function updateOrderDb($orderList): bool
    {
        global $ORDER_DB_ADDRESS;
        try {
            $fp = fopen($ORDER_DB_ADDRESS, "w");
            flock($fp, LOCK_EX);
            foreach ($orderList as $order) {
                $oinfo = $order->toArray();
                fputcsv($fp, $oinfo, self::DELIMITER);
                fwrite($fp, `\n` . "");
                foreach ($order->getProductList() as $p) {
                    fputcsv($fp, array($p->productId));
                    fwrite($fp, `\n` . "");
                }
            }
        } catch (ErrorException | Error $e) {
            return false;
        } finally {
            flock($fp, LOCK_UN);
            fclose($fp);
        }

        return true;
    }
}
