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

    const COUNTRY_AUSTRIA = 'AUT';
    const COUNTRY_AZERBAIJAN = 'AZE';
    const COUNTRY_ALBANIA = 'ALB';
    const COUNTRY_ANDORRA = 'AND';
    const COUNTRY_ARMENIA = 'ARM';
    const COUNTRY_BELARUS = 'BLR';
    const COUNTRY_BELGIUM = 'BEL';
    const COUNTRY_BULGARIA = 'BGR';
    const COUNTRY_BOSNIA_AND_HERZEGOVINA = 'BIH';
    const COUNTRY_VATICAN = 'VAT';
    const COUNTRY_UNITED_KINGDOM = 'GBR';
    const COUNTRY_HUNGARY = 'HUN';
    const COUNTRY_GERMANY = 'DEU';
    const COUNTRY_GREECE = 'GRC';
    const COUNTRY_GEORGIA = 'GEO';
    const COUNTRY_DENMARK = 'DNK';
    const COUNTRY_IRELAND = 'IRL';
    const COUNTRY_ICELAND = 'ISL';
    const COUNTRY_SPAIN = 'ESP';
    const COUNTRY_ITALY = 'ITA';
    const COUNTRY_CYPRUS = 'CYP';
    const COUNTRY_LATVIA = 'LVA';
    const COUNTRY_LITHUANIA = 'LTU';
    const COUNTRY_LIECHTENSTEIN = 'LIE';
    const COUNTRY_LUXEMBOURG = 'LUX';
    const COUNTRY_MALTA = 'MLT';
    const COUNTRY_MOLDOVA = 'MDA';
    const COUNTRY_MONACO = 'MCO';
    const COUNTRY_NETHERLANDS = 'NLD';
    const COUNTRY_NORWAY = 'NOR';
    const COUNTRY_POLAND = 'POL';
    const COUNTRY_PORTUGAL = 'PRT';
    const COUNTRY_MACEDONIA = 'MKD';
    const COUNTRY_RUSSIA = 'RUS';
    const COUNTRY_ROMANIA = 'ROU';
    const COUNTRY_SAN_MARINO = 'SMR';
    const COUNTRY_SERBIA = 'SRB';
    const COUNTRY_SLOVAKIA = 'SVK';
    const COUNTRY_SLOVENIA = 'SVN';
    const COUNTRY_TURKEY = 'TUR';
    const COUNTRY_UKRAINE = 'UKR';
    const COUNTRY_FINLAND = 'FIN';
    const COUNTRY_FRANCE = 'FRA';
    const COUNTRY_CROATIA = 'HRV';
    const COUNTRY_MONTENEGRO = 'MNE';
    const COUNTRY_CZECH_REPUBLIC = 'CZE';
    const COUNTRY_SWITZERLAND = 'CHE';
    const COUNTRY_SWEDEN = 'SWE';
    const COUNTRY_ESTONIA = 'EST';

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
            self::COUNTRY_ALBANIA                   => 'Albania',
            self::COUNTRY_ANDORRA                   => 'Andorra',
            self::COUNTRY_ARMENIA                   => 'Armenia',
            self::COUNTRY_AUSTRIA                   => 'Austria',
            self::COUNTRY_AZERBAIJAN                => 'Azerbaijan',
            self::COUNTRY_BELARUS                   => 'Belarus',
            self::COUNTRY_BELGIUM                   => 'Belgium',
            self::COUNTRY_BOSNIA_AND_HERZEGOVINA    => 'Bosnia and Herzegovina',
            self::COUNTRY_BULGARIA                  => 'Bulgaria',
            self::COUNTRY_CROATIA                   => 'Croatia',
            self::COUNTRY_CYPRUS                    => 'Cyprus',
            self::COUNTRY_CZECH_REPUBLIC            => 'Czech Republic',
            self::COUNTRY_DENMARK                   => 'Denmark',
            self::COUNTRY_ESTONIA                   => 'Estonia',
            self::COUNTRY_FINLAND                   => 'Finland',
            self::COUNTRY_FRANCE                    => 'France',
            self::COUNTRY_GEORGIA                   => 'Georgia',
            self::COUNTRY_GERMANY                   => 'Germany',
            self::COUNTRY_UNITED_KINGDOM            => 'Great Britain',
            self::COUNTRY_GREECE                    => 'Greece',
            self::COUNTRY_HUNGARY                   => 'Hungary',
            self::COUNTRY_ICELAND                   => 'Iceland',
            self::COUNTRY_IRELAND                   => 'Ireland',
            self::COUNTRY_ITALY                     => 'Italy',
            self::COUNTRY_LATVIA                    => 'Latvia',
            self::COUNTRY_LIECHTENSTEIN             => 'Liechtenstein',
            self::COUNTRY_LITHUANIA                 => 'Lithuania',
            self::COUNTRY_LUXEMBOURG                => 'Luxembourg',
            self::COUNTRY_MACEDONIA                 => 'Macedonia',
            self::COUNTRY_MALTA                     => 'Malta',
            self::COUNTRY_MOLDOVA                   => 'Moldova',
            self::COUNTRY_MONACO                    => 'Monaco',
            self::COUNTRY_MONTENEGRO                => 'Montenegro',
            self::COUNTRY_NETHERLANDS               => 'Netherlands',
            self::COUNTRY_NORWAY                    => 'Norway',
            self::COUNTRY_POLAND                    => 'Poland',
            self::COUNTRY_PORTUGAL                  => 'Portugal',
            self::COUNTRY_ROMANIA                   => 'Romania',
            self::COUNTRY_RUSSIA                    => 'Russia',
            self::COUNTRY_SAN_MARINO                => 'San Marino',
            self::COUNTRY_SERBIA                    => 'Serbia',
            self::COUNTRY_SLOVAKIA                  => 'Slovakia',
            self::COUNTRY_SLOVENIA                  => 'Slovenia',
            self::COUNTRY_SPAIN                     => 'Spain',
            self::COUNTRY_SWEDEN                    => 'Sweden',
            self::COUNTRY_SWITZERLAND               => 'Switzerland',
            self::COUNTRY_TURKEY                    => 'Turkey',
            self::COUNTRY_UKRAINE                   => 'Ukraine',
            self::COUNTRY_VATICAN                   => 'Vatican',
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