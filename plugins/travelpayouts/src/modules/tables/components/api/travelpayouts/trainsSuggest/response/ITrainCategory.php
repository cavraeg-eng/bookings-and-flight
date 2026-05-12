<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\trainsSuggest\response;

interface ITrainCategory
{
    /**
     * @return string
     */
    public function getLabel(): string;

    public function getValue(): ?float;
}
