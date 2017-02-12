<?php

namespace JV\BlogBundle\Controller;

use JV\BlogBundle\Entity\Article;
use JV\BlogBundle\Event\MessagePostEvent;
use JV\BlogBundle\Event\PlatformEvents;
use JV\BlogBundle\Form\AdvertEditType;
use JV\BlogBundle\Form\AdvertType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleController extends Controller
{
  public function indexAction($page)
  {
    if ($page < 1) {
      throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
    }

    $nbPerPage = 5;

    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('JVBlogBundle:Article')
      ->getAdverts($page, $nbPerPage)
    ;

    $nbPages = ceil(count($listArticles) / $nbPerPage);

    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    return $this->render('JVBlogBundle:Article:index.html.twig', array(
      '$listArticles' => $listArticles,
      'nbPages'     => $nbPages,
      'page'        => $page,
    ));
  }

  public function viewAction(Article $article)
  {
    $em = $this->getDoctrine()->getManager();

    $listApplications = $em
      ->getRepository('JVBlogBundle:Application')
      ->findBy(array('article' => $article))
    ;

    $listArticlesTags = $em
      ->getRepository('JVBlogBundle: ArticleTags')
      ->findBy(array('article' => $article))
    ;

    return $this->render('JVBlogBundle:Article:view.html.twig', array(
      'article'           => $article,
      'listApplications' => $listApplications,
      'listArticlesTags' => $listArticlesTags,
    ));
  }

  /**
   * @Security("has_role('ROLE_AUTEUR')")
   */
  public function addAction(Request $request)
  {

    $article = new Article();
    $form = $this->get('form.factory')->create(ArticleType::class, $article);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $event = new MessagePostEvent($article->getContent(), $article->getUser());

      $this->get('event_dispatcher')->dispatch(PlatformEvents::POST_MESSAGE, $event);

      $article->setContent($event->getMessage());

      $em = $this->getDoctrine()->getManager();
      $em->persist($article);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Article bien enregistrée.');

      return $this->redirectToRoute('jv_blog_view', array('id' => $article->getId()));
    }

    return $this->render('JVBlogBundle:Article:add.html.twig', array(
      'form' => $form->createView(),
    ));
  }

  public function editAction(Article $article, Request $request)
  {
    $form = $this->get('form.factory')->create(AdvertEditType::class, $article);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Article bien modifiée.');

      return $this->redirectToRoute('jv_blog_view', array('id' => $article->getId()));
    }

    return $this->render('JVBlogBundle:Article:edit.html.twig', array(
      'advert' => $article,
      'form'   => $form->createView(),
    ));
  }

  public function deleteAction(Article $article, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $form = $this->get('form.factory')->create();

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em->remove($article);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "L'article a bien été supprimée.");

      return $this->redirectToRoute('jv_blog_home');
    }

    return $this->render('JVBlogBundle:Article:delete.html.twig', array(
      'advert' => $article,
      'form'   => $form->createView(),
    ));
  }

  public function menuAction($limit)
  {
    $em = $this->getDoctrine()->getManager();

    $listArticles = $em->getRepository('JVBlogBundle:Article')->findBy(
      array(),
      array('date' => 'desc'),
      $limit,
      0
    );

    return $this->render('JVBlogBundle:Article:menu.html.twig', array(
      'listArticles' => $listArticles
    ));
  }

  /**
   * @ParamConverter("json")
   */
  public function ParamConverterAction($json)
  {
    return new Response(print_r($json, true));
  }
}
