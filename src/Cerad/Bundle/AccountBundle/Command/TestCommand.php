<?php
namespace Cerad\Bundle\AccountBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//  Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use FOS\UserBundle\FOSUserEvents;

use Symfony\Component\EventDispatcher\Event;

class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName       ('cerad:account:test')
            ->setDescription('Some Cerad Account Tests')
             ->addArgument   ('testName', InputArgument::REQUIRED, 'Test to run')
             ->addArgument   ('arg1',     InputArgument::OPTIONAL, 'arg1(optional)')
       ;
    }
    protected function getService($id)     { return $this->getContainer()->get($id); }
    protected function getParameter($name) { return $this->getContainer()->getParameter($name); }
    
   
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $testName = $input->getArgument('testName');
        $arg1     = $input->getArgument('arg1');
        
        switch($testName)
        {
            case 'basic': return $this->executeBasic();
            case 'event': return $this->executeEvent($arg1);
        }
        
        echo sprintf("Unknown test name: %s\n",$testName);
    }
    protected function executeEvent($eventName)
    {
        switch($eventName)
        {
            default:
                $eventKey = FOSUserEvents::REGISTRATION_INITIALIZE;
        }
        
        echo sprintf(">>> %s\n",$eventKey);
        
        $dispatcher = $this->getService('event_dispatcher');
        
        $event = new Event();
        
        $dispatcher->dispatch($eventKey, $event);
        
        echo sprintf("<<< %s\n",$eventKey);

    }
    protected function executeBasic()
    {
        $config = $this->getParameter('cerad_account_config');
        print_r($config);
    }
 }
?>
