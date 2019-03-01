<?php

namespace EspaceBundle\Controller;

use blackknight467\StarRatingBundle\Form\RatingType;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use MainBundle\Entity\Avis_espace;
use MainBundle\Entity\Commentaire_espace;
use MainBundle\Entity\Espace;
use MainBundle\Entity\Espace_copy;
use MainBundle\Entity\Photo_espace;
use MainBundle\Entity\promotion;
use MainBundle\Entity\User;
use MainBundle\Form\promotionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EspaceController extends Controller
{
    public function readAction()
    {
        $em= $this->getDoctrine()->getManager();
        $espaces=$em->getRepository("MainBundle:Espace")->findetat();
        return $this->render('@Espace/Espace_admin/espace.html.twig', array(
            "espaces"=>$espaces

        ));
    }

    public function read_modifierAction()
    {
        $em= $this->getDoctrine()->getManager();
        $espaces=$em->getRepository(Espace_copy::class)->findAll();
        return $this->render('EspaceBundle:Espace_admin:espace_modifier_client.html.twig', array(
            "espaces"=>$espaces

        ));
    }
    public function afficherAction()
    {
        return $this->render('@Espace/Espace_client/espace_client.html.twig');
    }
    public function read_confirmerAction()
    {
        $em= $this->getDoctrine()->getManager();
        $espaces=$em->getRepository("MainBundle:Espace")->findetatnon();
        return $this->render('@Espace/Espace_admin/espace_confirmer.html.twig', array(
            "espaces"=>$espaces

        ));
    }
    public function confirmerAction($id)
    {
        $em= $this->getDoctrine()->getManager();
        $espaces=$em->getRepository("MainBundle:Espace")->find($id);
        $espaces->setEtat("1");
        $em->persist($espaces);
        $em->flush();
        return $this->redirectToRoute("espace_confirmer");
    }
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $espace=$em->getRepository("MainBundle:Espace")->find($id);
        $em->remove($espace);
        $em->flush();
        return $this->redirectToRoute("afficher_espace");
    }
    public function supprimer_commentaireAction($id,$espace){
        $em = $this->getDoctrine()->getManager();
        $commentaire=$em->getRepository("MainBundle:Commentaire_espace")->find($id);
        $commen=$em->getRepository(Commentaire_espace::class)->findcom($espace);
        $user= $this->container->get('security.token_storage')->getToken()->getUser();
        $album=$em->getRepository('MainBundle:Photo_espace')->findalbum($espace);
        $espaces=$em->getRepository('MainBundle:Espace')->find($espace);
        $em->remove($commentaire);
        $em->flush();
        return $this->redirectToRoute("info_espace",array('id'=>$espace));
    }
    public function deleteconfAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $espace=$em->getRepository("MainBundle:Espace")->find($id);
        $em->remove($espace);
        $em->flush();
        return $this->redirectToRoute("espace_confirmer");
    }
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $espace=$em->getRepository("MainBundle:Espace")->find($id);


        $form=$this->createFormBuilder($espace)
            ->add('titre',TextType::class)
            ->add('description',TextType::class)
            ->add('adresse',TextType::class)
            ->add('file',FileType::class)

            ->add('Save',SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if (($form->isSubmitted())&&($form->isValid()))
        {   $espace=$form->getData();
            $espace->upload();
            $em->persist($espace);
            $em->flush();
            return $this->redirectToRoute('afficher_espace');
        }
        // Recuperation des donnees
        //Remplir form
        return $this->render('@Espace/Espace_admin/modifier_espace.html.twig', array(
            "form"=>$form->createView()
            // ...
        ));


    }

    public function  confirmer_modificationAction($id)
    {
             $em=$this->getDoctrine()->getManager();
             $em1=$this->getDoctrine()->getManager();
              $espace_copy=$em->getRepository(Espace_copy::class)->find($id);
              $espace=$em->getRepository(Espace::class)->find($espace_copy->getIdEsp());
              $espace->setDescription($espace_copy->getDescription());
              $espace->setTitre($espace_copy->getTitre());
              $em->persist($espace);
              $em1->remove($espace_copy);
              $em1->flush();
              $em->flush();
              return $this->redirectToRoute("espace_modifier_client");

        }
    public function modifierparclientAction(Request $request, $id_esp)
    {     $em=$this->getDoctrine()->getManager();
          $espace_copy = new Espace_copy();
        if($request->isMethod('post')){
           $espace_copy->setDescription($request->get('description'));
           $espace_copy->setTitre($request->get('titre'));
           $espace_copy->setNom($request->get('nom'));
           $espace_copy->setPrenom($request->get('prenom'));
           $espace_copy->setIdEsp($id_esp);
            $em->persist($espace_copy);
            $em->flush();
            return $this->redirectToRoute("offre_espace");
        }
        return $this->redirectToRoute("offre_espace");
    }

    public function modifierespaceAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $espace=$em->getRepository("MainBundle:Espace")->find($id);


        $form=$this->createFormBuilder($espace)
            ->add('titre',TextType::class)
            ->add('description',TextType::class)
            ->add('adresse',TextType::class)
            ->add('file',FileType::class)
            ->add('Save',SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if (($form->isSubmitted())&&($form->isValid()))
        {   $espace=$form->getData();
            $espace->upload();
            $em->persist($espace);
            $em->flush();
            return $this->redirectToRoute('info_espace');
        }
        // Recuperation des donnees
        //Remplir form
        return $this->render('@Espace/Espace_client/modifierespaceinter.html.twig', array(
            "form"=>$form->createView()
            // ...
        ));


    }
    public function RechercheAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $search = $request->query->get('espace');
            $en = $this->getDoctrine()->getManager();
            $espaces = $en->getRepository("MainBundle:Espace")->find($search);
            $serializer = new Serializer(array(new ObjectNormalizer()));
            $data = $serializer->normalize($espaces);
            return new JsonResponse($data);

        }
        return $this->render("@Espace/Espace_admin/espace.html.twig");
    }
    public function createAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $espace= new Espace();
        $user= $this->container->get('security.token_storage')->getToken()->getUser();
        $form=$this->createFormBuilder($espace)
            ->add('titre',TextType::class)
            ->add('description',TextareaType::class)
            ->add('adresse',TextType::class)
            ->add('file',FileType::class)
            ->add('Enregistrer',SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if (($form->isSubmitted())&&($form->isValid()))
        {   $espace=$form->getData();
            $espace->upload();
            $espace->setEtat("0");
            $espace->setNBrating("0");
            $espace->setRating("0");
            $espace->setUser($user);
            $em->persist($espace);
            $em->flush();
            return $this->redirectToRoute("ajouter_album_client",array("id"=>$espace->getId()));
        }
        return $this->render('EspaceBundle:Espace_client:ajouter_espace_client.html.twig',array(
                "form"=>$form->createView()
            )
        );



    }

    public function albumAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $espace=$em->getRepository("MainBundle:Espace")->find($id);
        $album = new Photo_espace();
        $form=$this->createFormBuilder($album)
            ->add('file4',FileType::class)
            ->add('file1',FileType::class)
            ->add('file2',FileType::class)
            ->add('file3',FileType::class)
            ->add('Enregistrer',SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if (($form->isSubmitted())&&($form->isValid()))
        {   $album=$form->getData();
        $album->setEspace($espace);
            $album->upload();
            $em->persist($album);
            $em->flush();
            return $this->redirectToRoute('offre_espace');
        }
        return $this->render('@Espace/Espace_client/ajouter_album_espace.html.twig',array(
                "form"=>$form->createView()
            )
        );



    }
    public function ajouter_commentaireAction(Request $request,$id)
    {

        $em=$this->getDoctrine()->getManager();
        $commentaire= new Commentaire_espace();

        $espaces=$em->getRepository(Espace::class)->find($id);
        $commentaire1=$em->getRepository(Commentaire_espace::class)->findcom($id);
        $user= $this->container->get('security.token_storage')->getToken()->getUser();
       if($request->isMethod('post')){
           $commentaire->setContenu($request->get('contenu'));
           $commentaire->setUser($user);
           $commentaire->setEspace($espaces);
            $commentaire->setDateCommentaire(new \DateTime('now +1hour'));
            $em->persist($commentaire);
            $em->flush();
          return $this->redirectToRoute("info_espace",array("id"=>$id));
        }
            return $this->render('EspaceBundle:Espace_client:info_espace.html.twig', array(
                "espaces"=>$espaces,"commentaire"=>$commentaire1

            ));




    }
    public function Liste_espaceAction()
    {
        $em= $this->getDoctrine()->getManager();
        $user=$this->container->get('security.token_storage')->getToken()->getUser();
        $espaces=$em->getRepository("MainBundle:Espace")->findetat();
        $rating=$em->getRepository("MainBundle:Avis_espace")->findAll();
        return $this->render('EspaceBundle:Espace_client:offre_espace.html.twig', array(
            "espaces"=>$espaces,"user"=>$user,"rating"=>$rating,"rating1"=>$rating

        ));
    }













    public function Liste_promotionAction()
    {
        $em= $this->getDoctrine()->getManager();
        $em1= $this->getDoctrine()->getManager();
        $promotion=$em1->getRepository("MainBundle:promotion")->findAll();

        $user=$this->container->get('security.token_storage')->getToken()->getUser();
        $espaces=$em->getRepository("MainBundle:Espace")->findetat();
        $rating=$em->getRepository("MainBundle:Avis_espace")->findAll();
        var_dump($promotion);
        return $this->render('EspaceBundle:Espace_client:offre_promotion.html.twig', array(
            "promotion"=>$promotion,"espaces"=>$espaces,"user"=>$user,"rating"=>$rating,"rating1"=>$rating

        ));
    }






    public function verifAction()
    {


        $em= $this->getDoctrine()->getManager();


        $rating=$em->getRepository("MainBundle:Avis_espace")->findAll();
        $promotion=$em->getRepository("MainBundle:promotion")->findAll();
        $i=0;
        foreach ($promotion as $a) {
            $i++;
            foreach ($rating as $b) {


                if ($a->getIdespace()->getid() == $b->getEspace()->getid()) {
                    $user = $b->getUser();
                    $message1=$a->getidespace()->gettitre();

                    $message = "l'espace $message1  à une nouvelle promotion , pour plus d'information vérifier votre compte.";
                    $email = \Swift_Message::newInstance()
                        ->setSubject("promotion ")
                        ->setFrom(array('test@idealconstruction.tn' => 'wordfrensh'))
                        ->setTo($user->getEmailCanonical())
                        ->setCharset('UTF-8')
                        ->setContentType('text/html')
                        ->setBody($this->render('EspaceBundle:Espace_client:mail.html.twig', array('user' => $user, 'message' => $message)));
                    $this->get('mailer')->send($email);



                    break;
                }

            }
        }
        return $this->render('EspaceBundle:Espace_client:test.html.twig');
    }






    public function infoespaceAction(Request $request,$id,Request $request1)
    {


        $modele1= new  promotion();
        $form1= $this->createForm(promotionType::class,$modele1);
        $form1->handleRequest($request1);
        if($form1->isValid()){
            $em1= $this->getDoctrine()->getManager();
            $em1->persist($modele1);
            $em1->flush();

        }

        $em= $this->getDoctrine()->getManager();
        $espaces=$em->getRepository("MainBundle:Espace")->find($id);
        // var_dump($espaces);
        $user=$this->container->get('security.token_storage')->getToken()->getUser();
        $commentaire=$em->getRepository(Commentaire_espace::class)->findcom($id);
        $album=$em->getRepository("MainBundle:Photo_espace")->findalbum($id);
        $rating=$em->getRepository("MainBundle:Avis_espace")->findrati($id);
        $avis=new Avis_espace();
        $form=$this->createFormBuilder($avis)
            ->add('rating',RatingType::class, [
                //...
                'stars' => 5,
                //...
            ])
            ->add('add',SubmitType::class)
        ->getForm();
        $form->handleRequest($request);
        if (($form->isSubmitted())&&($form->isValid()))
        {
            $avis=$form->getData();
            $avis->setNbrating(1);
            $avis->setUser($user);
            $avis->setEspace($espaces);
            $em->persist($avis);
            $em->flush();

           // $this->verifAction();




            return $this->render('EspaceBundle:Espace_client:info_espace.html.twig', array(
                "espaces"=>$espaces,"commentaire"=>$commentaire,"album"=>$album,"user"=>$user,"form"=>$form->createView(),"rati"=>$rating,"form123"=>$form1->createView()));
        }


        return $this->render('EspaceBundle:Espace_client:info_espace.html.twig', array(
            "espaces"=>$espaces,"commentaire"=>$commentaire,"album"=>$album,"user"=>$user,"form"=>$form->createView(),"rati"=>$rating,"form123"=>$form1->createView()

        ));

    }





