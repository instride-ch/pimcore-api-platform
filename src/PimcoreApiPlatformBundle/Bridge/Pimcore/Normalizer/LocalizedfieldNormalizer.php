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

use Pimcore\Model\DataObject\Localizedfield;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LocalizedfieldNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function normalize($object, $format = null, array $context = array()): array
    {
        /**
         * @var Localizedfield $object
         */
        return $object->getInternalData(true);
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Localizedfield;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
