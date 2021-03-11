<?php

namespace App\Controller;

use App\Entity\Sites;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Participants;
use App\Form\RegisterType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Finder\Finder;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;


class ParticipantController extends AbstractController
{
    /**
     * @Route("/register", name="participant_register")
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder ): Response
    {

        $participant = new Participants();
        $registerForm = $this->createForm(RegisterType::class, $participant, array('method' => 'POST'));
        $registerForm->remove('profilPicture');

        $registerForm->handleRequest($request);
        if($registerForm->isSubmitted() && $registerForm->isValid()){
            $participant->setAdministrateur(false);
            $participant->setActif(true);
            //hashage mdp
            $mdp_hashed = $encoder->encodePassword($participant, $participant->getPassword());
            $participant->setMotDePasse($mdp_hashed);
            try {
                $em->persist($participant);
                $em->flush();
                $this->addFlash(
                    'success',
                    ' '.$participant->getPseudo().' à été ajouté !'
                );
           }
            catch (UniqueConstraintViolationException $e) {
               $this->getDoctrine()->resetManager();
                var_dump($e->getMessage());
                $this->addFlash(
                    'error',
                    'Le participant : '.$participant->getPseudo() . ' existe déjà.'
                );
            }

            return $this->redirectToRoute("home");
        }
      $arrResult = array();

        if(isset($_FILES['file']['name'])){
            $file_extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            if ($file_extension != "csv") {
                $this->addFlash(
                    'error',
                    'Veuillez insérer un fichier au format .csv',
                );
                return $this->redirectToRoute("home");
            }else if (($handle = fopen($_FILES['file']['tmp_name'], 'r')) !== FALSE)
            {

                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                        $participant = new Participants();
                        $repo = $this->getDoctrine()->getRepository(Sites::class);
                        $site = $repo->find(1);
                        $participant->setPseudo($data[0]);
                        $participant->setNom($data[1]);
                        $participant->setPrenom($data[2]);
                        $participant->setTelephone($data[3]);
                        $participant->setMail($data[4]);
                        $participant->setMotDePasse($data[5]);
                        $mdp_hashed = $encoder->encodePassword($participant, $participant->getMotDePasse());
                        $participant->setMotDePasse($mdp_hashed);
                        //par défaut un participant n'est pas administrateur
                        $participant->setAdministrateur(0);
                        //par défaut un participant est actif
                        $participant->setActif(1);
                        //par défaut on attribut le site no1 au participant
                        $participant->setSitesNoSite($site);
                        try {
                            $em->persist($participant);
                            $em->flush();
                            $this->addFlash(
                                'success',
                                ' '.$participant->getPseudo().' à été ajouté !'
                            );
                        }catch(UniqueConstraintViolationException $e){
                            $em = $this->getDoctrine()->resetManager();
                            $this->addFlash(
                                'error',
                                'Le participant ' .$participant->getPseudo() . ' existe déjà en base.'
                            );
                        }


                    }
                    fclose($handle);

                    return $this->redirectToRoute("home");
            }

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

            
            // le téléchargement d'une photo de profil n'est pas obligatoire
            // donc on lance le process d'update' seulement si le fichier est fourni
            $ppFile = $profilForm['profilPicture']->getData();
            if ($ppFile) {
                //on force le format png pour écraser la photo de profil précédente
                $newFilename = $participant->getNoParticipant().'-profil-picture.png'; 
                $ppFile->move(
                       $this->getParameter('profil_pictures_directory'),
                       $newFilename
                   );                
                $participant->setProfilPicture($newFilename);
            }
            
            $em->persist($participant);
            $em->flush();

            $this->addFlash(
                'success',
                'Modifications effectuées'
            );
            //flash fonctionne que via redirection
            return $this->redirectToRoute("my_profil");
        }

        return $this->render("participants/my_profil.html.twig",[
            "ppPath" => $participant->getProfilPicture(),
            "profilForm" => $profilForm->createView()
        ]);
        
    }

    /**
     * @Route("/profil/{id}", name="profil", requirements={"id": "\d+"})
     */
    public function profil(int $id, EntityManagerInterface $em) : Response
    {
        $participant = $em->getRepository(Participants::class)->find($id);
        if(!$participant)
            throw new NotFoundHttpException('Participant not found');
        return $this->render("participants/profil.html.twig", ['participant' => $participant]);
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
