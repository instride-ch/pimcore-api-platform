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

namespace Wvision\Bundle\PimcoreApiPlatformBundle;

use ApiPlatform\Core\Bridge\Symfony\Bundle\ApiPlatformBundle;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wvision\Bundle\PimcoreApiPlatformBundle\DependencyInjection\CompilerPass\SerializerLoaderPass;

class PimcoreApiPlatformBundle extends AbstractPimcoreBundle implements DependentBundleInterface
{
    use PackageVersionTrait;

    public static function registerDependentBundles(BundleCollection $collection)
    {
        $collection->addBundle(new ApiPlatformBundle());
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SerializerLoaderPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getNiceName()
    {
        return 'API Platform';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Pimcore API Platform integrates API Platform into Pimcore';
    }

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackageName()
    {
        return 'w-vision/data-definitions';
    }
}

