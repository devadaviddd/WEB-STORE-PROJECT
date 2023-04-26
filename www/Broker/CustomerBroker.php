<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Customer.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Order.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Broker/DataFile.php');
class CustomerBroker
{
    private static $cbObject;
    private const ORDER_ID_PATTERN = "/^O[0-9]{3}$/i";
    private const DELIMITER = ';';
    private function __construct()
    {
    }

    public static function getInstance()
    {
        // check if the System Broker is instantiated yet
        if (!isset(self::$cbObject)) {
            self::$cbObject = new CustomerBroker();
        }
        return self::$cbObject;
    }

    public static function addNewOrder(Order $order): bool
    {
        global $ORDER_DB_ADDRESS;
        try {
            $fp = fopen($ORDER_DB_ADDRESS, "a");
            flock($fp, LOCK_EX);
            fputcsv($fp, $order->toArray(), self::DELIMITER);
            fwrite($fp, `\n` . "");
            foreach ($order->getProductList() as $p) {
                fputcsv($fp, array(`\n` . $p->productId));
            }
            fwrite($fp, `\n` . "");
        } catch (ErrorException | Error $e) {
            return false;
        } finally {
            flock($fp, LOCK_UN);
            fclose($fp);
        }

        return true;
    }
}
