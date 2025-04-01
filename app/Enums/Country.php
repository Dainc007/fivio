<?php


namespace App\Enums;

enum Country: string
{
    case AUSTRIA = 'AT';
    case BELGIUM = 'BE';
    case BULGARIA = 'BG';
    case CROATIA = 'HR';
    case CYPRUS = 'CY';
    case CZECH_REPUBLIC = 'CZ';
    case DENMARK = 'DK';
    case ESTONIA = 'EE';
    case FINLAND = 'FI';
    case FRANCE = 'FR';
    case GERMANY = 'DE';
    case GREECE = 'GR';
    case HUNGARY = 'HU';
    case IRELAND = 'IE';
    case ITALY = 'IT';
    case LATVIA = 'LV';
    case LITHUANIA = 'LT';
    case LUXEMBOURG = 'LU';
    case MALTA = 'MT';
    case NETHERLANDS = 'NL';
    case POLAND = 'PL';
    case PORTUGAL = 'PT';
    case ROMANIA = 'RO';
    case SLOVAKIA = 'SK';
    case SLOVENIA = 'SI';
    case SPAIN = 'ES';
    case SWEDEN = 'SE';

    public function getLabel(): string
    {
        return match ($this) {
            self::AUSTRIA => 'Austria',
            self::BELGIUM => 'Belgium',
            self::BULGARIA => 'Bulgaria',
            self::CROATIA => 'Croatia',
            self::CYPRUS => 'Cyprus',
            self::CZECH_REPUBLIC => 'Czech Republic',
            self::DENMARK => 'Denmark',
            self::ESTONIA => 'Estonia',
            self::FINLAND => 'Finland',
            self::FRANCE => 'France',
            self::GERMANY => 'Germany',
            self::GREECE => 'Greece',
            self::HUNGARY => 'Hungary',
            self::IRELAND => 'Ireland',
            self::ITALY => 'Italy',
            self::LATVIA => 'Latvia',
            self::LITHUANIA => 'Lithuania',
            self::LUXEMBOURG => 'Luxembourg',
            self::MALTA => 'Malta',
            self::NETHERLANDS => 'Netherlands',
            self::POLAND => 'Poland',
            self::PORTUGAL => 'Portugal',
            self::ROMANIA => 'Romania',
            self::SLOVAKIA => 'Slovakia',
            self::SLOVENIA => 'Slovenia',
            self::SPAIN => 'Spain',
            self::SWEDEN => 'Sweden',
        };
    }

    public static function getLabels(): array
    {
        return array_reduce(
            self::cases(),
            function (array $labels, self $country) {
                $labels[$country->value] = $country->getLabel();
                return $labels;
            },
            []
        );
    }
}
