<?php

namespace Anh\ContentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class OrphansCommand extends ContainerAwareCommand
{
    private $connection;

    protected function configure()
    {
        $this
            ->setName('content:orphans')
            ->setDescription('List orphaned assets')
            ->addOption('delete', null, InputOption::VALUE_NONE, 'Delete orphans')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dm = $container->get('anh_content.manager.paper');
        $am = $container->get('anh_content.asset.manager');
        $assetsDir = $container->getParameter('anh_content.assets_dir');

        $assets = array();
        foreach ($dm->findAll() as $paper) {
            foreach ($paper->getAssets() as $asset) {
                $assets[] = $asset['fileName'];
            }
        }

        $finder = Finder::create()->files()->in($assetsDir);

        $files = array_map(function($file) {
            return $file->getFilename();
        }, iterator_to_array($finder, false));

        $orphans = array_diff($files, $assets);

        $output->writeln(sprintf('%d orphan(s) found.', count($orphans)));

        foreach ($orphans as $orphan) {
            $output->writeln($orphan);

            if ($input->getOption('delete')) {
                $am->remove($orphan);
            }
        }
    }
}
