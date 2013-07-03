<?php
namespace Cerad\Bundle\AccountBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\UserInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PasswordResetController extends Controller
{
    const SESSION_DATA = 'cerad_account_password_reset';

    public function requestAction(Request $request)
    {
        // Majic to get any previous errors
        $authInfo = $this->get('cerad_account.authentication_information');
        $info = $authInfo->get($request);
        
        $item = array(
            'error'    => null,
            'username' => $info['lastUsername'],
        );
        $form = $this->createForm($this->get('cerad_account.password_reset.formtype'),$item);
        $form->handleRequest($request);

        if ($form->isValid()) 
        { 
            // Suppose a data transformer could be used here?
            $item = $form->getData();
            $username = $item['username'];
            
            $userProvider = $this->get('cerad_account.user_provider');
            $user = $userProvider->findUser($username);
            if (!$user)
            {
                // TODO: add error to form
                $item['error'] = 'User not found.';
            }
            else
            {
                // Should check to see how long since last request
                
                // Set the confirmation token
                if (!$user->getConfirmationToken() || 1) 
                {
                    $tokenGenerator = $this->container->get('fos_user.util.token_generator');
                    $user->setConfirmationToken($tokenGenerator->generateToken());
                    $user->setConfirmationToken(mt_rand(1000,9999));
                }
                // Tuck away email address for check email page
                $sessionData = array
                (
                    'username' => $username,
                    'email'    => $this->getObfuscatedEmail($user),
                    'token'    => $user->getConfirmationToken(),
                );
                $request->getSession()->set(static::SESSION_DATA,$sessionData);
                
                // Send the actual email
                // $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
                $this->sendEmail($user);
                
                // Persist the updated user
                $user->setPasswordRequestedAt(new \DateTime());
                $this->container->get('cerad_account.user_manager')->updateUser($user);

                return $this->redirect($this->generateUrl('cerad_account_password_reset_check_email'));
            }
        }
        // Render
        $tplData = array();
        $tplData['passwordResetError'] = $item['error'];
        $tplData['passwordResetForm' ] = $form->createView();
        return $this->render('@CeradAccount/Password/Reset/request.html.twig', $tplData);
    }
    protected function sendEmail($user)
    {
        $fromName =  'Zayso Password Reset';
        $fromEmail = 'noreply@zayso.org';
        
        $adminName =  'Art Hundiak';
        $adminEmail = 'ahundiak@gmail.com';
       
        $userName  = $user->getName();
        $userEmail = $user->getEmail();
        
        $tplData = array();
        $tplData['user']   = $user;
        $tplData['prefix'] = 'Zayso';
        
        $body    = $this->renderView('@CeradAccount/Password/Reset/email_body.html.twig',  $tplData);
        $subject = $this->renderView('@CeradAccount/Password/Reset/email_subject.txt.twig',$tplData);
        
        
         // This goes to the assignor
        $message = \Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setBody($body);
        $message->setFrom(array($fromEmail  => $fromName ));
        $message->setBcc (array($adminEmail => $adminName));
        $message->setTo  (array($userEmail  => $userName ));

        $this->get('mailer')->send($message);
       
    }
    public function checkEmailAction(Request $request)
    {
        $session     = $request->getSession();
        $sessionData = $session->get(static::SESSION_DATA);
        $sessionData['error'] = null;
        
      //$session->remove(static::SESSION_DATA);

        if (!$sessionData) 
        {
            return $this->redirect($this->generateUrl('cerad_account_password_reset_request'));
        }
        $item = array('token' => null);
        $form = $this->createForm($this->get('cerad_account.token.formtype'),$item);
        $form->handleRequest($request);

        if ($form->isValid()) 
        { 
            $item = $form->getData();
            if ($item['token'] != $sessionData['token']) $sessionData['error'] = 'Invalid token entered, try again.';
            else
            {
                $sessionData['error'] = 'Tokens match';
                
                // So how exactly do we implement a password reset?  A session flag perhaps?
                
                //$session->remove(static::SESSION_DATA);
            }
            
        }
        $tplData = array();
        $tplData['error']     = $sessionData['error'];
        $tplData['email']     = $sessionData['email'];
        $tplData['token']     = $sessionData['token'];
        $tplData['username']  = $sessionData['username'];
        $tplData['tokenForm'] = $form->createView();
        return $this->render('@CeradAccount/Password/Reset/check_email.html.twig', $tplData);
    }
    /**
     * Get the truncated email displayed when requesting the resetting.
     *
     * The default implementation only keeps the part following @ in the address.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     *
     * @return string
     */
    protected function getObfuscatedEmail($user)
    {
        $email = $user->getEmail();
        if (false !== $pos = strpos($email, '@')) {
            $email = '...' . substr($email, $pos);
        }

        return $email;
    }
    
}
?>
