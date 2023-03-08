<?php

namespace App\Service;

use App\Entity\Command;
use App\Entity\Product;
use App\Entity\Shop;
use App\Repository\CustomerCommandRepository;
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
        $result = [
            'id' => $shop->getId(),
            'name' => $shop->getName(),
            'address' => $shop->getAddress(),
            'phone' => $shop->getPhone(),
            'state' => $shop->getState(),
            'open_time' => $shop->getOpenTime(),
            'closing_time' => $shop->getClosingTime()
        ];
        if ($list_product !== [])
            $result['products'] = $list_product;

        return $result;
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

    public function commandArray(Command $command, ProductRepository $pR = null)
    {
        $list_product = [];
        if ($pR !== null && !empty($command->getCustomerCommands())) {
            foreach ($command->getCustomerCommands() as $command_data) {
                $product = $command_data->getProducts();
                if ($product !== null) {
                    $list_product[] = [
                        'product' => $this->productArray($product),
                        'quantity' => $command_data->getQuantity()
                    ];
                }
            }
        }
        return [
            'id' => $command->getId(),
            'shop' => $this->shopArray($command->getShop()),
            'date_created' => $command->getDateCreated(),
            'date_recup' => $command->getRecupDate(),
            'products' => $list_product
        ];
    }
}
