<?php

namespace Vidarl\DummyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('VidarlDummyBundle:Default:index.html.twig');
    }

    /**
     * @Route("/one_xlocationid")
     */
    public function oneXLocatonIdAction()
    {
        $response = $this->render('VidarlDummyBundle:Default:index.html.twig');
        $response->headers->set('X-Location-Id', 'location-42');
        return $response;
    }

    /**
     * @Route("two_xlocationids")
     */
    public function twoXLocatonIdsAction()
    {
        $response = $this->render('VidarlDummyBundle:Default:index.html.twig');
        $response->headers->set('X-Location-Id', 'location-42,location-43');
        $response->headers->set('xkey', 'foobar');
        return $response;
    }

}
