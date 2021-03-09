<?php

namespace App\Controller;

use App\Entity\Etats;
use App\Entity\Inscriptions;
use App\Entity\Lieux;
use App\Entity\Sorties;
use App\Entity\Participants;
use App\Entity\Villes;
use App\Form\CreateSortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Form\CancelSortieType;


class SortieController extends AbstractController
{
    /**
     * @Route("/sorties", name="sorties")
     */
    public function liste(TokenStorageInterface $tokenStorage): Response
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $sortieRepo = $this->getDoctrine()->getRepository(Sorties::class);
        $participantRepo = $this->getDoctrine()->getRepository(Participants::class);
        $inscriptionRepo = $this->getDoctrine()->getRepository(Inscriptions::class);
        $etatRepo = $this->getDoctrine()->getRepository(Etats::class);
        $listeSorties = $sortieRepo->findAllFilteredByDateAndState();
        foreach($listeSorties as $sortie) {
            // Récupération du nom, prénom, pseudo de l'organisateur
            $orga = $participantRepo->findOneBy(['noParticipant' => $propertyAccessor->getValue($sortie, 'organisateur')]);
            $sortie->nomorganisateur = $orga->getNom().' '.$orga->getPrenom();
            $sortie->numorganisateur = $orga->getNoParticipant();
            $sortie->pseudoorganisateur = $orga->getPseudo();

            // Récupération du nombre d'inscrit à la sortie
            $nbInscrit = $inscriptionRepo->findBy(['sortiesNoSortie' => $propertyAccessor->getValue($sortie, 'nosortie')]);
            $sortie->nbinscrit = count($nbInscrit);

            // Récupération du libelle de l'état
            $libelleEtat = $etatRepo->findOneBy(['noEtat' => $propertyAccessor->getValue($sortie, 'etatsNoEtat')]);
            $sortie->libelleetat = $libelleEtat->getLibelle();

            // L'utilisateur actuel est inscrit ?
            if($tokenStorage->getToken() != null) {
                $user=$tokenStorage->getToken()->getUser();
                $actualUser = $participantRepo->findOneBy(['pseudo' => $user->getUsername()]);
                $estInscrit = $inscriptionRepo->findOneBy(['participantsNoParticipant' => $actualUser->getNoParticipant(), 'sortiesNoSortie' => $propertyAccessor->getValue($sortie, 'nosortie')]);
                empty($estInscrit) ? $sortie->estinscrit = false : $sortie->estinscrit = true;
            } else {
                $sortie->estinscrit = false;
            }

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
        $dump ="";
        $sortie = new Sorties();
        $createSortieForm = $this->createForm(CreateSortieType::class);
        $createSortieForm->handleRequest($request);
        if($createSortieForm->isSubmitted() && $createSortieForm->isValid()) {
            $data = $createSortieForm->getData();

            // Récupération du nom de l'utilisateur
            $participantRepo = $this->getDoctrine()->getRepository(Participants::class);
            $participant = $participantRepo->findOneBy(['pseudo' => $user->getUsername()]);
            $sortie->setNom($data['nom']);
            $sortie->setDatedebut($data['datedebut']);
            $sortie->setDatecloture($data['datecloture']);
            $sortie->setNbinscriptionsmax($data['nbinscriptionsmax']);
            $sortie->setDuree($data['duree']);
            $sortie->setDescriptioninfos($data['descriptioninfos']);
            $sortie->setLieuxNoLieu($data['lieuxnolieu']->getNoLieu());
            $sortie->setOrganisateur($participant->getNoParticipant());
            $sortie->setEtatsNoEtat(1);
            $em->persist($sortie);
            $em->flush();
        }
        return $this->render('sortie/creerSortie.html.twig',[
            "createSortieForm" => $createSortieForm->createView(),
            'dump' => $dump
        ]);
    }

    /**
     * @Route("/afficherSortie/{noSortie}", name="afficherSortie")
     */
    public function afficherSortie(Request $request): Response
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $noSortie = $request->attributes->get('noSortie');
        $sortieRepo = $this->getDoctrine()->getRepository(Sorties::class);
        $lieuRepo = $this->getDoctrine()->getRepository(Lieux::class);
        $villeRepo = $this->getDoctrine()->getRepository(Villes::class);
        $sortie = $sortieRepo->findOneBy(['noSortie' => $noSortie]);

        $lieu = $lieuRepo->findOneBy(['noLieu' => $propertyAccessor->getValue($sortie, 'lieuxNoLieu')]);
        $ville = $villeRepo->findOneBy(['noVille' => $propertyAccessor->getValue($lieu, 'villesNoVille')]);
        $lieu->laville = $ville;
        $sortie->lieu = $lieu;
        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie
        ]);
    }

    /**
     * @Route("/annuler/{nosortie}", name="annuler")
     */
    public function annuler(Request $request, EntityManagerInterface $em): Response
    {
        $sortieRepo = $this->getDoctrine()->getRepository(Sorties::class);
        $etatRepo = $this->getDoctrine()->getRepository(Etats::class);
        $sortie = $sortieRepo->findOneBy(['noSortie' => $request->attributes->get('nosortie')]);
        $cancelSortieForm = $this->createForm(CancelSortieType::class);
        $cancelSortieForm->handleRequest($request);
        if($cancelSortieForm->isSubmitted() && $cancelSortieForm->isValid()) {
            $data = $cancelSortieForm->getData();
            $etat = $etatRepo->findOneBy(['libelle' => 'Annulée']);
            $sortie->setDescriptionInfos("ANNULATION : ".$data['motif']);
            $sortie->setEtatsNoEtat($etat->getNoEtat());
            $em->flush();
            return $this->redirectToRoute('sorties');
        }
        return $this->render('sortie/annulerSortie.html.twig', [
            "cancelSortieForm" => $cancelSortieForm->createView(),
            'sortie' => $sortie
        ]);
    }
}
