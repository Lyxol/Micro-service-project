<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\EntityToArray;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/products')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product', methods: ['GET'])]
    public function index(ProductRepository $productRepository, EntityToArray $entityToArray): JsonResponse
    {
        $json = [];
        foreach ($productRepository->findAll() as $product) {
            $json[] = $entityToArray->productArray($product);
        }
        return $this->json([
            'global_products' => $json
        ]);
    }

    #[Route('/', name: 'app_product_add', methods: ['POST'])]
    public function add(Request $request, ProductRepository $productRepository): JsonResponse
    {
        try {
            $parameters = json_decode($request->getContent());
            $product = new Product;
            if ($productRepository->findOneByName($parameters->name) !== null)
                return $this->json([
                    'error' => 'already exist'
                ], 409);
            $product->setName($parameters->name);
            $product->setPrice($parameters->price);
            $productRepository->save($product, true);
            return $this->json(
                $parameters
            );
        } catch (\Throwable $th) {
            return $this->json([
                'error' => 'bad request'
            ], 400);
        }
    }

    #[Route('/{id}', name: 'app_product_update', methods: ['PATCH'])]
    public function update(string $id, Request $request, EntityManagerInterface $em, EntityToArray $entityToArray): JsonResponse
    {
        $product = $em->getRepository(Product::class)->findById($id);
        if ($product === null)
            return $this->json([
                'error' => 'data not found'
            ], 404);
        $obj_request = json_decode($request->getContent(), true);
        if (array_key_exists('name', $obj_request))
            $product->setName($obj_request['name']);
        if (array_key_exists('desc', $obj_request))
            $product->setDescription($obj_request['desc']);
        if (array_key_exists('price', $obj_request))
            $product->setPrice($obj_request['price']);
        $em->flush();
        return $this->json($entityToArray->productArray($product));
    }
}
