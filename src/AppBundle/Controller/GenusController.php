<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 27/11/2017
 * Time: 19:39
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class GenusController extends Controller
{
    /**
     * @Route("/genus/{genusName}")
     */
    public function showAction($genusName): Response
    {
        $templating = $this->container->get('templating');
        $html = $templating->render('genus/show.html.twig', [
            'name' => $genusName,
            'chirulao' => 'Chirulao',
        ]);

        return new Response($html);
    }
}