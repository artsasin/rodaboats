<?php

namespace AppBundle\DataProvider;


class DateTimeProvider
{
    /**
     * @param \DateTime $dateTime
     * @return \DateTime
     */
    public static function utc(\DateTime $dateTime)
    {
        $result = clone $dateTime;
        $result->setTimezone(new \DateTimeZone('UTC'));
        $result->setTime(
            intval($dateTime->format('G')),
            intval($dateTime->format('i')),
            intval($dateTime->format('s'))
        );

        return $result;
    }

    /**
     * @param \DateTime $dateTime
     * @return int
     */
    public static function utcTimestamp(\DateTime $dateTime)
    {
        $utc = self::utc($dateTime);
        return $utc->getTimestamp();
    }
}