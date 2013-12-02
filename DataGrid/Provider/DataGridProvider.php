<?php
/*
 * This file is part of ThraceDataGridBundle
 *
 * (c) Nikolay Georgiev <azazen09@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\DataGridBundle\DataGrid\Provider;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Default implementation of the DataGridProviderInterface
 *
 * @author Nikolay Georgiev <azazen09@gmail.com>
 * @since 1.0
 */
class DataGridProvider implements DataGridProviderInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var DataGridInterface[]
     */
    private $dataGridIds;

    /**
     * Construct
     *
     * @param ContainerInterface $container            
     * @param array $dataGridIds            
     */
    public function __construct (ContainerInterface $container, array $dataGridIds = array())
    {
        $this->container = $container;
        $this->dataGridIds = $dataGridIds;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Thrace\DataGridBundle\DataGrid\Provider.DataGridProviderInterface::get()
     */
    public function get ($name)
    {
        if (!isset($this->dataGridIds[$name])) {
            throw new \InvalidArgumentException(sprintf('The datagrid "%s" is not defined.', $name));
        }
        
        return $this->container->get($this->dataGridIds[$name]);
    }

    /**
     * (non-PHPdoc)
     *
     * @see Thrace\DataGridBundle\DataGrid\Provider.DataGridProviderInterface::has()
     */
    public function has ($name)
    {
        return isset($this->dataGridIds[$name]);
    }

}