/*


    public function ajaxSnippetImageSendAction(Request $request)
    {
        $em = $this->container->get("doctrine.orm.default_entity_manager");
        $document = new Photo_espace();
        $document->setEspace($id);
        $document->upload();
        $em->persist($document);
        $em->flush();

        //infos sur le document envoyé
        //var_dump($request->files->get('file'));die;
        return new JsonResponse(array('success' => true));
    }*/
    public function statAction()
    {
        $m = $this->getDoctrine()->getManager();

        $avis = $m->getRepository('MainBundle:Espace')->findAll();
        $valeurs= array();
        $ids= array();
        foreach ($avis as $a)
        {  if($a->getRating()>0) {
            $as = $a->getRating() / $a->getNbrating();
        }else{
            $as= $a->getRating();
        }
            array_push($valeurs, $as);
            array_push($ids, $a->getTitre());
        }
        $bar = new BarChart();
        $bar->getData()->setArrayToDataTable([
            $ids,$valeurs
        ]);
        $bar->getOptions()->setTitle('Rating des espaces');
        $bar->getOptions()->getHAxis()->setTitle('Valeurs');
        $bar->getOptions()->getHAxis()->setMinValue(1);
        $bar->getOptions()->getHAxis()->setMaxValue(4);
        $bar->getOptions()->getVAxis()->setTitle('Espace');
        $bar->getOptions()->setWidth(900);
        $bar->getOptions()->setHeight(600);
        return $this->render('@Espace/Espace_admin/stat.html.twig', array('barchart'=>$bar));
    }

    public function mapAction($id)
    {

        $m=$this->getDoctrine()->getManager();
        $map=$m->getRepository('MainBundle:Espace')->find($id);
        //var_dump($map);
        $lon=$map->getLongitude();
        $lat=$map->getLatitude();
       // var_dump($lon);
       // var_dump($lat);

       /* $user = $this->getUser();

        $message = "Votre rendez-vous à été enregisté, pour plus d'information vérifier votre compte.";
        $email = \Swift_Message::newInstance()
            ->setSubject("Création d'un rendez-vous")
            ->setFrom(array('test@idealconstruction.tn'=>'wordfrensh'))
            ->setTo($user->getEmailCanonical())
            ->setCharset('UTF-8')
            ->setContentType('text/html')
            ->setBody($this->render('EspaceBundle:Espace_client:mail.html.twig',array('user'=>$user,'message'=>$message)))
        ;
        $this->get('mailer')->send($email);
       */

        return $this->render('EspaceBundle:Espace_client:map.html.twig', array('long'=>$lon,'lat'=>$lat));
    }


}
