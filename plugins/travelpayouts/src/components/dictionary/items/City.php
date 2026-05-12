<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\dictionary\items;

use Travelpayouts\components\dictionary\TravelpayoutsApiData;

class City extends TravelpayoutsApiItem
{
    /**
     * @var string[]
     */
    public $cases;
    public $name;

    public function getName($case = false)
    {
        if ($case) {
            $case_path = "cases.{$case}";
            $name = $this->dataDot->get($case_path);
            if ($name !== null)
                return $name;
        }
        return $this->dataDot->get('name');
    }

    /**
     * @return string|null
     */
    public function getCaseNominative(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getCaseGenitive(): ?string
    {
        return $this->getValueByCase(TravelpayoutsApiData::CASE_GENITIVE);
    }

    /**
     * @return string|null
     */
    public function getCaseAccusative(): ?string
    {
        return $this->getValueByCase(TravelpayoutsApiData::CASE_ACCUSATIVE);
    }

    /**
     * @return string|null
     */
    public function getCaseDative(): ?string
    {
        return $this->getValueByCase(TravelpayoutsApiData::CASE_DATIVE);
    }

    /**
     * @return string|null
     */
    public function getCaseInstrumental(): ?string
    {
        return $this->getValueByCase(TravelpayoutsApiData::CASE_INSTRUMENTAL);
    }

    /**
     * @return string|null
     */
    public function getCasePrepositional(): ?string
    {
        return $this->getValueByCase(TravelpayoutsApiData::CASE_PREPOSITIONAL);
    }

    /**
     * @param string $case
     * @return string|null
     */
    protected function getValueByCase(string $case): ?string
    {
        return is_array($this->cases) && isset($this->cases[$case])
            ? $this->cases[$case]
            : null;
    }

}
