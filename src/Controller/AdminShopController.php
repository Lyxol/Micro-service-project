<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Product;
use App\Entity\ProductShop;
use App\Entity\Shop;
use App\Service\EntityToArray;
use App\Repository\ShopRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductShopRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/admin/shop')]
class AdminShopController extends AbstractController
{
    #[Route('/', name: 'app_admin_shop', methods: ['GET'])]
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

    #[Route('/', name: 'app_admin_shop_add', methods: ['POST'])]
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
            $shop->setOpenTime($parameters->open_time);
            $shop->setClosingTime($parameters->closing_time);
            $shopRepository->save($shop, true);
            return $this->json($EntityToArray->shopArray($shop));
        } catch (\Throwable $th) {
            return $this->json([
                'error' => 'bad request'
            ], 400);
        }
    }

    #[Route('/{id_shop}/products', name: 'app_admin_shop_add_product', methods: ['POST'])]
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

    #[Route('/{id_shop}/products/{id_product}', name: 'app_admin_shop_update_product', methods: ['PATCH'])]
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

    #[Route('/{id_shop}/products/{id_product}', name: 'app_admin_shop_delete_product', methods: ['DELETE'])]
    public function deleteProduct(ProductShopRepository $pSR, string $id_shop, string $id_product)
    {
        $target = $pSR->findByIdProductAndIdShop($id_product, $id_shop);
        $last_deleted = [
            'id_product' => $target->getIdProduct()->getId(),
            'last_quantity_deleted' => $target->getQuantity()
        ];
        if ($target === null)
            return $this->json([
                'error' => 'does not exist'
            ], 404);
        $pSR->remove($target, true);
        return $this->json($last_deleted);
    }

    #[Route('/{id_shop}/commands/notify', name: 'app_admin_shop_notify_customer', methods: ['POST'])]
    public function notify(MailerInterface $mailer,ShopRepository $sR,string $id_shop)
    {

        $shop = $sR->findOneById($id_shop);
        if($shop === null)
            return $this->json([
                'error'=>'shop does not exist'
            ],404);
        $display = [];
        foreach($shop->getCommands() as $command){
            $send = true;
            $user = $command->getIdCustomer();
            try {
                $email = (new Email())
                ->from('shopdeliut@gmail')
                ->to($user->getEmail())
                ->subject('Votre commande est prête!')
                ->text('Sending emails is fun again!')
                ->html('<p>See Twig integration for better HTML integration!</p>');
                $mailer->send($email);   
            } catch (\Throwable $th) {
                $send = false;
            }
            $display[] = [
                'command_id'=>$command->getId(),
                'user_id'=>$user->getId(),
                'email_send'=>$send
            ];
        }
        return $this->json($display);
    }
}
