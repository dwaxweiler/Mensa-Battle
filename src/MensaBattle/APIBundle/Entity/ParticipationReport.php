<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParticipationReport
 *
 * @ORM\Table(name="participationreport")
 * @ORM\Entity
 */
class ParticipationReport extends Report
{
    /**
     * @var unknown
     *
     * @ORM\OneToOne(targetEntity="Participation")
     * @ORM\JoinColumn(name="participation_id", referencedColumnName="id")
     */
    private $participation;

    /**
     * Set participation
     *
     * @param Participation $participation
     * @return ParticipationReport
     */
    public function setParticipation(Participation $participation = null)
    {
        $this->participation = $participation;
    
        return $this;
    }

    /**
     * Get participation
     *
     * @return Participation 
     */
    public function getParticipation()
    {
        return $this->participation;
    }
    
    public function toArray()
    {
        return array('id' => $this->id,
            'reporter' => $this->reporter->getId(),
            'participation' => $this->participation->getId(),
            'message' => $this->message,
            'time' => $this->time
            );
    }
}