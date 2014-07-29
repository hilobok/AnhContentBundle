<?php

namespace Anh\ContentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * CLI command for reparsing papers
 */
class ReparseCommand extends ContainerAwareCommand
{
    const DEFAULT_LIMIT = 100;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('anh:content:reparse')
            ->setDescription('Reparse papers')
            ->addArgument('criteria', InputArgument::OPTIONAL, 'Criteria in json format')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL,
                sprintf('Number of papers per flush (%d by default).', self::DEFAULT_LIMIT)
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '-1');

        $criteria = (array) json_decode($input->getArgument('criteria'), true);

        $helper = $this->getHelper('question');

        if (empty($criteria)) {
            $question = new ConfirmationQuestion('<question>Reparse ALL papers?</question> [y/N] ', false);

            if (!$helper->ask($input, $output, $question)) {
                return;
            }
        } else {
            $output->writeln(
                sprintf('<info>Criteria:</info> %s', $this->dumpArray($criteria))
            );

            $question = new ConfirmationQuestion('<question>Continue with this criteria?</question> [Y/n] ', true);

            if (!$helper->ask($input, $output, $question)) {
                return;
            }
        }

        $container = $this->getContainer();
        $repository = $container->get('anh_content.paper.repository');
        $entityManager = $container->get('anh_content.paper.manager')->getManager();

        $limit = $input->getOption('limit') ?: self::DEFAULT_LIMIT;
        $offset = 0;

        while ($papers = $repository->fetch($criteria, null, $limit, $offset)) {
            $output->writeln(
                sprintf('Fetched <info>%d</info> paper(s).', count($papers))
            );

            foreach ($papers as $entity) {
                $output->writeln(
                    sprintf("Reparse entity <info>%d</info>: '%s'", $entity->getId(), $entity->getTitle())
                );

                $entityManager->getUnitOfWork()
                    ->setOriginalEntityProperty(spl_object_hash($entity), 'markup', '__suppose there will never be such value__')
                ;
            }

            $output->writeln('Flushing changes...');
            $entityManager->flush();
            $entityManager->clear();

            $offset += $limit;
        };
    }

    protected function dumpArray(array $array)
    {
        $lines = explode("\n", print_r($array, true));
        unset($lines[0], $lines[1], $lines[count($lines)]);

        $lines = array_filter(
            array_map(
                function($value) { return trim($value); },
                $lines
            )
        );

        return implode(', ', $lines);
    }
}
