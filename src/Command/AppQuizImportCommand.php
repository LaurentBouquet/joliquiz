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
            ->setHelp(<<<EOT
The <info>%command.name%</info> imports quizzes from another software:

  <info>php %command.full_name%</info>

For example, to import 'certificationy' data from '/home/user/architecture.yml' file:

  <info>php %command.full_name% --format=certificationy /home/user/architecture.yml</info>

EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');
        $format = $input->getOption('format');

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

        if (!$io->confirm('<question>Careful, data will be inserted in the database. Do you want to continue y/N ?</question>', false)) {
            return;
        }

        switch ($format) {
            case 'certificationy':
                $result = $this->importCertificationy($format, $file);
                break;

            default:
                $io->error('Not yet implemented');
                break;
        }

        if ($result) {
            $io->success(sprintf('Import "%s" format "%s" file finished!', $format, $file));
        }
    }

    protected function importCertificationy($format, $file) {
        /** @var $doctrine \Doctrine\Common\Persistence\ManagerRegistry */
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $data = Yaml::parseFile($file);
        $number = count($data);
        $questions = $data['questions'];
        $category = $data['category'];

        ///$em = $this->container->get('doctrine.orm.entity_manager')->getConnection();

        $joliquizCategory = new Category('Certificationy');
        $em->persist($joliquizCategory);

        foreach ($questions as $question) {

            $answers = array();

            $joliquizQuestion = new Question();
            $joliquizQuestion->addCategory($joliquizCategory);
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

        return true;
    }
}
