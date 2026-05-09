<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\rest\actions;
use Travelpayouts\Vendor\Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Class EditShortcodeAction
 * @package Travelpayouts\components\rest
 * На будущее, можно будет добавить редактирование уже существующих шорткодов
 */
class EditShortcodeAction extends PreviewShortcodeAction
{
    public function run()
    {
        $shortcode = \Travelpayouts::getInstance()->request->getInputParam('shortcode');
        if (!$shortcode) {
            throw new NotFoundResourceException('Shortcode parameter is required');
        }

        $shortcodeModel = $this->findModelFromShortcodeList($shortcode);

        if($shortcodeModel){
            return $shortcodeModel->toArray([null], ['id', 'label', 'fields', 'extraData']);
        }
        throw new NotFoundResourceException('Shortcode not found');
    }
}