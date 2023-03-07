<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ProductShopRepository;
use App\Repository\ShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\EntityToArray;

#[Route('/shop')]
class ShopController extends AbstractController
{
    #[Route('/closest', name: 'app_shop_closest', methods: ['GET'])]
    public function index(ShopRepository $sR, ProductRepository $pR, Request $request, EntityToArray $entityToArray)
    {
        //TODO : Replace test by user state
        //Test
        $state = $request->query->get('state');
        //
        $response = [];
        foreach ($sR->findByState($state) as $shop) {
            $response[] = $entityToArray->shopArray($shop, $pR);
        }
        return $this->json([
            'closest_shops' => $response
        ]);
    }

    #[Route('/{id_shop}/products', name: 'app_shop_product', methods: ['GET'])]
    public function showProduct(ShopRepository $sR, EntityToArray $entityToArray, string $id_shop)
    {
        $shop = $sR->findOneById($id_shop);
        if ($shop === null)
            return $this->json([
                'error' => 'does not exist'
            ], 404);
        $json_display = [];
        foreach ($shop->getProducts() as $product_data) {
            $json_display[] = [
                'product' => $entityToArray->productArray($product_data->getIdProduct()),
                'quantity' => $product_data->getQuantity()
            ];
        }
        return $this->json([
            'products_in_shop' => $json_display
        ]);
    }

    #[Route('/{id_shop}/products/{id_product}', name: 'app_shop_unique_product', methods: ['GET'])]
    public function showOneProduct(ShopRepository $sR, ProductRepository $pR, ProductShopRepository $pSR, string $id_shop, string $id_product)
    {
        $shop = $sR->findOneById($id_shop);
        if ($shop === null)
            return $this->json([
                'error' => 'shop does not exist'
            ], 404);
        $product = $pR->findById($id_product);
        if ($product === null)
            return $this->json([
                'error' => 'product does not exist'
            ], 404);
        $quantity = $pSR->findByIdProductAndIdShop($id_product, $id_shop)->getQuantity();
        if ($quantity === null) {
            return $this->json([
                'error' => "the shop doesn't not sell this product"
            ], 404);
        }
        return $this->json([
            'product' => [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice()
            ],
            'quantity' => $quantity
        ]);
    }
}
