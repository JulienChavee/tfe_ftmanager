<?php

namespace MatchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Matchs
 *
 * @ORM\Table(name="matchs")
 * @ORM\Entity(repositoryClass="MatchBundle\Repository\MatchsRepository")
 */
class Matchs
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var \UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="referee", referencedColumnName="id")
     */
    private $referee;

    /**
     * @var \TeamBundle\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="TeamBundle\Entity\Team")
     * @ORM\JoinColumn(name="team1", referencedColumnName="id")
     */
    private $team1;

    /**
     * @var \TeamBundle\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="TeamBundle\Entity\Team")
     * @ORM\JoinColumn(name="team2", referencedColumnName="id")
     */
    private $team2;

    //private $field;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="MatchBundle\Entity\Prestation", mappedBy="match")
     */
    private $prestations;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Matchs
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set referee
     *
     * @param \UserBundle\Entity\User $referee
     *
     * @return Matchs
     */
    public function setReferee(\UserBundle\Entity\User $referee = null)
    {
        $this->referee = $referee;

        return $this;
    }

    /**
     * Get referee
     *
     * @return \UserBundle\Entity\User
     */
    public function getReferee()
    {
        return $this->referee;
    }

    /**
     * Set team1
     *
     * @param \TeamBundle\Entity\Team $team1
     *
     * @return Matchs
     */
    public function setTeam1(\TeamBundle\Entity\Team $team1 = null)
    {
        $this->team1 = $team1;

        return $this;
    }

    /**
     * Get team1
     *
     * @return \TeamBundle\Entity\Team
     */
    public function getTeam1()
    {
        return $this->team1;
    }

    /**
     * Set team2
     *
     * @param \TeamBundle\Entity\Team $team2
     *
     * @return Matchs
     */
    public function setTeam2(\TeamBundle\Entity\Team $team2 = null)
    {
        $this->team2 = $team2;

        return $this;
    }

    /**
     * Get team2
     *
     * @return \TeamBundle\Entity\Team
     */
    public function getTeam2()
    {
        return $this->team2;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Matchs
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->prestations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add prestation
     *
     * @param \MatchBundle\Entity\Prestation $prestation
     *
     * @return Matchs
     */
    public function addPrestation(\MatchBundle\Entity\Prestation $prestation)
    {
        $this->prestations[] = $prestation;

        return $this;
    }

    /**
     * Remove prestation
     *
     * @param \MatchBundle\Entity\Prestation $prestation
     */
    public function removePrestation(\MatchBundle\Entity\Prestation $prestation)
    {
        $this->prestations->removeElement($prestation);
    }

    /**
     * Get prestations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrestations()
    {
        return $this->prestations;
    }
}
