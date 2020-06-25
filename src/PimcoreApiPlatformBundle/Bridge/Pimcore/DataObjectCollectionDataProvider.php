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

use ApiPlatform\Core\Exception\RuntimeException;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Listing;

final class DataObjectCollectionDataProvider extends AbstractCollectionDataProvider
{
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_subclass_of($resourceClass, Concrete::class) || $resourceClass === Concrete::class;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        if ($resourceClass === Concrete::class) {
            $list = new Listing();
        }
        else {
            $listClass = $resourceClass.'\\Listing';
            $list = new $listClass;
        }

        if (!$list instanceof Listing) {
            throw new RuntimeException('Data Object Listing could not be created');
        }

        return $this->loadData($list, $resourceClass, $operationName, $context);
    }
}
