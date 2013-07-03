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
                if (!$user->getConfirmationToken()) 
                {
                    $tokenGenerator = $this->container->get('fos_user.util.token_generator');
                    $user->setConfirmationToken($tokenGenerator->generateToken());
                }
                // Tuck away email address for check email page
                $sessionData = array
                (
                    'username' => $username,
                    'email'    => $this->getObfuscatedEmail($user),
                    'token'    => mt_rand(1000,9999),
                );
                $request->getSession()->set(static::SESSION_DATA,$sessionData);
                
                // Send the actual email
                // $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        
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
    public function checkEmailAction(Request $request)
    {
        $session = $request->getSession();
        $sessionData = $session->get(static::SESSION_DATA);
        
        $session->remove(static::SESSION_DATA);

        if (!$sessionData) 
        {
            return $this->redirect($this->generateUrl('cerad_account_password_reset_request'));
        }

        $tplData = array();
        $tplData['email']    = $sessionData['email'];
        $tplData['token']    = $sessionData['token'];
        $tplData['username'] = $sessionData['username'];
        return $this->render('@CeradAccount/Password/Reset/check_email.html.twig', $tplData);
    }
    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');
die($username);
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:request.html.'.$this->getEngine(), array('invalid_username' => $username));
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:passwordAlreadyRequested.html.'.$this->getEngine());
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->container->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));
        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_check_email'));
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
