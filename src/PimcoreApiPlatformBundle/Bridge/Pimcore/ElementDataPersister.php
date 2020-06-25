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

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\ElementInterface;

final class ElementDataPersister implements ContextAwareDataPersisterInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof ElementInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function persist($data, array $context = [])
    {
        /**
         * @var ElementInterface $data
         */
        $data->save();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data, array $context = [])
    {
        /**
         * @var ElementInterface $data
         */
        $data->delete();
    }
}
