<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\WorkoutRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: WorkoutRepository::class)]
#[ORM\Table(name: 'tbl_workout')]
class Workout
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'workouts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $student = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy:'workouts')]
    #[ORM\JoinColumn(nullable: false)]
    private $quiz;

    #[ORM\Column(type: 'datetime')]
    private $started_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $ended_at;

    #[ORM\Column(type: 'integer')]
    private $number_of_questions;

    #[ORM\Column(type: 'boolean')]
    private $completed;

    #[ORM\OneToMany(targetEntity: QuestionHistory::class, mappedBy: 'workout', orphanRemoval: true)]
    private $questionsHistory;

    #[ORM\Column(type: 'float', nullable: true)]
    private $score;

    #[ORM\Column(type: 'text', nullable: true)]
    private $comment;

    #[ORM\Column(length: 255, nullable: true)]
    private $token;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy:'workouts')]
    private $session;

    public function __construct()
    {
        $this->completed = false;
        $this->questionsHistory = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(?User $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->started_at;
    }

    public function setStartedAt(\DateTimeInterface $started_at): self
    {
        $this->started_at = $started_at;

        return $this;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->ended_at;
    }

    public function setEndedAt(\DateTimeInterface $ended_at): self
    {
        $this->ended_at = $ended_at;

        return $this;
    }

    public function getDuration(): ?\DateInterval
    {
        return $this->ended_at->diff($this->started_at);
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

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * @return Collection|QuestionHistory[]
     */
    public function getQuestionsHistory(): Collection
    {
        return $this->questionsHistory;
    }

    public function addQuestionsHistory(QuestionHistory $questionsHistory): self
    {
        if (!$this->questionsHistory->contains($questionsHistory)) {
            $this->questionsHistory[] = $questionsHistory;
            $questionsHistory->setWorkout($this);
        }

        return $this;
    }

    public function removeQuestionsHistory(QuestionHistory $questionsHistory): self
    {
        if ($this->questionsHistory->contains($questionsHistory)) {
            $this->questionsHistory->removeElement($questionsHistory);
            // set the owning side to null (unless already changed)
            if ($questionsHistory->getWorkout() === $this) {
                $questionsHistory->setWorkout(null);
            }
        }

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): self
    {
        $this->session = $session;

        return $this;
    }
}
