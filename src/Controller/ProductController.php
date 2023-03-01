<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/products')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product',methods:['GET'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $json = [];
        foreach($productRepository->findAll() as $product){
            $json[] = [
                "name" => $product->getName(),
                "price"=> $product->getPrice()
            ];
        }
        return $this->json([
            'global_products' => $json
        ]);
    }

    #[Route('/',name: 'app_product_add',methods:['POST'])]
    public function add(Request $request,ProductRepository $productRepository): JsonResponse
    {
        $parameters = json_decode($request->getContent());
        $product = new Product;
        $product->setName($parameters->name);
        $product->setPrice($parameters->price);
        $productRepository->save($product,true);
        return $this->json(
            $parameters
        );
    }
}
