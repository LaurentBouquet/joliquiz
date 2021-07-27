<?php

namespace App\Entity;

use DateTime;
use App\Entity\Session;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuizRepository")
 * @ORM\Table(name="tbl_quiz")
 */
class Quiz
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $summary;

    /**
     * @ORM\Column(type="integer")
     */
    private $number_of_questions;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="quizzes")
     */
    private $created_by;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="quizzes")
     * @ORM\JoinTable(name="tbl_quiz_category")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Workout", mappedBy="quiz", orphanRemoval=true)
     */
    private $workouts;

    /**
     * @ORM\Column(type="boolean")
     */
    private $show_result_question;

    /**
     * @ORM\Column(type="boolean")
     */
    private $show_result_quiz;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="quizzes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $language;

    /**
     * @ORM\Column(type="boolean")
     */
    private $allow_anonymous_workout;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $result_quiz_comment;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $start_quiz_comment;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $default_question_max_duration;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $actived_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Session", mappedBy="quiz", orphanRemoval=true)
     */
    private $sessions;



    public function __construct()
    {
        $this->active = false;
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->setShowResultQuestion(false);
        $this->setShowResultQuiz(false);
        $this->setNumberOfQuestions(10);
        $this->categories = new ArrayCollection();
        $this->workouts = new ArrayCollection();
        $this->setAllowAnonymousWorkout(false);
        $this->sessions = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getNumberOfQuestions(): ?int
    {
        return $this->number_of_questions;
    }

    public function setNumberOfQuestions(int $number_of_questions): self
    {
        $this->number_of_questions = $number_of_questions;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active, EntityManager $em): self
    {
        $now = new DateTime();

        if ($active) {
            if (!$this->getActive()) {
                $this->actived_at = $now;
                $session = new Session($this, $now);
                $em->persist($session);
            }
        } else {
            $this->actived_at = null;
            $session = $this->getLastSession();
            $session->setEndedAt($now);
            $em->persist($session);
        }

        $this->active = $active;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->created_by;
    }

    public function setCreatedBy(?User $created_by): self
    {
        $this->created_by = $created_by;

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
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Collection|Workout[]
     */
    public function getWorkouts(): Collection
    {
        return $this->workouts;
    }

    public function addWorkout(Workout $workout): self
    {
        if (!$this->workouts->contains($workout)) {
            $this->workouts[] = $workout;
            $workout->setQuiz($this);
        }

        return $this;
    }

    public function removeWorkout(Workout $workout): self
    {
        if ($this->workouts->contains($workout)) {
            $this->workouts->removeElement($workout);
            // set the owning side to null (unless already changed)
            if ($workout->getQuiz() === $this) {
                $workout->setQuiz(null);
            }
        }

        return $this;
    }

    public function getShowResultQuestion(): ?bool
    {
        return $this->show_result_question;
    }

    public function setShowResultQuestion(bool $show_result_question): self
    {
        $this->show_result_question = $show_result_question;

        return $this;
    }

    public function getShowResultQuiz(): ?bool
    {
        return $this->show_result_quiz;
    }

    public function setShowResultQuiz(bool $show_result_quiz): self
    {
        $this->show_result_quiz = $show_result_quiz;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getAllowAnonymousWorkout(): ?bool
    {
        return $this->allow_anonymous_workout;
    }

    public function setAllowAnonymousWorkout(bool $allow_anonymous_workout): self
    {
        $this->allow_anonymous_workout = $allow_anonymous_workout;

        return $this;
    }

    public function getResultQuizComment(): ?string
    {
        return $this->result_quiz_comment;
    }

    public function setResultQuizComment(?string $result_quiz_comment): self
    {
        $this->result_quiz_comment = $result_quiz_comment;

        return $this;
    }

    public function getStartQuizComment(): ?string
    {
        return $this->start_quiz_comment;
    }

    public function setStartQuizComment(?string $start_quiz_comment): self
    {
        $this->start_quiz_comment = $start_quiz_comment;

        return $this;
    }

    public function getDefaultQuestionMaxDuration(): ?int
    {
        return $this->default_question_max_duration;
    }

    public function setDefaultQuestionMaxDuration(?int $default_question_max_duration): self
    {
        $this->default_question_max_duration = $default_question_max_duration;

        return $this;
    }

    public function getActivedAt(): ?\DateTimeInterface
    {
        return $this->actived_at;
    }

    public function setActivedAt(?\DateTimeInterface $actived_at): self
    {
        $this->actived_at = $actived_at;

        return $this;
    }

    /**
     * @return Collection|Session[]
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    /**
     * @return Session
     */
    public function getLastSession(): Session
    {
        return $this->sessions->last();
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setQuiz($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            // set the owning side to null (unless already changed)
            if ($session->getQuiz() === $this) {
                $session->setQuiz(null);
            }
        }

        return $this;
    }


}
