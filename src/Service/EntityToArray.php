<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Shop;
use App\Repository\ProductRepository;

class EntityToArray
{
    public function shopArray(Shop $shop, ProductRepository $pR = null)
    {
        $list_product = [];
        if ($pR !== null && !empty($shop->getProducts()))
            foreach ($shop->getProducts() as $product_data) {
                $list_product[] = [
                    'product' => $this->productArray($product_data->getIdProduct()),
                    'quantity' => $product_data->getQuantity()
                ];
            }
        return [
            'id' => $shop->getId(),
            'name' => $shop->getName(),
            'address' => $shop->getAddress(),
            'phone' => $shop->getPhone(),
            'state' => $shop->getState(),
            'open_time' => $shop->getOpenTime(),
            'closing_time' => $shop->getClosingTime(),
            'products' => $list_product
        ];
    }

    public function productArray(Product $product)
    {
        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'desc' => $product->getDescription(),
            'price' => $product->getPrice()
        ];
    }
}
