<?php


namespace CaptainKant\Generics\Core;


use DateInterval;
use DateTime;
use Exception;

/**
 * @noinspection PhpUnused
 */

class GenericDateTime extends DateTime
{

    static $mockedNow = null;
    static $wasBooted = false;
    private $wasInstanciated = false;

    public function __construct($param)
    {
        if (!self::$wasBooted) {
            if (file_exists('/etc/timezone')) {
                date_default_timezone_set(rtrim(file_get_contents('/etc/timezone')));
            }
            self::$wasBooted = true;
        }

        if ($param == 'now' && self::$mockedNow) {
            $param = (string)self::$mockedNow->getDatetime();
        }


        if ($param instanceof DateTime) {
            $param = $param->format('Y-m-d H:i:s');
        }
        if (is_string($param) &&
            preg_match('/([0-9]{2})\/([0-9]{2})\/([0-9]{4}) ([0-9]{2}):([0-9]{2})/', $param, $tabReg)) {
            $param = $tabReg[3] . '-' . $tabReg[2] . '-' . $tabReg[1] . ' ' . $tabReg[4] . ':' . $tabReg[5] . ':00';
        } else if (is_string($param) &&
            preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $param, $tabReg)
        ) {
            $param = $tabReg[3] . '-' . $tabReg[2] . '-' . $tabReg[1] . ' 00:00:00';
        } else if (is_string($param) &&
            preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})T([0-9]{2}):([0-9]{2}):(.*)$/', $param, $tabReg)
        ) {
            $param = $tabReg[3] . '-' . $tabReg[2] . '-' . $tabReg[1] . ' ' . $tabReg[4] . ':' . $tabReg[5] . ':00';
        } else if (is_int($param) //timestamp
        ) {
            $param = date("c", $param);
        } else if (is_string($param) && (string)(int)$param == $param) {
            $param = date("c", $param);
        }
        if ($param) {
            $this->wasInstanciated = true;
        }

        parent::__construct($param);
    }

    /**
     * @noinspection PhpUnused
     * @throws Exception
     */
    static function getTodayBegin(): self
    {
        return self::getInstance(date('Y-m-d'));
    }

    /**
     * @noinspection PhpUnused
     * @return GenericDatetime|null
     * @throws Exception
     */
    static public function getInstance(string $str = 'now')
    {
        if (!$str)
            return null;
        return new GenericDateTime($str);
    }

    /**
     * @noinspection PhpUnused
     */
    static public function getNow(): self
    {
        return new GenericDatetime("now");
    }

    /**
     * @noinspection PhpUnused
     * @throws Exception
     */
    static public function getToday0H(): self
    {
        $date = new GenericDatetime("now");
        return $date->cloneAtZeroHour();
    }

    /**
     * @noinspection PhpUnused
     * @throws Exception
     */
    public function cloneAtZeroHour(): self
    {
        $date = clone $this;
        $date->addHours(-$this->getHourInt());
        $date->addMins(-(int)$this->getMinTwoDigits());
        $date->addSeconds(-(int)$this->getSecTwoDigits());
        return $date;
    }

    /**
     * @noinspection PhpUnused
     * @throws Exception
     */
    public function addHours(int $nbHours): self
    {
        return $this->addSeconds($nbHours * 3600);
    }

    /**
     * @noinspection PhpUnused
     * @throws Exception
     */
    public function addSeconds(int $nbSeconds): self
    {
        $intervall = new DateInterval('PT' . abs($nbSeconds) . 'S');
        if ($nbSeconds > 0) {
            $this->add($intervall);
        } else {
            $this->sub($intervall);
        }
        return $this;
    }

    /**
     * @noinspection PhpUnused
     */
    public function getHourInt(): int
    {
        return (int)$this->getHourTwoDigits();
    }

    /**
     * @noinspection PhpUnused
     */
    public function getHourTwoDigits(): string
    {
        return $this->format("H");
    }

    /**
     * @noinspection PhpUnused
     * @throws Exception
     */
    public function addMins(int $nbMins): self
    {
        return $this->addSeconds($nbMins * 60);
    }

    /**
     * @noinspection PhpUnused
     */
    public function getMinTwoDigits(): string
    {
        return $this->format("i");
    }

    /**
     * @noinspection PhpUnused
     */
    public function getSecTwoDigits(): string
    {
        return $this->format("s");
    }

    /**
     * @noinspection PhpUnused
     * @throws Exception
     */
    static public function getInstanceNow(): self
    {
        return self::getInstance();
    }

    /**
     * @noinspection PhpUnused
     */
    static public function mockNow(GenericDatetime $mockedNow)
    {
        self::$mockedNow = $mockedNow;
    }

    /**
     * @noinspection PhpUnused
     */
    public function getWasInstanciated(): bool
    {
        if (!$this->wasInstanciated) {
            return false;
        }

        if ($this->getYear() == "-0001") {

            return false;
        }
        return true;
    }

    /**
     * @noinspection PhpUnused
     */
    public function setWasInstanciated(bool $wasInstanciated)
    {
        $this->wasInstanciated = $wasInstanciated;
    }

    /**
     * @noinspection PhpUnused
     */
    public function getYear(): string
    {
        return $this->format("Y");
    }

    /**
     * @noinspection PhpUnused
     */
    public function getFirstDayMonth(): self
    {
        return $this->getCloned()->addDays(1 - (int)$this->getDayTwoDigits());
    }

    /**
     * @noinspection PhpUnused
     */
    public function addDays(int $d): self
    {
        return $this->addDay($d);
    }

    /**
     * @noinspection PhpUnused
     */
    public function addDay(int $nbDays): GenericDatetime
    {

        if ($nbDays >= 0) {
            $str = '+' . $nbDays;
        } else {
            $str = $nbDays;
        }

        $this->modify($str . ' day');
        return $this;
    }

    /**
     * @noinspection PhpUnused
     */
    public function getCloned(): self
    {
        return clone $this;
    }

    /**
     * @noinspection PhpUnused
     */
    public function getDayTwoDigits(): string
    {
        return $this->format("d");
    }

    /**
     * @noinspection PhpUnused
     */
    public function addMonth(int $num = 1): self
    {
        $date = $this->format('Y-n-j');
        list($y, $m, $d) = explode('-', $date);
        $m += $num;
        while ($m > 12) {
            $m -= 12;
            $y++;
        }
        $last_day = date('t', strtotime("$y-$m-1"));
        if ($d > $last_day) {
            $d = $last_day;
        }
        $this->setDate($y, $m, $d);
        return $this;
    }

    /**
     * @noinspection PhpUnused
     */
    public function getHourFloat(): float
    {
        return $this->getHourInt() + (((int)$this->getMinTwoDigits()) / 60);
    }

    /**
     * @noinspection PhpUnused
     * Janvier = "01"
     */
    public function getMonthTwoDigits(): string
    {
        return $this->format("m");
    }

    /**
     * @noinspection PhpUnused
     */
    public function getSecDifference(GenericDatetime $date): int
    {
        return $date->getTimestamp() - $this->getTimestamp();
    }

    /**
     * @noinspection PhpUnused
     */
    public function getStrDateTimeFr(): string
    {
        return $this->format('d/m/Y H:i');
    }

    /**
     * @noinspection PhpUnused
     */
    public function getDate(): string
    {
        return $this->format('Y-m-d');
    }

    /**
     * @noinspection PhpUnused
     */
    public function getDateFr(): string
    {
        return $this->format('d/m/Y');
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTime(): string
    {
        return $this->format('H:i:s');
    }

    /**
     * @noinspection PhpUnused
     */
    public function __toString(): string
    {
        return $this->getDatetime();
    }

    /**
     * @noinspection PhpUnused
     */
    public function getDatetime(): string
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     * @noinspection PhpUnused
     */
    public function isLaterThan(GenericDatetime $DateTime): bool
    {
        return $this->getTimestamp() > $DateTime->getTimestamp();
    }

    /**
     * @noinspection PhpUnused
     */
    public function isLaterOrEqualThan(GenericDatetime $DateTime): bool
    {
        return $this->getTimestamp() >= $DateTime->getTimestamp();
    }

    /**
     * @noinspection PhpUnused
     */
    public function isEarlierOrEqualThan(GenericDatetime $DateTime): bool
    {
        return $this->getTimestamp() < $DateTime->getTimestamp();
    }


    /**
     * @noinspection PhpUnused
     */
    public function isBetween(GenericDatetime $DateTime1, GenericDatetime $DateTime2): bool
    {
        return $this->isEarlierThan($DateTime2) && $DateTime1->isEarlierThan($this);
    }

    /**
     * @noinspection PhpUnused
     */
    public function isEarlierThan(GenericDatetime $DateTime): bool
    {
        return $this->getTimestamp() < $DateTime->getTimestamp();
    }

    /**
     * @noinspection PhpUnused
     */
    function isWeekEnd(): bool
    {
        if (6 == $this->format('w') || 0 == $this->format('w')) {
            return true;
        }
        return false;
    }

    /**
     * @noinspection PhpUnused
     * @throws Exception
     */
    function isHoliday(): bool
    {

        $timestamp = $this->getTimestamp();
        $jour = date("d", $timestamp);
        $mois = date("m", $timestamp);
        $annee = date("Y", $timestamp);

        if ($jour == 1 && $mois == 1)
            return true; // 1er janvier
        if ($jour == 1 && $mois == 5)
            return true; // 1er mai
        if ($jour == 8 && $mois == 5)
            return true; // 5 mai
        if ($jour == 14 && $mois == 7)
            return true; // 14 juillet
        if ($jour == 15 && $mois == 8)
            return true; // 15 aout
        if ($jour == 1 && $mois == 11)
            return true; // 1 novembre
        if ($jour == 11 && $mois == 11)
            return true; // 11 novembre
        if ($jour == 25 && $mois == 12)
            return true; // 25 décembre

        $date_paques = easter_date($annee);
        $jour_paques = date("d", $date_paques);
        $mois_paques = date("m", $date_paques);
        if ($jour_paques == $jour && $mois_paques == $mois)
            return true; // Pâques

        $obDatePaques = self::getInstance($date_paques);
        $obDatLundiPaques = $obDatePaques->cloneAddDays(1);

        if ($obDatLundiPaques->isSameDay($this)) { // lundi de Pâques
            return true;
        }

        $datesAscension = array(
            '21/05/2009',
            '13/05/2010',
            '02/06/2011',
            '17/05/2012',
            '09/05/2013',
            '29/05/2014',
            '14/05/2015',
            '05/05/2016',
            '25/05/2017'
        );
        foreach ($datesAscension as $dateAscension) {
            if (self::getInstance($dateAscension)->isSameDay($this)) // jeudi de ascension
                return true;
        }
        $datesPentecote = array(
            '31/05/2009',
            '23/05/2010',
            '12/06/2011',
            '27/05/2012',
            '19/05/2013',
            '08/06/2014',
            '24/05/2015',
            '15/05/2016',
            '04/06/2017'
        );
        foreach ($datesPentecote as $pentecote) {
            if (self::getInstance($pentecote)->isSameDay($this)) //pentecote
                return true;
            if (self::getInstance($pentecote)->addDays(1)->isSameDay($this)) //lundi de pentecote
                return true;
        }
        return false;
    }

    /**
     * @noinspection PhpUnused
     */
    public function cloneAddDays(int $nbDays): self
    {
        $date = clone $this;
        $date->addDay($nbDays);
        return $date;
    }

    /**
     * Check if the current day is the same as the argument
     * @noinspection PhpUnused
     */
    function isSameDay(GenericDatetime $obDate): bool
    {
        if (($this->getYear() == $obDate->getYear()) && ($this->getDayYear() == $obDate->getDayYear())) {
            return true;
        }
        return false;
    }


    /**
     * @noinspection PhpUnused
     */
    function getDayYear(): int
    {
        return ((int)$this->format("z")) + 1;
    }

    /**
     * @noinspection PhpUnused
     */
    public function getIso8601(): string
    {
        return $this->format(DateTime::ISO8601);
    }

    /**
     * @noinspection PhpUnused
     * @throws Exception
     */
    public function setSecondsTo(int $seconds): self
    {
        $this->addSeconds(-(int)$this->getSecTwoDigits());
        $this->addSeconds($seconds);
        return $this;
    }


}