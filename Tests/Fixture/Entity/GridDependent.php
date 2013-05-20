<?php
namespace Thrace\DataGridBundle\Tests\Fixture\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="grid_dependent")
 * 
 */
class GridDependent
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string 
     *
     * @ORM\Column(type="string", name="name", length=255, nullable=false, unique=true)
     */
    protected $name;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Thrace\DataGridBundle\Tests\Fixture\Entity\GridMaster",  inversedBy="grids")
     */   
    protected $master;

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function setMaster(GridMaster $master)
    {
        $this->master = $master;
    }
    
    public function getMaster()
    {
        return $this->master;
    }

}
