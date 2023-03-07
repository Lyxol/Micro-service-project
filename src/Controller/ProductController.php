<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
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
                "id" => $product->getId(),
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

    //TODO : FIX SHOW PRODUCT
    #[Route('/{id}',name:'app_product_update',methods:['PATCH'])]
    public function update(string $id,Request $request,EntityManagerInterface $em): JsonResponse
    {
        $product = $em->getRepository(Product::class)->findById($id);
        if($product === null)
            return $this->json([
                'error'=>'data not found'
            ],404);
        $obj_request = json_decode($request->getContent(),true);
        if (array_key_exists('name',$obj_request))
            $product->setName($obj_request['name']);
        if (array_key_exists('desc',$obj_request))
            $product->setDescription($obj_request['desc']);
        if (array_key_exists('price',$obj_request))
            $product->setPrice($obj_request['price']);
        $em->flush();
        dd($product);
        return $this->json($product);
    }
}
