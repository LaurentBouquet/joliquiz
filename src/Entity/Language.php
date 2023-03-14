<?php

namespace App\Entity;

use App\Entity\Quiz;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Question;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\LanguageRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: LanguageRepository::class)]
#[ORM\Table(name: 'tbl_language')]
class Language
{
    #[ORM\Id]
    #[ORM\Column(length: 2)]
    private ?string $id = null;

    #[ORM\Column(length: 50, unique:true, nullable:false)]
    private $english_name;

    #[ORM\Column(length: 50)]
    private $native_name;

    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'language')]
    private $questions;

    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'language')]
    private $quizzes;

    #[ORM\OneToMany(mappedBy: 'prefered_language', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'language')]
    private $categories;



    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->getNativeName();
    }

    // public function getCode(): ?string
    // {
    //     return $this->code;
    // }

    // public function setCode(string $code): self
    // {
    //     $this->code = $code;
    //
    //     return $this;
    // }

    public function getEnglishName(): ?string
    {
        return $this->english_name;
    }

    public function setEnglishName(string $english_name): self
    {
        $this->english_name = $english_name;

        return $this;
    }

    public function getNativeName(): ?string
    {
        return $this->native_name;
    }

    public function setNativeName(string $native_name): self
    {
        $this->native_name = $native_name;

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
            $question->setLanguage($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getLanguage() === $this) {
                $question->setLanguage(null);
            }
        }

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
            $quiz->setLanguage($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): self
    {
        if ($this->quizzes->contains($quiz)) {
            $this->quizzes->removeElement($quiz);
            // set the owning side to null (unless already changed)
            if ($quiz->getLanguage() === $this) {
                $quiz->setLanguage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setPreferedLanguage($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getPreferedLanguage() === $this) {
                $user->setPreferedLanguage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setLanguage($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            // set the owning side to null (unless already changed)
            if ($category->getLanguage() === $this) {
                $category->setLanguage(null);
            }
        }

        return $this;
    }
}
