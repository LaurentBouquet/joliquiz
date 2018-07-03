<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

/**
 * @ORM\Table(name="tbl_category")
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @var int The id of this category
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string The shortname of the category
     * @ORM\Column(type="string", length=50)
     */
    private $shortname;

    /**
     * @var string The longname of the category
     * @ORM\Column(type="string", length=255)
     */
    private $longname;

    // /**
    //  * @var string The quizzes list in this category
    //  *
    //  * @ORM\ManyToMany(targetEntity="App\Entity\Quiz", mappedBy="category", cascade={"persist", "remove"})
    //  */
    // private $quizzes;
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Quiz", mappedBy="categories")
     */
    private $quizzes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Question", mappedBy="categories")
     */
    private $questions;

    public function __construct($shortname = null)
    {
        $this->quizzes = new ArrayCollection();
        $this->questions = new ArrayCollection();
        if ($shortname) {
            $this->shortname = $shortname;
            $this->longname = $shortname;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getShortname(): ?string
    {
        return $this->shortname;
    }

    public function setShortname(string $shortname): self
    {
        $this->shortname = $shortname;

        return $this;
    }

    public function getLongname(): ?string
    {
        return $this->longname;
    }

    public function setLongname(string $longname): self
    {
        $this->longname = $longname;

        return $this;
    }

    /**
     * @return Collection|Quiz[]
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): self
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes[] = $quiz;
            $quiz->addCategory($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): self
    {
        if ($this->quizzes->contains($quiz)) {
            $this->quizzes->removeElement($quiz);
            $quiz->removeCategory($this);
        }

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->addCategory($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            $question->removeCategory($this);
        }

        return $this;
    }
}
