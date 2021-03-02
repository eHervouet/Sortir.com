<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Participants;
use App\Form\RegisterType;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/register", name="participant_register")
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder ): Response
    {
        $participant = new Participants();
        $registerForm = $this->createForm(RegisterType::class, $participant);

        $registerForm->handleRequest($request);
        if($registerForm->isSubmitted() && $registerForm->isValid()){
            $participant->setAdministrateur(false);
            $participant->setActif(true);
            $participant->setSitesNoSite(0);
            //hashage mdp
            $mdp_hashed = $encoder->encodePassword($participant, $participant->getPassword());
            $participant->setMotDePasse($mdp_hashed);
            $em->persist($participant);
            $em->flush();

            return $this->redirectToRoute("home");
        }

        return $this->render("participants/register.html.twig",[
            "registerForm" => $registerForm->createView()
        ]);
    }

    /**
     * @Route("/login", name="participant_login")
     */
    public function login(): Response
    {
        return $this->render("participants/login.html.twig");
    }

    /**
     * @Route("/logout", name="participant_logout")
     */
    public function logout(): Response  {    }
}
