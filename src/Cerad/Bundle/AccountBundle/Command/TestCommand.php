<?php
namespace Cerad\Bundle\AccountBundle\Command;

use Symfony\Component\Yaml\Yaml;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//  Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName       ('cerad:account:test')
            ->setDescription('Some Cerad Account Tests')
             ->addArgument   ('testName', InputArgument::REQUIRED, 'Test to run')
       ;
    }
    protected function getService($id)     { return $this->getContainer()->get($id); }
    protected function getParameter($name) { return $this->getContainer()->getParameter($name); }
    
   
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $testName = $input->getArgument('testName');
        
        switch($testName)
        {
            case 'basic': return $this->executeBasic();
        }
        
        echo sprintf("Unknown test name: %s\n",$testName);
    }
    protected function executeBasic()
    {
        $config = $this->getParameter('cerad_account_config');
        print_r($config);
    }
 }
?>
