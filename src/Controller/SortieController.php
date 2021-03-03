<?php

namespace App\Controller;

use App\Entity\Etats;
use App\Entity\Inscriptions;
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
    public function liste(UserInterface $user): Response
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $sortieRepo = $this->getDoctrine()->getRepository(Sorties::class);
        $participantRepo = $this->getDoctrine()->getRepository(Participants::class);
        $inscriptionRepo = $this->getDoctrine()->getRepository(Inscriptions::class);
        $etatRepo = $this->getDoctrine()->getRepository(Etats::class);
        $listeSorties = $sortieRepo->findAll();
        foreach($listeSorties as $sortie) {
            // Récupération du nom et du prénom de l'organisateur
            $orga = $participantRepo->findOneBy(['noParticipant' => $propertyAccessor->getValue($sortie, 'organisateur')]);
            $sortie->nomorganisateur = $orga->getNom().' '.$orga->getPrenom();

            // Récupération du nombre d'inscrit à la sortie
            $nbInscrit = $inscriptionRepo->findBy(['sortiesNoSortie' => $propertyAccessor->getValue($sortie, 'nosortie')]);
            $sortie->nbinscrit = count($nbInscrit);

            // Récupération du libelle de l'état
            $libelleEtat = $etatRepo->findOneBy(['noEtat' => $propertyAccessor->getValue($sortie, 'etatsNoEtat')]);
            $sortie->libelleetat = $libelleEtat->getLibelle();

            // L'utilisateur actuel est inscrit ?
            $actualUser = $participantRepo->findOneBy(['pseudo' => $user->getUsername()]);
            $estInscrit = $inscriptionRepo->findOneBy(['participantsNoParticipant' => $actualUser->getNoParticipant(), 'sortiesNoSortie' => $propertyAccessor->getValue($sortie, 'nosortie')]);
            empty($estInscrit) ? $sortie->estinscrit = false : $sortie->estinscrit = true;
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
