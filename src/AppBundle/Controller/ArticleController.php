<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleController extends Controller
{
    /**
     * @Route("/", name="article_list")
     */
    public function listAction(Request $request)
    {
        $articles = $this->getDoctrine()
            ->getRepository('AppBundle:Article')
            ->findAll();
        return $this->render('article/index.html.twig', array(
            'articles' => $articles
        ));
    }

    /**
     * @Route("/article/create", name="article_create")
     */
    public function createAction(Request $request)
    {
        $article = new Article;
        $form = $this->createFormBuilder($article)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px;')))
            ->add('author', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px;')))
            ->add('content', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px;')))
            ->add('save', SubmitType::class, array('label' => 'Create Article', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px;')))
            ->getForm();
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $name = $form['name']->getData();
            $author = $form['author']->getData();
            $content = $form['content']->getdata();

            $create_date = new\DateTime('now');

            $article->setName($name);
            $article->setAuthor($author);
            $article->setCreateDate($create_date);
            $article->setContent($content);

            $em = $this->getDoctrine()->getManager();

            $em->persist($article);
            $em->flush();

            $this->addFlash(
                'notice',
                'Article Added'
            );

            return $this->RedirectToRoute('article_list');
        }

        return $this->render('article/create.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/article/edit/{id}", name="article_edit")
     */
    public function editAction($id, Request $request)
    {
        $article = $this->getDoctrine()
            ->getRepository('AppBundle:Article')
            ->find($id);
        
        $article->setName($article->getName());
        $article->setAuthor($article->getAuthor());
        $article->setContent($article->getContent());

        $form = $this->createFormBuilder($article)            
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('author', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px')))
            ->add('content', TextareaType::class, array('attr' => array('class' => 'form-control', 'style'=> 'margin-bottom: 15px')))
            ->add('save', SubmitType::class, array('label' => 'Update Article', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $name = $form['name']->getData();
            $author = $form['author']->getData();
            $content = $form['content']->getData();

            $em = $this->getDoctrine()->getManager();
            $article = $em->getRepository('AppBundle:Article')->find($id);

            $article->setName($name);
            $article->setAuthor($author);
            $article->setContent($content);

            $em->flush();

            $this->addFlash(
                'notice',
                'Article Updated'
            );

            return $this->redirectToRoute('article_list');
        }


        return $this->render('article/edit.html.twig', array(
            'article' => $article,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/article/details/{id}", name="article_details")
     */
    public function detailsAction($id)
    {
        $article = $this->getDoctrine()
            ->getRepository('AppBundle:Article')
            ->find($id);
        return $this->render('article/details.html.twig', array(
            'article' => $article
        ));
    }

    /**
     * @Route("/article/delete/{id}", name="article_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('AppBundle:Article')->find($id);

        $em->remove($article);
        $em->flush();

        $this->addFlash(
            'notice',
            'Article Removed'
        );

        return $this->redirectToRoute('article_list');
    }
}
