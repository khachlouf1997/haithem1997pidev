<?php

namespace EspaceBundle\Controller;

use MainBundle\Entity\promotion;
use MainBundle\Form\promotionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PromotionController extends Controller
{
    public function ajouterAction(Request $request1)
    {
        $modele1= new  promotion();
        $form1= $this->createForm(promotionType::class,$modele1);
        $form1->handleRequest($request1);
        if($form1->isValid()){
            $em1= $this->getDoctrine()->getManager();
            $em1->persist($modele1);
            $em1->flush();
            return $this->redirectToRoute("ajout");
        }
        return $this->render("@Espace/Espace_client/ajout_promotion.html.twig",array('form123'=>$form1->createView()));
    }

    public function deleteAction($id)
    {
        $em2=$this->getDoctrine()->getManager();
        $modele2=$em2->getRepository(promotion::class)->findBy(array('idespace'=>$id));

        $em2->remove($modele2[1]);
        $em2->flush();
        return $this->redirectToRoute("info_espace",array("id"=>$id));
    }


    public function updateAction($id, Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $modele=$em->getRepository(promotion::class)->find($id);
        $form=$this->createForm(promotionType::class,$modele);
        $form=$form->handleRequest($request);
        if($form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("affiche");}
        return $this->render('@Espace/Espace_client/ajout_promotion.html.twig',array('form'=>$form->createView()));
    }




    public function afficherAction()
    {
        $modeles=$this->getDoctrine()
            ->getRepository(promotion::class)
            ->findAll();

        return $this->render('@Espace/Espace_client/afficher_promotion.html.twig',array("m"=>$modeles));
    }










    }
