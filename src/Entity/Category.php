<?php

namespace App\Entity;

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
     * @ORM\GeneratedValue(strategy="AUTO")
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
}
