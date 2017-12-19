<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 19.12.17
 * Time: 13:46
 */

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LuckyController extends Controller
{
    /**
     * @Template()
     * @Route("/lucky/number")
     * @return array
     */
    public function number()
    {
        $number = mt_rand(0, 100);
        return ['number'=>$number];
        //return $this->render('lucky/number.html.twig', ['number'=>$number]);
    }
}