<?php
namespace Cerad\Bundle\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class MainController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->redirect($this->generateUrl('cerad_account_welcome'));
    }
    public function welcomeAction(Request $request)
    {
        $tplData = array();
        return $this->render('@CeradAccount/welcome.html.twig', $tplData);        
    }
}
?>
