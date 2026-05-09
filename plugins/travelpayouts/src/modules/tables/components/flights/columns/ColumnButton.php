<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\columns;
use Travelpayouts\Vendor\Carbon\Carbon;
use DateTimeInterface;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\Model;
use Travelpayouts\components\tables\enrichment\UrlHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\account\AccountForm;

class ColumnButton extends \Travelpayouts\components\grid\columns\ColumnButton
{
    /**
     * @var string|null|callable
     */
    protected $origin;
    /**
     * @var string|null|callable
     */
    protected $destination;
    /**
     * @var string|null
     */
    protected $currency;
    /**
     * @var string|null
     */
    protected $locale;
    /**
     * @var string|boolean|null
     */
    protected $oneWay;
    /**
     * @var string|null
     */
    protected $subid;

    /**
     * @var callable(Model): DateTimeInterface|string|int
     */
    protected $departDate;
    /**
     * @var callable(Model): DateTimeInterface|string|int
     */
    protected $returnDate;

    /**
     * @var string|null
     */
    protected $linkMarker;

    /**
     * @var string
     */
    protected $host;

    /**
     * @Inject
     * @var AccountForm
     */
    protected $accountSettings;

    public function getButtonUrl($model): string
    {
        return UrlHelper::buildMediaUrl([
            'p' => UrlHelper::FLIGHT_P,
            'marker' => !empty($this->accountSettings->api_marker)
                ? $this->getMarker()
                : null,
            'u' => UrlHelper::buildUrl($this->host, $this->getButtonUrlParams($model)),
        ]);
    }

    public function init()
    {
        parent::init();
        $this->host = !empty($this->accountSettings->flights_domain)
            ? $this->accountSettings->flights_domain
            : $this->globalSettings->getFlightHost();
    }

    /**
     * @param $model
     * @return array
     */
    protected function getButtonUrlParams($model): array
    {
        $departureDate = $this->callableToDate($model, $this->departDate);
        $returnDate = $this->callableToDate($model, $this->returnDate);

        return [
            'origin_iata' => $this->getOrigin($model),
            'destination_iata' => $this->getDestination($model),
            'currency' => $this->currency,
            'locale' => $this->locale,
            'depart_date' => $departureDate ? $departureDate->format('Y-m-d') : null,
            'return_date' => $returnDate ? $returnDate->format('Y-m-d') : null,
            'with_request' => $this->getWithRequestParam() ? 'true' : null,
            'one_way' => $this->getOneWay() ? 'true' : null,
            'trs' => $this->accountSettings->platform,
        ];
    }

    protected function getWithRequestParam(): bool
    {
        return $this->globalSettings->flights_after_url === 'results';
    }

    protected function getOneWay(): bool
    {
        return StringHelper::toBoolean($this->oneWay);
    }

    /**
     * @param $model
     * @param callable|int|string|DateTimeInterface $value
     * @return Carbon|null
     */
    protected function callableToDate($model, $value): ?Carbon
    {
        if ($value instanceof DateTimeInterface && !is_string($value) && !is_int($value)) {
            return $this->parseDate($value);
        }

        if (is_callable($value) && $callableResult = $value($model)) {
            return $this->parseDate($callableResult);
        }
        return null;
    }

    /**
     * @param $value
     * @return Carbon
     */
    public function parseDate($value): ?Carbon
    {
        if (!$value instanceof DateTimeInterface && !is_string($value) && !is_int($value)) {
            return null;
        }
        return $value instanceof \DateTimeInterface
            ? Carbon::parse($value)
            : Carbon::parse((string)$value);
    }

    protected function getMarker(): string
    {
        return UrlHelper::get_marker(
            $this->accountSettings->api_marker,
            $this->subid,
            $this->linkMarker
        );
    }

    protected function getOrigin($model): ?string
    {
        $attribute = $this->origin;

        return !is_string($attribute) && is_callable($attribute) ? $attribute($model) : $attribute;
    }

    protected function getDestination($model): ?string
    {
        if (!empty($model->destination)) {
            return $model->destination;
        }

        $attribute = $this->destination;
        return !is_string($attribute) && is_callable($attribute) ? $attribute($model) : $attribute;
    }

}
