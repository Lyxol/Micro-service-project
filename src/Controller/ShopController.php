<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductShop;
use App\Entity\Shop;
use App\Repository\ProductRepository;
use App\Repository\ProductShopRepository;
use App\Repository\ShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\EntityToArray;
use Doctrine\ORM\EntityManagerInterface;

class ShopController extends AbstractController
{
    /***Admin***/
    #[Route('/admin/shop', name: 'app_admin_shop', methods: ['GET'])]
    public function index(ShopRepository $shopRepository, ProductRepository $pR, EntityToArray $EntityToArray): JsonResponse
    {
        $jsonDisplay = [];
        foreach ($shopRepository->findAll() as $shop) {
            $jsonDisplay[] = $EntityToArray->shopArray($shop, $pR);
        }
        return $this->json([
            'global_shops' => $jsonDisplay
        ]);
    }

    #[Route('/admin/shop', name: 'app_admin_shop_add', methods: ['POST'])]
    public function add(ShopRepository $shopRepository, Request $request, EntityToArray $EntityToArray)
    {
        $parameters = json_decode($request->getContent());
        if ($shopRepository->findOneByAddress($parameters->address) !== null) {
            return $this->json([
                'error' => 'already existing'
            ], 409);
        }
        try {
            $shop = new Shop;
            $shop->setName($parameters->name);
            $shop->setAddress($parameters->address);
            $shop->setPhone($parameters->phone);
            $shop->setState($parameters->state);
            $shopRepository->save($shop, true);
            return $this->json($EntityToArray->shopArray($shop));
        } catch (\Throwable $th) {
            return $this->json([
                'error' => 'bad request'
            ], 400);
        }
    }

    #[Route('/admin/shop/{id_shop}/products', name: 'app_admin_shop_add_product', methods: ['POST'])]
    public function addProduct(ShopRepository $sR, ProductRepository $pR, ProductShopRepository $pSR, Request $request, EntityToArray $EntityToArray, string $id_shop)
    {
        $list_products = json_decode($request->getContent());
        $shop = $sR->findOneById($id_shop);
        if ($shop === null)
            return $this->json([
                'error' => 'does not exist'
            ], 404);
        try {
            foreach ($list_products->products as $data_product) {
                $product = $pR->findById($data_product->id);
                if (
                    $product !== null &&
                    $pSR->findByIdProductAndIdShop($data_product->id, $id_shop) === null
                ) {
                    $product_shop = new ProductShop;
                    $product_shop->setIdShop($shop);
                    $product_shop->setIdProduct($product);
                    $product_shop->setQuantity($data_product->quantity);
                    $pSR->save($product_shop, true);
                }
            }
            return $this->json(
                $EntityToArray->shopArray($sR->findOneById($id_shop), $pR)
            );
        } catch (\Throwable $th) {
            return $this->json([
                'error' => 'bad request'
            ], 400);
        }
    }

    #[Route('/admin/shop/{id_shop}/products/{id_product}', name: 'app_admin_shop_update_product', methods: ['PATCH'])]
    public function updateQuantityProduct(EntityManagerInterface $em, Request $request, EntityToArray $EntityToArray, string $id_shop, string $id_product)
    {

        $product_shop = $em->getRepository(ProductShop::class)->findByIdProductAndIdShop($id_product, $id_shop);
        $product = $em->getRepository(Product::class)->findById($id_product);
        if ($product_shop === null || $product === null)
            return $this->json([
                'error' => 'does not exist'
            ], 404);
        try {
            $newQuantity = json_decode($request->getContent())->quantity;
            $product_shop->setQuantity($newQuantity);
            $em->flush();
            return $this->json([
                'product' => $EntityToArray->productArray($product),
                'quantity' => $product_shop->getQuantity()
            ]);
        } catch (\Throwable $th) {
            return $this->json([
                'error' => 'bad request'
            ], 400);
        }
    }

    /***User***/
    #[Route('/shop/{id_shop}/products', name: 'app_shop_product', methods: ['GET'])]
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

    #[Route('/shop/{id_shop}/products/{id_product}', name: 'app_shop_unique_product', methods: ['GET'])]
    public function showOneProduct(ShopRepository $sR, ProductRepository $pR, ProductShopRepository $pSR, EntityToArray $entityToArray, string $id_shop, string $id_product)
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
