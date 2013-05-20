<?php
namespace Thrace\DataGridBundle\Tests\Fixture\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="grid_master")
 * 
 */
class GridMaster
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
    protected  $name;
    
    /**
     * @var string 
     *
     * @ORM\Column(type="integer", name="rank")
     */
    protected $rank;
    
    /**
     * @ORM\OneToMany(targetEntity="\Thrace\DataGridBundle\Tests\Fixture\Entity\GridDependent", mappedBy="master", cascade={"all"})
     */  
    protected $grids; 
    
    
    public function __construct()
    {
        $this->grids = new ArrayCollection();
    }

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
    
    public function setRank($rank)
    {
        $this->rank = $rank;
    }
    
    public function getRank()
    {
        return $rank;
    }
    
    public function addGrid(GridDependent $grid)
    {
        $this->grids[] = $grid;
        $grid->setMaster($this);
    }
    
    public function getGrids()
    {
        return $this->grids;
    }

}
