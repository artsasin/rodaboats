<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 17.04.18
 * Time: 10:52
 */

namespace AppBundle\DataProvider;


class CustomerDataProvider
{
    const LANG_DE = 'de';
    const LANG_EN = 'en';
    const LANG_ES = 'es';
    const LANG_FR = 'fr';

    const COUNTRY_ARGENTINA = 'ARG';
    const COUNTRY_AUSTRALIA = 'AUS';
    const COUNTRY_AUSTRIA = 'AUT';

    const COUNTRY_BELARUS = 'BLR';
    const COUNTRY_BELGIUM = 'BEL';
    const COUNTRY_BOSNIA_AND_HERZEGOVINA = 'BIH';
    const COUNTRY_BRAZIL = 'BRA';
    const COUNTRY_BULGARIA = 'BGR';

    const COUNTRY_CANADA = 'CAN';
    const COUNTRY_CHILE = 'CHL';
    const COUNTRY_CHINA = 'CHN';
    const COUNTRY_COLOMBIA = 'COL';
    const COUNTRY_COSTA_RICA = 'CRI';
    const COUNTRY_CROATIA = 'HRV';
    const COUNTRY_CUBA = 'CUB';
    const COUNTRY_CYPRUS = 'CYP';
    const COUNTRY_CZECH_REPUBLIC = 'CZE';

    const COUNTRY_DENMARK = 'DNK';

    const COUNTRY_ECUADOR = 'ECU';
    const COUNTRY_EGYPT = 'EGY';
    const COUNTRY_ESTONIA = 'EST';

    const COUNTRY_FINLAND = 'FIN';
    const COUNTRY_FRANCE = 'FRA';

    const COUNTRY_GERMANY = 'DEU';
    const COUNTRY_GREECE = 'GRC';

    const COUNTRY_HUNGARY = 'HUN';

    const COUNTRY_ICELAND = 'ISL';
    const COUNTRY_INDIA = 'IND';
    const COUNTRY_INDONESIA = 'IDN';
    const COUNTRY_IRELAND = 'IRL';
    const COUNTRY_ISRAEL = 'ISR';
    const COUNTRY_ITALY = 'ITA';

    const COUNTRY_JAPAN = 'JPN';

    const COUNTRY_SOUTH_KOREA = 'KOR';

    const COUNTRY_LATVIA = 'LVA';
    const COUNTRY_LEBANON = 'LBN';
    const COUNTRY_LIECHTENSTEIN = 'LIE';
    const COUNTRY_LITHUANIA = 'LTU';
    const COUNTRY_LUXEMBOURG = 'LUX';

    const COUNTRY_MACEDONIA = 'MKD';
    const COUNTRY_MALTA = 'MLT';
    const COUNTRY_MEXICO = 'MEX';
    const COUNTRY_MOLDOVA = 'MDA';
    const COUNTRY_MONACO = 'MCO';
    const COUNTRY_MONTENEGRO = 'MNE';
    const COUNTRY_MOROCCO = 'MAR';

    const COUNTRY_NETHERLANDS = 'NLD';
    const COUNTRY_NEW_ZEALAND = 'NZL';
    const COUNTRY_NICARAGUA = 'NIC';
    const COUNTRY_NORWAY = 'NOR';

    const COUNTRY_OTHER = 'OTR';

    const COUNTRY_PHILLIPINES = 'PHL';
    const COUNTRY_POLAND = 'POL';
    const COUNTRY_PORTUGAL = 'PRT';

    const COUNTRY_ROMANIA = 'ROU';
    const COUNTRY_RUSSIA = 'RUS';

    const COUNTRY_SERBIA = 'SRB';
    const COUNTRY_SLOVAKIA = 'SVK';
    const COUNTRY_SLOVENIA = 'SVN';
    const COUNTRY_SOUTH_AFRICA = 'ZAF';
    const COUNTRY_SPAIN = 'ESP';
    const COUNTRY_SWEDEN = 'SWE';
    const COUNTRY_SWITZERLAND = 'CHE';

    const COUNTRY_TURKEY = 'TUR';

    const COUNTRY_UKRAINE = 'UKR';
    const COUNTRY_UNITED_ARAB_EMIRATES = 'ARE';
    const COUNTRY_UNITED_KINGDOM = 'GBR';
    const COUNTRY_UNITED_STATES = 'USA';

    const COUNTRY_VENEZUELA = 'VEN';

    /**
     * @return string[]
     */
    public static function languages()
    {
        return [
            self::LANG_DE   => 'German',
            self::LANG_EN   => 'English',
            self::LANG_ES   => 'Spanish',
            self::LANG_FR   => 'French'
        ];
    }

    /**
     * @param string $code
     * @return string
     */
    public static function languageName($code)
    {
        $languages = self::languages();
        return array_key_exists($code, $languages) ? $languages[$code] : 'UNKNOWN';
    }

