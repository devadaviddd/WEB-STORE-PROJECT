<?php

class Product
{
    public readonly string $productId;
    public readonly string $productName;
    public readonly string $description;
    public readonly string $imageURL;
    public readonly float $price;
    public readonly string $vendorUsername;

    public function __construct(
        string $productId,
        string $productName,
        string $description,
        string $imageURl,
        float $price,
        string $vendorUsername
    ) {
        $this->productId = $productId;
        $this->productName = $productName;
        $this->description = $description;
        $this->imageURL = $imageURl;
        $this->price = $price;
        $this->vendorUsername = $vendorUsername;
    }
}
