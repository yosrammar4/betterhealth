<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    // 1️⃣ Afficher la liste des produits
    #[Route('/', name: 'liste_produits')]
    public function listeProduits(EntityManagerInterface $entityManager): Response
    {
        $produits = $entityManager->getRepository(Produit::class)->findAll();

        return $this->render('index.html.twig', [
            'produits' => $produits,
        ]);
    }

    // 2️⃣ Ajouter un produit
    #[Route('/produit/ajouter', name: 'ajouter_produit')]
    public function ajouterProduit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Produit ajouté avec succès !');
            return $this->redirectToRoute('liste_produits');
        }

        return $this->render('ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // 3️⃣ Modifier un produit
    #[Route('/produit/modifier/{id}', name: 'modifier_produit')]
    public function modifierProduit(Produit $produit, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès !');
            return $this->redirectToRoute('liste_produits');
        }

        return $this->render('modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // 4️⃣ Supprimer un produit
    #[Route('/produit/supprimer/{id}', name: 'supprimer_produit')]
    public function supprimerProduit(Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($produit);
        $entityManager->flush();

        $this->addFlash('success', 'Produit supprimé avec succès !');
        return $this->redirectToRoute('liste_produits');
    }
}
