<?php

namespace MensaBattle\APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Report on an example photo
 *
 * @ORM\Table(name="examplereport")
 * @ORM\Entity
 */
class ExampleReport extends Report
{
    /**
     * @var unknown
     *
     * @ORM\OneToOne(targetEntity="Example")
     * @ORM\JoinColumn(name="example_id", referencedColumnName="id")
     */
    private $example;

    /**
     * Set example.
     *
     * @param Example $example
     * @return ExampleReport
     */
    public function setExample(Example $example = null)
    {
        $this->example = $example;
    
        return $this;
    }

    /**
     * Get example.
     *
     * @return Example 
     */
    public function getExample()
    {
        return $this->example;
    }
    
    public function toArray()
    {
        return array('id' => $this->id,
            'reporter' => $this->reporter->getId(),
            'example' => $this->example->getId(),
            'message' => $this->message,
            'time' => $this->time
            );
    }
}