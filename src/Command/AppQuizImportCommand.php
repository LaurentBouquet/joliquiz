<?php

namespace App\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Yaml\Yaml;
use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Category;
use App\Entity\Language;

class AppQuizImportCommand extends DoctrineCommand
{
    protected static $defaultName = 'app:quiz-import';

    protected $em;

    protected function configure()
    {
        $this
            ->setDescription('Imports quizzes from another software')
            ->addArgument('file', InputArgument::REQUIRED, 'Quiz data file')
            ->addArgument('silent', InputArgument::OPTIONAL, 'No confirmation')
            ->addOption('format', '-f', InputOption::VALUE_REQUIRED, 'Quiz data file format')
            ->addOption('category', '-c', InputOption::VALUE_OPTIONAL, 'Category (shortname) to class imported questions')
            ->addOption('language', '-l', InputOption::VALUE_OPTIONAL, 'Language to associate with imported questions (default "en")', 'en')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> imports quizzes from another software:

  <info>php %command.full_name%</info>

For example, to import 'certificationy' data from '/home/user/architecture.yml' file:

  <info>php %command.full_name% --format certificationy /home/user/architecture.yml</info>
or
  <info>php %command.full_name% --format certificationy --category 'Symfony 3' /home/user/architecture.yml silent</info>

EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $this->em = $doctrine->getManager();

        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');
        $silent = $input->getArgument('silent');
        $format = $input->getOption('format');
        $category = $input->getOption('category');
        $language = $input->getOption('language');

        if ($file) {
            if (!$silent) {
                $io->note(sprintf('Import data from file: "%s"', $file));
            }
        } else {
            throw new \LogicException(sprintf("The file must be provided. Pass --help to see options."));
        }

        if ($format) {
            if (!$silent) {
                $io->note(sprintf('Import format: "%s"', $format));
            }
        } else {
            throw new \LogicException(sprintf("The format must be provided. Pass --help to see options."));
        }

        if ($category) {
            if (!$silent) {
                $io->note(sprintf('Class imported questions into category: "%s"', $category));
            }
        }

        if (!$silent) {
            $io->note(sprintf('Questions language: "%s"', $language));
            if (!$io->confirm('<question>Careful, data will be inserted in the database. Do you want to continue y/N ?</question>', false)) {
                return;
            }
        }

        switch ($format) {
            case 'certificationy':
                $result = $this->importCertificationy($output, $format, $file, $language, $category);
                break;

            default:
                $io->error('Not yet implemented');
                break;
        }

        if ($result) {
            if (!$silent) {
                $io->success(sprintf('Import from "%s" file completed!', $file));
            }
        }
        else {
            $io->error(sprintf('Error durring import from "%s" file.', $file));
        }
    }

    protected function getCategory($category, $joliquizLanguage)
    {
        $categoryRepository = $this->em->getRepository(Category::class);

        $persistedCategory = $categoryRepository->findOneByShortnameAndLanguage($category, $joliquizLanguage);

        if ($persistedCategory) {
            return $persistedCategory;
        } else {
            $joliquizCategory = new Category($category);
            $joliquizCategory->setLanguage($joliquizLanguage);
            $this->em->persist($joliquizCategory);
            return $joliquizCategory;
        }
    }

    protected function getLanguage($language)
    {
        // $repository = $this->em->getRepository(Language::class);
        // $persistedLanguage = $repository->findOneById($language);
        // return $persistedLanguage;
        return $this->em->getReference(Language::class, $language);
    }

    protected function importCertificationy($output, $format, $file, $language, $secondCategory=null)
    {
        $data = Yaml::parseFile($file);
        $questions = $data['questions'];
        $category = $data['category'];

        $joliquizLanguage = $this->getLanguage($language);

        $joliquizCategory = $this->getCategory($category, $joliquizLanguage);
        if ($secondCategory) {
            $joliquizSecondCategory = $this->getCategory($secondCategory, $joliquizLanguage);
        }

        if ($secondCategory) {
            $output->write(sprintf('Importing %s question(s) (language: "%s") in category "%s" and category "%s":', count($questions), $language, $category, $secondCategory), true);
        } else {
            $output->write(sprintf('Importing %s question(s) (language: "%s") in category "%s":', count($questions), $language, $category), true);
        }

        // create a new progress bar (50 units)
        $progress = new ProgressBar($output, sizeof($questions));

        // start and displays the progress bar
        $progress->start();

        foreach ($questions as $question) {
            $answers = array();

            $joliquizQuestion = new Question();
            $joliquizQuestion->addCategory($joliquizCategory);
            if ($secondCategory) {
                $joliquizQuestion->addCategory($joliquizSecondCategory);
            }
            $joliquizQuestion->setLanguage($joliquizLanguage);
            $joliquizQuestion->setText($question['question']);

            foreach ($question['answers'] as $answer) {
                $joliquizAnswer = new Answer();
                $joliquizAnswer->setText($answer['value']);
                $joliquizAnswer->setCorrect($answer['correct']);
                $this->em->persist($joliquizAnswer);
                $joliquizQuestion->addAnswer($joliquizAnswer);
            }

            /*
            if (!isset($question['shuffle']) || true === $item['shuffle']) {
                shuffle($answers);
            }
            $help = isset($item['help']) ? $item['help']: null;
            //$questions[] = new Question($item['question'], $item['category'], $answers, $help);
            */

            $this->em->persist($joliquizQuestion);

            $progress->advance();
        }

        $this->em->flush();

        $progress->finish();
        $output->write(' completed', true);

        return true;
    }
}
