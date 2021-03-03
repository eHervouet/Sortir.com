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
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/register", name="participant_register")
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder ): Response
    {
        $participant = new Participants();
        $registerForm = $this->createForm(RegisterType::class, $participant, array('method' => 'POST'));

        $registerForm->handleRequest($request);
        if($registerForm->isSubmitted() && $registerForm->isValid()){
            $participant->setAdministrateur(false);
            $participant->setActif(true);
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
     * @Route("/my-profil", name="my_profil")
     */
    public function myProfil(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder) : Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        //l'utilisateur n'est pas connecté
        if(!$user)
            return $this->redirectToRoute("home");

        $participant = $em->getRepository(Participants::class)->find($user->getNoParticipant());
        if(!$participant)
            throw new NotFoundHttpException('Profil not found');

        $profilForm = $this->createForm(RegisterType::class, $participant, array('method' => 'POST'));
        $profilForm->handleRequest($request);
        if($profilForm->isSubmitted() && $profilForm->isValid()){
            $mdp_hashed = $encoder->encodePassword($participant, $participant->getPassword());
            $participant->setMotDePasse($mdp_hashed);
            $em->persist($participant);
            $em->flush();

            $this->addFlash(
                'sucess',
                'Modifications effectuées'
            );

            return $this->redirectToRoute("my_profil");
        }
        return $this->render("participants/my_profil.html.twig",[
            "profilForm" => $profilForm->createView()
        ]);
        
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render("participants/login.html.twig", ['error' => $error, 'lastUsername' => $lastUsername]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response  {
        return $this->redirectToRoute("home");
    }
}
