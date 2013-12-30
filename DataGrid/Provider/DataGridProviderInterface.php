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

/**
 * Interface implemented by a ContainerAwareProvider class.
 *
 * @author Nikolay Georgiev <azazen09@gmail.com>
 * @since 1.0
 */
interface DataGridProviderInterface
{

    /**
     * Retrieves a datagrid by its name
     *
     * @param string $name            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     * @throws \InvalidArgumentException if the datagrid does not exists
     */
    function get ($name);

    /**
     * Checks whether a datagrid exists in this provider
     *
     * @param string $name            
     * @return bool
     */
    function has ($name);
}
