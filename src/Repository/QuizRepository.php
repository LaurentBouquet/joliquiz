<?php

namespace App\Repository;

use App\Entity\Language;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    private $em;
    private $param;
    private $language;
    private $translator;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em, ParameterBagInterface $param, TranslatorInterface $translator)
    {
        parent::__construct($registry, Quiz::class);
        $this->em = $em;
        $this->param = $param;
        $this->language = $this->em->getReference(Language::class, $this->param->get('locale'));
        $this->translator = $translator;
    }

    public function create(): Quiz
    {
        $quiz = new Quiz();
        $quiz->setLanguage($this->language);
        $commentLines = "0-24: " . $this->translator->trans("Your result is not enough, please review and redo this quiz.") . "\n";
        $commentLines = $commentLines . "25-49: " . $this->translator->trans("Your result is fairly average, we advise you to review the questions on which you made mistakes, then redo this quiz.") . "\n";
        $commentLines = $commentLines . "50-74: " . $this->translator->trans("Good result. You have acquired most of the concepts covered in this quiz.") . "\n";
        $commentLines = $commentLines . "75-100: " . $this->translator->trans("Congratulations! Your answers showed that you have a good knowledge of the concepts covered in this quiz.") . "\n";
        $quiz->setResultQuizComment($commentLines);
        return $quiz;
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $builder = $this->createQueryBuilder('q');
        $builder->andWhere('q.id = :id');
        $builder->setParameter('id', $id);
        $builder->andWhere('q.language = :language');
        $builder->setParameter('language', $this->language);
        $builder->orderBy('q.title', 'ASC');
        return $builder->getQuery()->getOneOrNullResult();
    }

    public function findAll($isAdmin = false)
    {
        $builder = $this->createQueryBuilder('q');
        $builder->andWhere('q.language = :language');
        $builder->setParameter('language', $this->language);
        if (!$isAdmin) {
            $builder->andWhere('q.active = :active');
            $builder->setParameter('active', true);
        }
        $builder->orderBy('q.title', 'ASC');
        return $builder->getQuery()->getResult();
    }

    public function findAllByCategories($isAdmin = false, array $categories)
    {
        $builder = $this->createQueryBuilder('q');
        $builder->andWhere('q.language = :language');
        $builder->setParameter('language', $this->language);
        $builder->innerJoin('q.categories', 'categories');
        $builder->andWhere($builder->expr()->in('categories', ':categories'))->setParameter('categories', $categories);
        if (!$isAdmin) {
            $builder->andWhere('q.active = :active');
            $builder->setParameter('active', true);
        }
        $builder->orderBy('q.title', 'ASC');
        return $builder->getQuery()->getResult();
    }
//    /**
    //     * @return Quiz[] Returns an array of Quiz objects
    //     */
    /*
    public function findByExampleField($value)
    {
    return $this->createQueryBuilder('q')
    ->andWhere('q.exampleField = :val')
    ->setParameter('val', $value)
    ->orderBy('q.id', 'ASC')
    ->setMaxResults(10)
    ->getQuery()
    ->getResult()
    ;
    }
     */

    /*
public function findOneBySomeField($value): ?Quiz
{
return $this->createQueryBuilder('q')
->andWhere('q.exampleField = :val')
->setParameter('val', $value)
->getQuery()
->getOneOrNullResult()
;
}
 */
}
