<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 23.04.18
 * Time: 15:08
 */

namespace AppBundle\DataProvider;


class BoatDataProvider
{
    const MAINTENANCE_ENGINE_OIL_CHECK = 'engine_oil_check';
    const MAINTENANCE_SHAFT_OIL_CHECK = 'shaft_oil_check';
    const MAINTENANCE_PETROL_FILTER_CHANGE = 'petrol_filter_change';
    const MAINTENANCE_BATTERY_CHECK = 'battery_check';
    const MAINTENANCE_SPARK_CHANGE = 'spark_change';
    const MAINTENANCE_IMPELLOR_CHANGE = 'impellor_change';
    const MAINTENANCE_OIL_FILTER_CHANGE = 'oil_filter_change';
    const MAINTENANCE_STEERING_WHEEL_GREASE = 'steering_wheel_grease';
    const MAINTENANCE_THROTTLE_SHIFTING_GREASE = 'throttle_shifting_grease';
    const MAINTENANCE_ENGINE_CLEANING = 'engine_cleaning';
    const MAINTENANCE_PROPELLER_CHANGE = 'propeller_change';
    const MAINTENANCE_ENGINE_HOURS = 'engine_hours';
    const MAINTENANCE_ENGINE_HOURS_CHECK = 'engine_hours_check';
}