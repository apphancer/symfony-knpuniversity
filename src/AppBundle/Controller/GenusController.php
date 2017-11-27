<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 27/11/2017
 * Time: 19:39
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;


class GenusController
{
    /**
     * @Route("/genus/{genusName}")
     */
    public function showAction($genusName): Response
    {
        return new Response('The genus: ' . $genusName);
    }
}