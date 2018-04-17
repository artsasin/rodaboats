<?php
/**
 * Created by PhpStorm.
 * User: artsasin
 * Date: 07.04.18
 * Time: 13:49
 */

namespace AppBundle\DataProvider;

class OrderDataProvider
{
    const STATUS_CONFIRMED              = 1;
    const STATUS_CANCELLED              = 2;
    const STATUS_DELIVERED              = 3;
    const STATUS_CLOSED                 = 4;

    const CANCELLATION_REASON_NONE      = 0;
    const CANCELLATION_REASON_WEATHER   = 1;
    const CANCELLATION_REASON_NOSHOW    = 2;
    const CANCELLATION_REASON_REPAIR    = 3;
    const CANCELLATION_REASON_PRIVATE   = 4;

    const TYPE_REGULAR                  = 1;
    const TYPE_FISHING                  = 2;

    const PAYMENT_METHOD_CREDIT_CARD    = 'credit_card';
    const PAYMENT_METHOD_CASH           = 'cash';

    const EXTRA_SKIPPER                 = 'skipper';
    const EXTRA_DONUT                   = 'donut';
    const EXTRA_WATERSKI                = 'waterski';
    const EXTRA_BANANA                  = 'banana';
    const EXTRA_EASY_BREATH_MASK        = 'easy_breath_mask';
    const EXTRA_FISHING_GEAR            = 'fishing_gear';
    const EXTRA_ROMANTIC_PACKAGE        = 'romantic_package';
    const EXTRA_LUNCH                   = 'lunch';
    const EXTRA_FLYBOARD                = 'flyboard';

    /**
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_CONFIRMED  => "Confirmed",
            self::STATUS_CANCELLED  => "Cancelled",
            self::STATUS_CLOSED     => "Closed",
        ];
    }

    /**
     * @param $code
     * @return string
     */
    public static function statusName($code)
    {
        $statuses = self::statuses();
        return array_key_exists($code, $statuses) ? $statuses[$code] : 'UNKNOWN';
    }

    /**
     * @return array
     */
    public static function cancellationReasons()
    {
        return [
            self::CANCELLATION_REASON_NONE      => "None",
            self::CANCELLATION_REASON_NOSHOW    => "No show",
            self::CANCELLATION_REASON_REPAIR    => "Repairs",
            self::CANCELLATION_REASON_WEATHER   => "Weather",
            self::CANCELLATION_REASON_PRIVATE   => "Private",
        ];
    }

    /**
     * @return array
     */
    public static function types()
    {
        return [
            self::TYPE_REGULAR  => "Regular",
            self::TYPE_FISHING  => "Fishing",
        ];
    }

    /**
     * @param $code
     * @return string
     */
    public static function typeName($code)
    {
        $types = self::types();
        return array_key_exists($code, $types) ? $types[$code] : 'UNKNOWN';
    }

    /**
     * @return array
     */
    public static function paymentMethods()
    {
        return [
            self::PAYMENT_METHOD_CREDIT_CARD    => 'Credit Card',
            self::PAYMENT_METHOD_CASH           => 'Cash',
        ];
    }

    /**
     * @param $code
     * @return string
     */
    public static function paymentMethodName($code)
    {
        $paymentMethods = self::paymentMethods();
        return array_key_exists($code, $paymentMethods) ? $paymentMethods[$code] : 'UNKNOWN';
    }

    /**
     * @param $code
     * @return string
     */
    public static function cancellationReasonName($code)
    {
        $cancellationReasons = self::cancellationReasons();
        return array_key_exists($code, $cancellationReasons) ? $cancellationReasons[$code] : 'UNKNOWN';
    }

    /**
     * @return array
     */
    public static function extras()
    {
        return [
            self::EXTRA_SKIPPER             => 'Skipper',
            self::EXTRA_DONUT               => 'Donut',
            self::EXTRA_WATERSKI            => 'Waterski',
            self::EXTRA_BANANA              => 'Banana',
            self::EXTRA_EASY_BREATH_MASK    => 'Easy Breath Mask',
            self::EXTRA_FISHING_GEAR        => 'Fisihing gear',
            self::EXTRA_ROMANTIC_PACKAGE    => 'Romantic package',
            self::EXTRA_LUNCH               => 'Lunch',
            self::EXTRA_FLYBOARD            => 'Flyboard',
        ];
    }

    public static function extrasAbbrevations()
    {
        return [
            self::EXTRA_SKIPPER             => 'SK',
            self::EXTRA_DONUT               => 'DN',
            self::EXTRA_WATERSKI            => 'WSKI',
            self::EXTRA_BANANA              => 'BAN',
            self::EXTRA_EASY_BREATH_MASK    => 'ESY_BM',
            self::EXTRA_FISHING_GEAR        => 'FGR',
            self::EXTRA_ROMANTIC_PACKAGE    => 'RPACK',
            self::EXTRA_LUNCH               => 'LNCH',
            self::EXTRA_FLYBOARD            => 'FBD',
        ];
    }

    /**
     * @param $code
     * @return string
     */
    public static function extraName($code)
    {
        $extras = self::extras();
        return array_key_exists($code, $extras) ? $extras[$code] : 'UNKNOWN';
    }
}