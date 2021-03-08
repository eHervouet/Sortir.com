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
use Symfony\Component\PropertyAccess\PropertyAccess;

class LieuController extends AbstractController
{
    /**
     * @Route("/lieux", name="lieux")
     */
    public function liste(): Response
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $lieuRepo = $this->getDoctrine()->getRepository(Lieux::class);
        $villeRepo = $this->getDoctrine()->getRepository(Villes::class);
        $listeLieux = $lieuRepo->findAll();
        foreach($listeLieux as $lieu) {
            // Récupération de la ville
            $ville = $villeRepo->findOneBy(['noVille' => $propertyAccessor->getValue($lieu, 'villesNoVille')]);
            $lieu->villeNom = $ville->getNomVille();
        }
        return $this->render('lieu/listeLieux.html.twig', ["lieux" => $listeLieux]);
    }
    /**
     * @Route("/creerLieu", name="creerLieu")
     */
    public function creer(Request $request, EntityManagerInterface $em): Response
    {
        $lieu = new Lieux();
        $createLieuForm = $this->createForm(CreateLieuType::class);
        $villeRepo = $this->getDoctrine()->getRepository(Villes::class);
        $createLieuForm->handleRequest($request);
        if($createLieuForm->isSubmitted() && $createLieuForm->isValid()) {
            $data = $createLieuForm->getData();
            $lieu->setLatitude($data{'latitude'});
            $lieu->setLongitude($data['longitude']);
            $lieu->setNomLieu($data['nomLieu']);
            $lieu->setRue($data['rue']);
            if(count($villeRepo->findBy(['nomVille' => $data['villesNoVille']])) > 0) {
                $lieuexist = $villeRepo->findOneBy(['nomVille' => $data['villesNoVille']])->getNoVille();
                $lieu->setVillesNoVille($lieuexist);
            } else {
                $ville = new Villes();
                $ville->setNomVille($data['villesNoVille']);
                $ville->setCodePostal($data['cp']);
                $em->persist($ville);
                $em->flush();
                $lieu->setVillesNoVille($ville->getNoVille());
            }
            $em->persist($lieu);
            $em->flush();
            return $this->redirectToRoute('lieux');
        }
        return $this->render('lieu/creerLieu.html.twig',[
            "createLieuForm" => $createLieuForm->createView()
        ]);
    }
}