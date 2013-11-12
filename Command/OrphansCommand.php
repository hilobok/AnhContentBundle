<?php

namespace Anh\ContentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * CLI command for listing/deleting orphaned assets
 */
class OrphansCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('anh:content:orphans')
            ->setDescription('List orphaned assets')
            ->addOption('delete', null, InputOption::VALUE_NONE, 'Delete orphans')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $pm = $container->get('anh_content.manager.paper');
        $am = $container->get('anh_content.asset.manager');

        $assets = array();
        foreach ($pm->findAll() as $paper) {
            foreach ($paper->getAssets() as $asset) {
                $assets[] = $asset['fileName'];
            }
        }

        $finder = Finder::create()->files()->in(
            $container->getParameter('anh_content.assets_dir')
        );

        $files = array_map(function($file) {
            return $file->getFilename();
        }, iterator_to_array($finder, false));

        $orphans = array_diff($files, $assets);

        $output->writeln(sprintf('%d orphan(s) found.', count($orphans)));

        $doDelete = $input->getOption('delete');

        foreach ($orphans as $orphan) {
            $output->writeln($orphan);
            if ($doDelete) {
                $am->remove($orphan);
            }
        }
    }
}
