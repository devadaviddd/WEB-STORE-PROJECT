<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/Model/OrderStatus.php");

class Order
{
    public readonly string $orderId;
    private $productList = array();
    public  float $totalPrice;
    public string $shippingAddress;
    public readonly string $customerId;

    private  OrderStatus $status;
    public readonly string $hubId;

    public function __construct(
        string $orderId,
        string $customerId,
        OrderStatus $status,
        string $hubId
    ) {
        $this->orderId = $orderId;
        $this->customerId = $customerId;
        $this->status = $status;
        $this->hubId = $hubId;
        $this->totalPrice = 0;
        $this->shippingAddress = "";
    }

    public function addProduct(Product $product): bool
    {
        if (!isset($product)) {
            return false;
        }
        #update total price
        $this->totalPrice += $product->price;
        #add product to a list
        array_push($this->productList, $product);
        return true;
    }

    public  function updateStatus(OrderStatus $status): bool
    {
        if (isset($status)) {
            $this->status = $status;
            return true;
        } else {
            return false;
        }
    }

    public function updateShipAddress(string $address): bool
    {
        if (!isset($address) || $address == "") {
            return false;
        }

        $this->shippingAddress = $address;
        return true;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getProductList(): array
    {
        return $this->productList;
    }

    public function toArray(): array
    {
        return array($this->orderId, $this->customerId, $this->status->value, $this->hubId);
    }
}
