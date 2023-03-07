<?php
namespace App\Service;

use App\Entity\Shop;

class EntityToArray
{
    public function shopArray(Shop $shop)
    {
        return [
            'id'=> $shop->getId(),
            'name'=> $shop->getName(),
            'address'=>$shop->getAddress(),
            'phone'=>$shop->getPhone(),
            'state'=>$shop->getState(),
            'product'=>$shop->getProducts()
        ];
    }
}