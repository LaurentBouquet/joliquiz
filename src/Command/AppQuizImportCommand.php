<?php

namespace App\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Category;

class AppQuizImportCommand extends DoctrineCommand
{
    protected static $defaultName = 'app:quiz-import';

    protected $container;
    //$this->container = $container;

    protected function configure()
    {
        $this
            ->setDescription('Imports quizzes from another software')
            ->addArgument('file', InputArgument::REQUIRED, 'Quiz data file')
            ->addOption('format', '-f', InputOption::VALUE_REQUIRED, 'Quiz data file format')
            ->addOption('category', '-c', InputOption::VALUE_OPTIONAL, 'Category to class imported questions')
            ->setHelp(<<<EOT
The <info>%command.name%</info> imports quizzes from another software:

  <info>php %command.full_name%</info>

For example, to import 'certificationy' data from '/home/user/architecture.yml' file:

  <info>php %command.full_name% --format certificationy --category 'Symfony 3' /home/user/architecture.yml</info>

EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');
        $format = $input->getOption('format');
        $category = $input->getOption('category');

        if ($file) {
            $io->note(sprintf('Import data from file: "%s"', $file));
        }
        else {
            throw new \LogicException(sprintf("The file must be provided. Pass --help to see options."));
        }

        if ($format) {
            $io->note(sprintf('Import format: "%s"', $format));
        }
        else {
            throw new \LogicException(sprintf("The format must be provided. Pass --help to see options."));
        }

        if ($category) {
            $io->note(sprintf('Class imported questions into category: "%s"', $category));
        }

        if (!$io->confirm('<question>Careful, data will be inserted in the database. Do you want to continue y/N ?</question>', false)) {
            return;
        }

        switch ($format) {
            case 'certificationy':
                $result = $this->importCertificationy($io, $format, $file, $category);
                break;

            default:
                $io->error('Not yet implemented');
                break;
        }

        if ($result) {
            $io->success(sprintf('Import from "%s" file finished!', $file));
        }
    }

    protected function importCertificationy($io, $format, $file, $secondCategory=null) {
        /** @var $doctrine \Doctrine\Common\Persistence\ManagerRegistry */
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $data = Yaml::parseFile($file);
        $questions = $data['questions'];
        $category = $data['category'];

        $joliquizCategory = new Category($category);
        $em->persist($joliquizCategory);
        if ($secondCategory) {
            $joliquizSecondCategory = new Category($secondCategory);
            $em->persist($joliquizSecondCategory);
        }

        foreach ($questions as $question) {

            $answers = array();

            $joliquizQuestion = new Question();
            $joliquizQuestion->addCategory($joliquizCategory);
            if ($secondCategory) {
                $joliquizQuestion->addCategory($joliquizSecondCategory);
            }
            $joliquizQuestion->setText($question['question']);

            foreach ($question['answers'] as $answer) {
                $joliquizAnswer = new Answer();
                $joliquizAnswer->setText($answer['value']);
                $joliquizAnswer->setCorrect($answer['correct']);
                $em->persist($joliquizAnswer);
                $joliquizQuestion->addAnswer($joliquizAnswer);
            }

            /*
            if (!isset($question['shuffle']) || true === $item['shuffle']) {
                shuffle($answers);
            }
            $help = isset($item['help']) ? $item['help']: null;
            //$questions[] = new Question($item['question'], $item['category'], $answers, $help);
            */

            $em->persist($joliquizQuestion);
        }

        $em->flush();

        if ($secondCategory) {
            $io->note(sprintf('Importing %s question(s) in category "%s" and category "%s".', count($questions), $category, $secondCategory));
        }
        else {
            $io->note(sprintf('Importing %s question(s) in category "%s".', count($questions), $category));
        }

        return true;
    }
}
