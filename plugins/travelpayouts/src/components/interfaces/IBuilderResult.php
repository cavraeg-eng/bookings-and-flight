<?php

namespace Travelpayouts\components\interfaces;

interface IBuilderResult
{
    /**
     * Каждый строитель должен вернуть результат
     *
     * @return mixed
     */
    public function getResult();
}