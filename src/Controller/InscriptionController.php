<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use \DateTime;

class InscriptionController extends AbstractController
{
    /**
     * @Route("/inscription/{nosortie}/{noparticipant}]", name="inscription")
     */
    public function inscription(Request $request, EntityManagerInterface $em): Response
    {
        $inscription = new Inscriptions();
        $date = new DateTime();
        $date->format('Y-m-d H:i:s');
        $inscription->setDateInscription($date);
        $inscription->setParticipantsNoParticipant($request->attributes->get('noparticipant'));
        $inscription->setSortiesNoSortie($request->attributes->get('nosortie'));
        $em->persist($inscription);
        $em->flush();

        return $this->redirectToRoute('sorties');
    }

    /**
     * @Route("/desinscription/{nosortie}/{noparticipant}]", name="desinscription")
     */
    public function desinscription(Request $request, EntityManagerInterface $em): Response
    {
        $inscriptionRepo = $this->getDoctrine()->getRepository(Inscriptions::class);
        $inscription = new Inscriptions();
        $inscription = $inscriptionRepo->findOneBy(['sortiesNoSortie' => $request->attributes->get('nosortie'), 'participantsNoParticipant' => $request->attributes->get('noparticipant')]);
        $em->remove($inscription);
        $em->flush();

        return $this->redirectToRoute('sorties');
    }
}
