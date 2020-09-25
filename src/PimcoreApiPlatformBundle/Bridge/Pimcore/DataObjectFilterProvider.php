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

use ApiPlatform\Core\DataProvider\DenormalizedIdentifiersAwareItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Pimcore\Model\DataObject\Concrete;

final class DataObjectFilterProvider implements DenormalizedIdentifiersAwareItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_subclass_of($resourceClass, Concrete::class);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        if (isset($id['id'])) {
            return $resourceClass::getById($id['id']);
        }

        if (count($id) === 1) {
            $key = array_keys($id);
            $getBy = "getBy".ucfirst($key[0]);

            return $resourceClass::$getBy($id[$key[0]], 1);
        }

        $condition = [];
        $conditionValues = [];

        foreach ($id as $key => $value) {
            $condition[] = '`'.$key.'` = ?';
            $conditionValues[] = $value;
        }

        /**
         * @var \Pimcore\Model\DataObject\Listing\Concrete $list
         */
        $list = new $resourceClass.'\\Listing';
        $list->setUnpublished(true);
        $list->setCondition(implode(' AND ', $condition), $conditionValues);
        $list->setObjectTypes([
            Concrete::OBJECT_TYPE_VARIANT,
            Concrete::OBJECT_TYPE_OBJECT,
        ]);
        $list->load();

        return $list->current();
    }
}
