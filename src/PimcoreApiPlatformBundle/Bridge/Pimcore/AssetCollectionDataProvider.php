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

namespace Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\RuntimeException;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Listing;
use Pimcore\Model\Element\ElementInterface;
use Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Extension\ListingCollectionExtensionInterface;
use Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Extension\ListingResultCollectionExtensionInterface;

final class AssetCollectionDataProvider extends AbstractCollectionDataProvider
{
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_subclass_of($resourceClass, Asset::class) || $resourceClass === Asset::class;
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $list = new Asset\Listing();

        if ($resourceClass !== Asset::class) {
            $empty = new $resourceClass;
            $list->addConditionParam('type = ?', $empty->getType());
        }

        if (!$list instanceof Asset\Listing) {
            throw new RuntimeException('Asset Listing could not be created');
        }

        return $this->loadData($list, $resourceClass, $operationName, $context);
    }
}
