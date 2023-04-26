<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Product.php');

class Vendor extends User
{
    public readonly string $businessAddress;
    public readonly string $businessName;
    private $productList;
    /**
     * 
     */
    public function __construct(string $username, string $password, string $picURL, string $businessName, string $businessAddress, array $productList)
    {
        parent::__construct(Role::Vendor, $username, $password, $picURL);
        $this->businessName = $businessName;
        $this->businessAddress = $businessAddress;
        $this->productList = $productList;
    }
    /**
     * 
     */
    public function getProductList(): array
    {
        return $this->productList;
    }
    /**
     * 
     */
    public function addNewProduct($product): bool
    {
        // add product to the list
        try {
            array_push($this->productList, $product);
            return true;
        } catch (ErrorException | Error $e) {
            return false;
        }
    }

    public function toArray(): array
    {
        return array(
            Role::Vendor->value, $this->username,
            $this->password, parent::getImageURL(),
            $this->businessAddress, $this->businessName
        );
    }
}
