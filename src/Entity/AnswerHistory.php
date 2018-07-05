<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnswerHistoryRepository")
 * @ORM\Table(name="tbl_history_answer")
 */
class AnswerHistory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $date_time;

    public function getId()
    {
        return $this->id;
    }

    public function getDateTime(): ?\DateTimeImmutable
    {
        return $this->date_time;
    }

    public function setDateTime(\DateTimeImmutable $date_time): self
    {
        $this->date_time = $date_time;

        return $this;
    }
}
