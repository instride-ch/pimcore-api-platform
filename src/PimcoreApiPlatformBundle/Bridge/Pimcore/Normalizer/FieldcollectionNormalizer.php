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

namespace Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Normalizer;

use ApiPlatform\Core\Util\ClassInfoTrait;
use Pimcore\Model\DataObject\Fieldcollection;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FieldcollectionNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    use ClassInfoTrait;

    public function normalize($object, $format = null, array $context = array()): array
    {
        $items = [];

        foreach ($object->getItems() as $item) {
            $item = $this->normalizer->normalize($item, $format, $context);

            $items[] = $item;
        }

        return $items;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Fieldcollection;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