    /**
     * @return string[]
     */
    public static function countries()
    {
        return [
            self::COUNTRY_ARGENTINA                 => 'Argentina',
            self::COUNTRY_AUSTRALIA                 => 'Australia',
            self::COUNTRY_AUSTRIA                   => 'Austria',
            self::COUNTRY_BELARUS                   => 'Belarus',
            self::COUNTRY_BELGIUM                   => 'Belgium',
            self::COUNTRY_BOSNIA_AND_HERZEGOVINA    => 'Bosnia and Herzegovina',
            self::COUNTRY_BRAZIL                    => 'Brazil',
            self::COUNTRY_BULGARIA                  => 'Bulgaria',
            self::COUNTRY_CANADA                    => 'Canada',
            self::COUNTRY_CHILE                     => 'Chile',
            self::COUNTRY_CHINA                     => 'China',
            self::COUNTRY_COLOMBIA                  => 'Colombia',
            self::COUNTRY_COSTA_RICA                => 'Costa Rica',
            self::COUNTRY_CROATIA                   => 'Croatia',
            self::COUNTRY_CUBA                      => 'Cuba',
            self::COUNTRY_CYPRUS                    => 'Cyprus',
            self::COUNTRY_CZECH_REPUBLIC            => 'Czech Republic',
            self::COUNTRY_DENMARK                   => 'Denmark',
            self::COUNTRY_ECUADOR                   => 'Ecuador',
            self::COUNTRY_EGYPT                     => 'Egypt',
            self::COUNTRY_ESTONIA                   => 'Estonia',
            self::COUNTRY_FINLAND                   => 'Finland',
            self::COUNTRY_FRANCE                    => 'France',
            self::COUNTRY_GERMANY                   => 'Germany',
            self::COUNTRY_GREECE                    => 'Greece',
            self::COUNTRY_HUNGARY                   => 'Hungary',
            self::COUNTRY_ICELAND                   => 'Iceland',
            self::COUNTRY_INDIA                     => 'India',
            self::COUNTRY_INDONESIA                 => 'Indonesia',
            self::COUNTRY_IRELAND                   => 'Ireland',
            self::COUNTRY_ISRAEL                    => 'Israel',
            self::COUNTRY_ITALY                     => 'Italy',
            self::COUNTRY_JAPAN                     => 'Japan',
            self::COUNTRY_SOUTH_KOREA               => 'Korea Republic',
            self::COUNTRY_LATVIA                    => 'Latvia',
            self::COUNTRY_LEBANON                   => 'Lebanon',
            self::COUNTRY_LIECHTENSTEIN             => 'Liechtenstein',
            self::COUNTRY_LITHUANIA                 => 'Lithuania',
            self::COUNTRY_LUXEMBOURG                => 'Luxembourg',
            self::COUNTRY_MACEDONIA                 => 'Macedonia',
            self::COUNTRY_MALTA                     => 'Malta',
            self::COUNTRY_MEXICO                    => 'Mexico',
            self::COUNTRY_MOLDOVA                   => 'Moldova',
            self::COUNTRY_MONACO                    => 'Monaco',
            self::COUNTRY_MONTENEGRO                => 'Montenegro',
            self::COUNTRY_MOROCCO                   => 'Morocco',
            self::COUNTRY_NETHERLANDS               => 'Netherlands',
            self::COUNTRY_NEW_ZEALAND               => 'New Zealand',
            self::COUNTRY_NICARAGUA                 => 'Nicaragua',
            self::COUNTRY_NORWAY                    => 'Norway',
            self::COUNTRY_OTHER                     => 'Other',
            self::COUNTRY_PHILLIPINES               => 'Philippines',
            self::COUNTRY_POLAND                    => 'Poland',
            self::COUNTRY_PORTUGAL                  => 'Portugal',
            self::COUNTRY_ROMANIA                   => 'Romania',
            self::COUNTRY_RUSSIA                    => 'Russia',
            self::COUNTRY_SERBIA                    => 'Serbia',
            self::COUNTRY_SLOVAKIA                  => 'Slovakia',
            self::COUNTRY_SLOVENIA                  => 'Slovenia',
            self::COUNTRY_SOUTH_AFRICA              => 'South Africa',
            self::COUNTRY_SPAIN                     => 'Spain',
            self::COUNTRY_SWEDEN                    => 'Sweden',
            self::COUNTRY_SWITZERLAND               => 'Switzerland',
            self::COUNTRY_TURKEY                    => 'Turkey',
            self::COUNTRY_UKRAINE                   => 'Ukraine',
            self::COUNTRY_UNITED_ARAB_EMIRATES      => 'United Arab Emirates',
            self::COUNTRY_UNITED_KINGDOM            => 'United Kingdom',
            self::COUNTRY_UNITED_STATES             => 'United States',
            self::COUNTRY_VENEZUELA                 => 'Venezuela'
        ];
    }

    /**
     * @param string $code
     * @return string
     */
    public static function countryName($code)
    {
        $countries = self::countries();
        return (array_key_exists($code, $countries)) ? $countries[$code] : 'UNKNOWN';
    }
}