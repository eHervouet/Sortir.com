<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Entity\Participants;
use App\Form\CreateSortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;


class SortieController extends AbstractController
{
    /**
     * @Route("/sorties", name="sorties")
     */
    public function liste(): Response
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $sortieRepo = $this->getDoctrine()->getRepository(Sorties::class);
        $participantRepo = $this->getDoctrine()->getRepository(Participants::class);
        $listeSorties = $sortieRepo->findAll();
        foreach($listeSorties as $sortie) {
            // Récupération du nom et du prénom de l'organisateur
            $orga = $participantRepo->findOneBy(['noParticipant' => $propertyAccessor->getValue($sortie, 'organisateur')]);
            $sortie->nomorganisateur = $orga->getNom().' '.$orga->getPrenom();

            // Récupération du nombre d'inscrit à la sortie

        }
        return $this->render('sortie/listeSortie.html.twig', [
            'listeSorties' => $listeSorties
        ]);
    }

    /**
     * @Route("/creerSortie", name="creerSortie")
     */
    public function creer(Request $request, EntityManagerInterface $em, UserInterface $user): Response
    {
        $sortie = new Sorties();
        $createSortieForm = $this->createForm(CreateSortieType::class, $sortie);
        $createSortieForm->handleRequest($request);
        if($createSortieForm->isSubmitted() && $createSortieForm->isValid()) {
            $participantRepo = $this->getDoctrine()->getRepository(Participants::class);
            $participant = $participantRepo->findOneBy(['pseudo' => $user->getUsername()]);
            $sortie->setOrganisateur($participant->getNoParticipant());
            $sortie->setEtatsNoEtat(1);
            $sortie->setLieuxNoLieu(1);
            $em->persist($sortie);
            $em->flush();
        }
        return $this->render('sortie/creerSortie.html.twig',[
            "createSortieForm" => $createSortieForm->createView()
        ]);
    }

    /**
     * @Route("/afficherSortie/{noSortie}", name="afficherSortie")
     */
    public function afficherSortie(Request $request): Response
    {
        $noSortie = $request->attributes->get('noSortie');
        $sortieRepo = $this->getDoctrine()->getRepository(Sorties::class);
        $sortie = $sortieRepo->findOneBy(['noSortie' => $noSortie]);
        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie
        ]);
    }
}
