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

use Pimcore\Model\Element\Note;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotesController extends AbstractController
{
    public function getNotesForAssets(int $id)
    {
        $noteList = new Note\Listing();
        $noteList->addConditionParam('ctype = \'asset\'');
        $noteList->addConditionParam('cid = ?', $id);
        $noteList->setOrderKey('date');
        $noteList->setOrder('desc');

        return $noteList->getNotes();
    }

    public function getNotesForObjects(int $id)
    {
        $noteList = new Note\Listing();
        $noteList->addConditionParam('ctype = \'object\'');
        $noteList->addConditionParam('cid = ?', $id);
        $noteList->setOrderKey('date');
        $noteList->setOrder('desc');

        return $noteList->getNotes();
    }

    public function getNotesForDocuments(int $id)
    {
        $noteList = new Note\Listing();
        $noteList->addConditionParam('ctype = \'document\'');
        $noteList->addConditionParam('cid = ?', $id);
        $noteList->setOrderKey('date');
        $noteList->setOrder('desc');

        return $noteList->getNotes();
    }
}
