<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Customer.php');

include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Vendor.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Shipper.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Hub.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Product.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Broker/DataFile.php');

class VendorBroker
{
    public static $vbObject;
    private const DELIMITER = ';';
    private function __construct()
    {
    }

    public static function getInstance()
    {
        // check if the System Broker is instantiated yet
        if (!isset(self::$vbObject)) {
            self::$vbObject = new VendorBroker();
        }
        return self::$vbObject;
    }

    public static function addNewProduct(Product $product): bool
    {
        global $PRODUCT_DB_ADDRESS;
        try {
            $fp = fopen($PRODUCT_DB_ADDRESS, "a");
            flock($fp, LOCK_EX);
            fputcsv($fp, array(
                `\n` . $product->productId, $product->productName,
                strval($product->price), $product->description, $product->imageURL, $product->vendorUsername
            ), self::DELIMITER);
        } catch (ErrorException | Error $e) {
            return false;
        } finally {
            flock($fp, LOCK_UN);
            fclose($fp);
        }

        return true;
    }

    public static function updateProductDB(Product $products): bool
    {
        global $PRODUCT_DB_ADDRESS;
        try {
            $fp = fopen($PRODUCT_DB_ADDRESS, "w");
            flock($fp, LOCK_EX);

            foreach ($products as $product) {
                fputcsv($fp, array(
                    `\n` . $product->productId, $product->productName,
                    strval($product->price), $product->description, $product->imageURL, $product->vendorUsername
                ), self::DELIMITER);
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
