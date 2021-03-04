<?php

namespace App\Controller;


use App\Entity\Lieux;
use App\Entity\Villes;
use App\Form\CreateLieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/lieux", name="lieux")
     */
    public function liste(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Lieux::class);
        $Lieux = $repo->findAll();
        return $this->render('lieu/listeLieux.html.twig', ["lieux" => $Lieux]);
    }
    /**
     * @Route("/creerLieu", name="creerLieu")
     */
    public function creer(Request $request, EntityManagerInterface $em): Response
    {
        $Lieu = new Lieux();
        $createLieuForm = $this->createForm(CreateLieuType::class, $Lieu, array('method' => 'POST'));

        $createLieuForm->handleRequest($request);
        if($createLieuForm->isSubmitted() && $createLieuForm->isValid()) {
            $em->persist($Lieu);
            $em->flush();
        }
        return $this->render('lieu/creerLieu.html.twig',[
            "createLieuForm" => $createLieuForm->createView()
        ]);
    }
}