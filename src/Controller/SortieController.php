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

class SortieController extends AbstractController
{
    /**
     * @Route("/sorties", name="sorties")
     */
    public function liste(): Response
    {
        return $this->render('sortie/listeSortie.html.twig');
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
}
