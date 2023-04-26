<?php

require(__DIR__ . "\User.php");
require(__DIR__ . "\Role.php");

class Customer extends User
{
    public readonly string $deliveryAddress;
    public readonly string $name;
    private $orderList = array();


    public function __construct(string $username, string $password, string $picURL, string $deliveryAddress, $orderList, string $name)
    {
        // need validationg here
        parent::__construct(Role::Customer, $username, $password, $picURL);
        $this->deliveryAddress = $deliveryAddress;
        $this->orderList = $orderList;
        $this->name = $name;
    }

    public function makeNewOrder($newOrder)
    {
        array_push($this->orderList, $newOrder);
    }

    public function getOrderList()
    {
        return $this->orderList;
    }

    public function toArray(): array
    {
        return array(
            Role::Customer->value, $this->username,
            $this->password, parent::getImageURL(),
            strval($this->deliveryAddress), $this->name
        );
    }
}
