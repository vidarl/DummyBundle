<?php

namespace Vidarl\DummyBundle\Controller;

use eZ\Publish\Core\MVC\Symfony\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class EzMVCController extends Controller
{
    /**
     * Route("/simpleStuff")
     */
    public function simpleStuffAction()
    {
        return new Response('simpleStuffAction content');
        return $this->render('VidarlDummyBundle:Default:index.html.twig');
    }


}