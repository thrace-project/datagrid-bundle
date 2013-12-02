<?php
namespace Thrace\DataGridBundle\Tests\Doctrine\Handler;

use Thrace\DataGridBundle\Tests\Fixture\Entity\GridDependent;

use Thrace\DataGridBundle\Tests\Fixture\Entity\GridMaster;

use Thrace\DataGridBundle\Doctrine\ORM\DataGridHandler;

use Symfony\Component\DependencyInjection\Container;

use Doctrine\ORM\QueryBuilder;

use Thrace\ComponentBundle\Test\Tool\BaseTestCaseORM;

class DataGridHandlerORMTest extends BaseTestCaseORM
{
    const GRID_MASTER = 'Thrace\DataGridBundle\Tests\Fixture\Entity\GridMaster';
    
    const GRID_DEPENDANT = 'Thrace\DataGridBundle\Tests\Fixture\Entity\GridDependent';
    
    protected function setUp()
    {
        $this->createMockEntityManager();
    }

    public function testBuildQueryWithInvalidQueryBuilder()
    {
        $options = array(
            'page' => 1,
            'records' => 10,
        );
        
        $this->setExpectedException('InvalidArgumentException', 'Value must be instance of Doctrine\ORM\QueryBuilder.');
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher(0))
            ->setDataGrid($this->getMockDataGridForInvalidTest())
            ->resolveOptions($options)
            ->buildQuery()
        ;
           
    }
    
    public function testBuildQueryWithDependentGrid()
    {
        $options = array(
            'page' => 1,
            'records' => 10,
            'masterGridRowId' => 20
        );
        
        $this->populate();
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher(2))
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilderForDependentGrid(), true))
            ->resolveOptions($options)
            ->buildQuery()
        ;
        
        $this->assertSame(3, $dataGridHandler->getCount());
        $this->assertInstanceOf('Doctrine\ORM\Query', $dataGridHandler->getQuery());
    
    }
    
    public function testBuildQueryWithSortableGrid()
    {
        $this->populate();
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher(3))
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder(), false, true))
            ->resolveOptions($this->getOptions('ne', 'test'))
            ->buildQuery()
            ->buildData()
        ;
        
        $this->assertSame(20, $dataGridHandler->getCount());
    
    }
    
    public function testBuildDataWithOperEq()
    {
        $this->populate();
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('eq', 'name_1'))
            ->buildQuery()
            ->buildData()
        ;
        
        $data = $dataGridHandler->getData();
        $this->assertSame(1, $dataGridHandler->getCount());
        $this->assertCount(1, $data);
        $this->assertSame('name_1', $data['0']['name']);
        $this->assertNotEmpty($dataGridHandler->getProcessedData());

    }
    
    public function testBuildDataWithOperNe()
    {
        $this->populate();
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('ne', 'name_1'))
            ->buildQuery()
            ->buildData()
        ;
        
        $data = $dataGridHandler->getData();
        $this->assertSame(19, $dataGridHandler->getCount());
        $this->assertCount(10, $data);
        $this->assertSame('name_20', $data['0']['name']);

    }
    
    public function testBuildDataWithOperLt()
    {
        $this->populate();
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('lt', 15, 'm.rank'))
            ->buildQuery()
            ->buildData()
        ;
        
        $data = $dataGridHandler->getData();
        $this->assertSame(14, $dataGridHandler->getCount());
        $this->assertCount(10, $data);
        $this->assertSame('name_14', $data['0']['name']);

    }
    
    public function testBuildDataWithOperLe()
    {
        $this->populate();
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('le', 15, 'm.rank'))
            ->buildQuery()
            ->buildData()
        ;
        
        $data = $dataGridHandler->getData();
        $this->assertSame(15, $dataGridHandler->getCount());
        $this->assertCount(10, $data);
        $this->assertSame('name_15', $data['0']['name']);

    }
    
    public function testBuildDataWithOperGt()
    {
        $this->populate();
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('gt', 5, 'm.rank'))
            ->buildQuery()
            ->buildData()
        ;
        
        $data = $dataGridHandler->getData();
        $this->assertSame(15, $dataGridHandler->getCount());
        $this->assertCount(10, $data);
        $this->assertSame('name_20', $data['0']['name']);

    }
    
    public function testBuildDataWithOperGe()
    {
        $this->populate();
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('ge', 5, 'm.rank'))
            ->buildQuery()
            ->buildData()
        ;
        
        $data = $dataGridHandler->getData();
        $this->assertSame(16, $dataGridHandler->getCount());
        $this->assertCount(10, $data);
        $this->assertSame('name_20', $data['0']['name']);

    }
    
    public function testBuildDataWithOperBw()
    {
        $this->populate();
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('bw', 'name_1', 'm.name'))
            ->buildQuery()
            ->buildData()
        ;
        
        $data = $dataGridHandler->getData();
        $this->assertSame(11, $dataGridHandler->getCount());
        $this->assertCount(10, $data);
        $this->assertSame('name_19', $data['0']['name']);

    }
    
    public function testBuildDataWithOperBn()
    {
        $this->populate();
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('bn', 'name_1', 'm.name'))
            ->buildQuery()
            ->buildData()
        ;
        
        $data = $dataGridHandler->getData();
        $this->assertSame(9, $dataGridHandler->getCount());
        $this->assertCount(9, $data);
        $this->assertSame('name_20', $data['0']['name']);

    }
    
    public function testBuildDataWithOperEw()
    {
        $this->populate();
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('ew', '5', 'm.name'))
            ->buildQuery()
            ->buildData()
        ;
        
        $data = $dataGridHandler->getData();
        $this->assertSame(2, $dataGridHandler->getCount());
        $this->assertCount(2, $data);
        $this->assertSame('name_15', $data['0']['name']);

    }
    
    public function testBuildDataWithOperEn()
    {
        $this->populate();
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('en', '5', 'm.name'))
            ->buildQuery()
            ->buildData()
        ;
        
        $data = $dataGridHandler->getData(); 
        $this->assertSame(18, $dataGridHandler->getCount());
        $this->assertCount(10, $data);
        $this->assertSame('name_20', $data['0']['name']);

    }
    
    public function testBuildDataWithOperCn()
    {
        $this->populate();
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('cn', '5', 'm.name'))
            ->buildQuery()
            ->buildData()
        ;
        
        $data = $dataGridHandler->getData();  
        $this->assertSame(2, $dataGridHandler->getCount());
        $this->assertCount(2, $data);
        $this->assertSame('name_15', $data['0']['name']);
    }
    
    public function testBuildDataWithOperNc()
    {
        $this->populate();
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('nc', '5', 'm.name', 'OR'))
            ->buildQuery()
            ->buildData()
        ;
        
        $data = $dataGridHandler->getData();  
        $this->assertSame(18, $dataGridHandler->getCount());
        $this->assertCount(10, $data);
        $this->assertSame('name_20', $data['0']['name']);
    }
    
    public function testBuildDataWithInvalidOperator()
    {
        $this->populate();
        $this->setExpectedException('InvalidArgumentException');
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher(0))
            ->setDataGrid($this->getMockDataGridForInvalidTest($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('invalid', '5', 'm.name'))
            ->buildQuery()
            ->buildData()
        ;  
    }
    
    public function testBuildDataWithInvalidGroupOperator()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher(0))
            ->setDataGrid($this->getMockDataGridForInvalidTest($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('eq', '5', 'm.name', 'INVALID'))
            ->buildQuery()
            ->buildData()
        ;  
    }
    
    public function testBuildDataWithInvalidGroupOp()
    {

        $options = array(
            'search' => true,
            'filters' => array('groupOp' => 'INVALID'),
            'page' => 1,
            'orderBy' => 'm.rank',
            'sort' => 'DESC',
            'records' => 10
        
        );

        $this->setExpectedException('InvalidArgumentException', 'Operator does not match OR | AND');
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher(0))
            ->setDataGrid($this->getMockDataGridForInvalidTest($this->getQueryBuilder()))
            ->resolveOptions($options)
            ->buildQuery()
        ;  
    }
    
    public function testBuildDataWithInvalidRules()
    {

        $options = array(
            'search' => true,
            'filters' => array('groupOp' => 'AND', 'rules' => 'INVALID'),
            'page' => 1,
        );

        $this->setExpectedException('InvalidArgumentException', 'Rules are not set.');
        
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher(0))
            ->setDataGrid($this->getMockDataGridForInvalidTest($this->getQueryBuilder()))
            ->resolveOptions($options)
            ->buildQuery()
        ;  
    }
    
    public function testAggregatedFieldWithEqAnd()
    {
        $this->populate();
    
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('eq', 3, 'slaveCount', 'AND'))
            ->buildQuery()
            ->buildData()
        ;
        
        $data = $dataGridHandler->getData(); 
        $this->assertSame(1, $dataGridHandler->getCount());
        $this->assertCount(1, $data);
        $this->assertSame('name_20', $data['0']['name']);
        $this->assertSame('3', $data['0']['slaveCount']);
    }
    
    public function testAggregatedFieldWithEqOr()
    {
        $this->populate();
    
        $dataGridHandler = new DataGridHandler();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid($this->getQueryBuilder()))
            ->resolveOptions($this->getOptions('eq', 3, 'slaveCount', 'OR'))
            ->buildQuery()
            ->buildData()
        ;
        
        $data = $dataGridHandler->getData(); 
        $this->assertSame(1, $dataGridHandler->getCount());
        $this->assertCount(1, $data);
        $this->assertSame('name_20', $data['0']['name']);
        $this->assertSame('3', $data['0']['slaveCount']);
    }

    protected function getUsedEntityFixtures()
    {
        return array(self::GRID_MASTER, self::GRID_DEPENDANT);
    }
    
    protected function getQueryBuilder()
    {
        $qb =  $this->em->createQueryBuilder();
        $qb
            ->select(array('m.id, m.name, m.rank, COUNT(s.id) as slaveCount, m'))
            ->from('\Thrace\DataGridBundle\Tests\Fixture\Entity\GridMaster', 'm')
            ->leftJoin('m.grids', 's')
            ->groupBy('m.id')
        ;
        
        return $qb;
    }
    
    private function getQueryBuilderForDependentGrid()
    {
        $qb =  $this->em->createQueryBuilder();
        $qb
            ->select(array('s.id, s.name, s'))
            ->from('\Thrace\DataGridBundle\Tests\Fixture\Entity\GridDependent', 's')
            ->join('s.master', 'm')
            ->where('m.id = :masterGridRowId')
        ;
        
        return $qb;
    }
    
    
    private function populate()
    {
        for($i = 1; $i <= 20; $i++){
            $gridMaster = new GridMaster();
            $gridMaster->setName('name_' . $i);
            $gridMaster->setRank($i);
            $this->em->persist($gridMaster);
        }
        
        for($i = 1; $i <= 3; $i++){
            $gridDependant = new GridDependent();
            $gridDependant->setName('name_' . $i);
            $this->em->persist($gridDependant);
            $gridMaster->addGrid($gridDependant);
        }

        $this->em->flush();
        $this->em->clear();
    
    }
    
    
    protected function getMockEventDispatcher($expects = 3)
    {
        $mock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        
        $mock
            ->expects($this->exactly($expects))
            ->method('dispatch')
            ->withAnyParameters()
        ;
        
        return $mock;
    }
    
    protected function getMockDataGrid(QueryBuilder $qb, $dependent = false, $sortable = false)
    {
        $mock = $this->getMock('Thrace\DataGridBundle\DataGrid\DataGridInterface');
        
        $mock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'))
        ;
        
        $mock
            ->expects($this->once())
            ->method('isDependentGrid')
            ->will($this->returnValue($dependent))
        ;
        
        
        $mock
            ->expects($this->once())
            ->method('isSortableEnabled')
            ->will($this->returnValue($sortable))
        ;
        
        $mock
            ->expects($this->once())
            ->method('getQueryBuilder')
            ->will($this->returnValue($qb))
        ;
        
        $mock
            ->expects($this->any())
            ->method('getColModel')
            ->will($this->returnValue(array(
                array('index' => 'm.name', 'name' => 'name'),
                array('index' => 'slaveCount', 'name' => 'slaveCount', 'aggregated' => true),
            )))
        ;
        
        return $mock;
    }
    
    protected function getMockDataGridForInvalidTest(QueryBuilder $qb = null)
    {
        $mock = $this->getMock('Thrace\DataGridBundle\DataGrid\DataGridInterface');
        
        $mock
            ->expects($this->any())
            ->method('getQueryBuilder')
            ->will($this->returnValue($qb))
        ;

        return $mock;
    }
    
    protected function getOptions($op, $value, $field = 'm.name', $groupOp = 'AND')
    {
        $options = array(
            'search' => true,
            //'filters' => (array) json_decode('{"groupOp":"'.$groupOp.'","rules":[{"field":"'. $field .'","op":"'.$op.'","data":"'.$value.'"}]}'),
            'filters' => array('groupOp' => $groupOp, 'rules' => array(
                array(
                    'field' => $field,
                    'op' => $op,
                    'data' => $value
                ) ,
            )),
            'page' => 1,
            'orderBy' => 'm.rank',
            'sort' => 'DESC',
            'records' => 10
        );
        
        return $options;
    }
    
    
}