<?php
/**
 * Pimcore Api Platform Bundle
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\PimcoreApiPlatformBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Serializer\ClassDefinitionSerializerLoader;

class SerializerLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $chainLoader = $container->findDefinition('serializer.mapping.chain_loader');
        $serializerLoaders = $chainLoader->getArgument(0);

        $pimcoreLoader = new Definition(
            ClassDefinitionSerializerLoader::class
        );
        $pimcoreLoader->setPublic(false);

        $serializerLoaders[] = $pimcoreLoader;

        $chainLoader->replaceArgument(0, $serializerLoaders);
    }
}
