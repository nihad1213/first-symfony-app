<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_index')]
    public function index(ProductRepository $repository): Response
    {
        $products = $repository->findAll();

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products,
        ]);
    }

    #[Route('/product/{id<\d+>}', name: 'product_show')]
    public function show(int $id, ProductRepository $repository): Response
    {
        $product = $repository->findOneBy(['id' => $id]);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/product/create', name: 'product_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $product = new Product();
            $product->setName($request->request->get('name'));
            $product->setDescription($request->request->get('description'));
            $product->setSize($request->request->get('size'));

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/create.html.twig');
    }

    #[Route('/product/{id<\d+>}/edit', name: 'product_edit')]
    public function edit(int $id, ProductRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = $repository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        if ($request->isMethod('POST')) {
            $product->setName($request->request->get('name'))
                    ->setDescription($request->request->get('description'))
                    ->setSize($request->request->get('size'));

            $entityManager->flush();

            $this->addFlash('success', 'Product updated successfully!');
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
        ]);
    }


    #[Route('/product/{id<\d+>}/delete', name: 'product_delete')]
    public function delete(int $id, ProductRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $product = $repository->find($id);
    
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }
    
        $entityManager->remove($product);
        $entityManager->flush();
    
        $this->addFlash('success', 'Product deleted successfully!');
        return $this->redirectToRoute('product_index');
    }
}
