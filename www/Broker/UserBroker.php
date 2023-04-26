<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Customer.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Vendor.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Shipper.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Hub.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Product.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Broker/DataFile.php');

class UserBroker
{
    public static $ubObject;
    private const DELIMITER = ';';
    private function __construct()
    {
    }

    public static function getInstance(): UserBroker
    {
        // check if the System Broker is instantiated yet
        if (!isset(self::$ubObject)) {
            self::$ubObject = new UserBroker();
        }
        return self::$ubObject;
    }

    public static function addUser($user): bool
    {
        global $ACCOUNT_DB_ADDRESS;
        try {
            $fp = fopen($ACCOUNT_DB_ADDRESS, "a");
            flock($fp, LOCK_EX);
            $ua = $user->toArray();
            fputcsv($fp, $ua, self::DELIMITER);
            fwrite($fp, `\n` . '');
        } catch (ErrorException | Error $e) {
            return false;
        } finally {
            flock($fp, LOCK_UN);
            fclose($fp);
        }

        return true;
    }

    public static function updateUserDB($users): bool
    {
        global $ACCOUNT_DB_ADDRESS;
        try {
            $fp = fopen($ACCOUNT_DB_ADDRESS, "w");
            flock($fp, LOCK_EX);

            foreach ($users as $user) {
                fputcsv($fp, $user->toArray(), self::DELIMITER);
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
