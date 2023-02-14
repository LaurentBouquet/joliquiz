<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: AnswerRepository::class)]
#[ORM\Table(name: 'tbl_answer')]
class Answer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private $text;

    #[ORM\Column(type: 'boolean')]
    private $correct;

    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy:'answers')]
    #[ORM\JoinColumn(nullable: false)]
    private $question;

    /**
     * This field will not be persisted
     * It is used to store the answer given by student (type="boolean") in the form
     */
    private $workout_correct_given = false;


    public function __construct()
    {
        //$this->questions = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCorrect(): ?bool
    {
        return $this->correct;
    }

    public function setCorrect(bool $correct): self
    {
        $this->correct = $correct;

        return $this;
    }

    public function getWorkoutCorrectGiven(): ?bool
    {
        return $this->workout_correct_given;
    }

    public function setWorkoutCorrectGiven(bool $workout_correct_given): self
    {
        $this->workout_correct_given = $workout_correct_given;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

}
