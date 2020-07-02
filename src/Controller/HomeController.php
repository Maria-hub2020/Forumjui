<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Theme;
use App\Form\PostType;
use App\Entity\Fichier;
use App\Form\FichierType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function home()
    {
        return $this->redirectToRoute('app_login');
        }
    

 /**
     * @Route("/post/{id}", name="post")
     */
    public function post(Request $request,$id){
        $session = $request->getSession();

        $repository = $this->getDoctrine()->getRepository(Theme::class);
        $theme = $repository->find($id);

        $repository=$this->getDoctrine()->getRepository(Post::class);
        $listePost=$repository->findByTheme($theme);


        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(($session->get('_security.last_username'))!= null){
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findByEmail($session->get('_security.last_username'));
            $post = $form->getData();
            $post->setUser($user[0]);
            $post->setTheme($theme);
            $post->setDate(new \DateTime(date("d-m-Y")));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
      
            return $this->redirectToRoute('post',['id' => $id]);
        }
        else {
            return $this->redirectToRoute('app_login');
        }
    }
    return $this->render('home/discussion.html.twig', [
    'form' => $form->createView(),'posts' => $listePost
    ]);
    }



    /**
     * @Route("/fichier/{id}" , name="fichier")
    */
    public function fichier(Request $request,$id){
        $repository = $this->getDoctrine()->getRepository(Theme::class);
        $theme = $repository->find($id);

        $fichier=new Fichier();
        $form=$this->createForm(FichierType::class,$fichier);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $fichier = $form->getData();
            $fichier->setTheme($theme);



            $em = $this->getDoctrine()->getManager();
            $em->persist($fichier);
            $em->flush();
            return $this->redirectToRoute('home');
        }


        return $this->render('home/newfichier.html.twig', [
            'form'=>$form->createView()
        ]);
    }
/**
     * @Route("/adminFichier", name="adminFichier")
     */
    public function adminFichier()
    {
        $repository=$this->getDoctrine()->getRepository(Fichier::class);
        $listeFichiers=$repository->findAll();
        
        return $this->render('home/listeFichiers.html.twig', [
            'listeFichiers' => $listeFichiers,
        ]);
    }

 /**
     * @Route("/fichier/supprfichier/{id}" , name="supprfichier")
    */
    public function supprfichier(Request $request,$id){
        $repository=$this->getDoctrine()->getRepository(Fichier::class);
        $Fichier=$repository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($Fichier);
        $entityManager->flush();
        return $this->redirectToRoute('adminFichier');        
}
/**
     * @Route("/fichier/editfichier/{id}" , name="editfichier")
    */
    public function editfichier(Request $request,$id){

        $repository=$this->getDoctrine()->getRepository(Fichier::class);
        $Fichier=$repository->find($id);
        $form=$this->createForm(FichierType::class,$Fichier);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($Fichier);
            $em->flush();
            return $this->redirectToRoute('adminFichier');
        }


        return $this->render('home/newFichier.html.twig', [
            'form'=>$form->createView()
        ]);
    }
    /**
    * @Route("/{id}/file", name="paquet_file")
    */
    public function downloadImageAction(Fichier $fichier, DownloadHandler $downloadHandler): Response
    {
        return $downloadHandler->downloadObject($fichier, $fileField = 'imageFile');
    }
}





