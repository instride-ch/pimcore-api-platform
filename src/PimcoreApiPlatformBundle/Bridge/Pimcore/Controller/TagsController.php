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

namespace Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Controller;

use Pimcore\Model\Element\Tag as Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagsController extends AbstractController
{
    public function getTagsForAssets(int $id)
    {
        return Tag::getTagsForElement('asset', $id);
    }

    public function getTagsForObjects(int $id)
    {
        return Tag::getTagsForElement('object', $id);
    }

    public function getTagsForDocuments(int $id)
    {
        return Tag::getTagsForElement('document', $id);
    }
}
