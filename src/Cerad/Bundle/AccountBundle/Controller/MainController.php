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
    protected function isAdmin()
    {
        return $this->container->get('security.context')->isGranted('ROLE_ADMIN'); 
    }
    protected function isUser()
    {
        return $this->container->get('security.context')->isGranted('ROLE_USER'); 
    }
    protected function redirectToWelcome()
    {
        return $this->redirect($this->generateUrl('cerad_account_welcome'));
    }
    public function homeAction(Request $request)
    {
        if (!$this->isUser()) return $this->redirectToWelcome();
        
        $tplData = array();
        return $this->render('@CeradAccount/home.html.twig', $tplData);        
    }
}
?>
