<?php
/*
 * This file is part of ThraceDataGridBundle
 *
 * (c) Nikolay Georgiev <azazen09@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\DataGridBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Thrace\DataGridBundle\DependencyInjection\Compiler\DataGridCompilerPass;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle class
 *
 * @author Nikolay Georgiev <azazen09@gmail.com>
 * @since 1.0
 */
class ThraceDataGridBundle extends Bundle
{
    public function build (ContainerBuilder $container)
    {
        parent::build($container);
    
        $container->addCompilerPass(new DataGridCompilerPass());
    }
}
