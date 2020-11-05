<?php


namespace CaptainKant\Generics\Core;


use DateInterval;
use Exception;

class GenericDateTime extends \DateTime
{

    static $mockedNow = null;
    private $wasInstanciated = false;

    public function __construct($param)
    {


        if ($param == 'now' && self::$mockedNow) {
            $param = (string)self::$mockedNow->getDatetime();
        }


        if ($param instanceof \DateTime) {
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

    static function getTodayBegin()
    {
        return self::getInstance(date('Y-m-d'));
    }

    /**
     * @param string $str
     * @return GenericDatetime|null
     * @throws Exception
     */
    static public function getInstance($str = 'now')
    {
        if (!$str)
            return null;
        return new GenericDateTime($str);
    }

    /**
     * @return GenericDatetime
     */
    static public function getNow()
    {
        return new GenericDatetime("now");
    }

    /**
     * @return GenericDatetime
     * @throws Exception
     */
    static public function getToday0H()
    {
        $date = new GenericDatetime("now");
        return $date->cloneAtZeroHour();
    }

    /**
     * @return GenericDatetime
     * @throws Exception
     */
    public function cloneAtZeroHour()
    {
        $date = clone $this;
        $date->addHours(-$this->getHourInt());
        $date->addMins(-(int)$this->getMinTwoDigits());
        $date->addSeconds(-(int)$this->getSecTwoDigits());
        return $date;
    }

    public function addHours($nbHours)
    {
        return $this->addSeconds($nbHours * 3600);
    }

    /**
     * @param $nbSeconds
     * @return GenericDatetime
     * @throws Exception
     */
    public function addSeconds($nbSeconds)
    {
        $intervall = new DateInterval('PT' . abs($nbSeconds) . 'S');
        if ($nbSeconds > 0) {
            $this->add($intervall);
        } else {
            $this->sub($intervall);
        }
        return $this;
    }

    public function getHourInt()
    {
        return (int)$this->getHourTwoDigits();
    }

    public function getHourTwoDigits()
    {
        return $this->format("H");
    }

    public function addMins($nbMins)
    {
        return $this->addSeconds($nbMins * 60);
    }

    public function getMinTwoDigits()
    {
        return $this->format("i");
    }

    public function getSecTwoDigits()
    {
        return $this->format("s");
    }

    static public function getInstanceNow()
    {
        return self::getInstance('now');
    }

    static public function mockNow(GenericDatetime $mockedNow)
    {
        self::$mockedNow = $mockedNow;
    }

    /**
     * @return boolean
     */
    public function getWasInstanciated()
    {
        if (!$this->wasInstanciated) {
            return false;
        }

        if ($this->wasInstanciated && $this->getYear() == "-0001") {

            return false;
        }
        return true;
    }

    /**
     * @param boolean $wasInstanciated
     */
    public function setWasInstanciated(bool $wasInstanciated)
    {
        $this->wasInstanciated = $wasInstanciated;
    }

    public function getYear()
    {
        return $this->format("Y");
    }

    public function getFirstDayMonth()
    {
        return $this->getCloned()->addDays(1 - (int)$this->getDayTwoDigits());
    }

    /**
     * @param $d
     * @return $this
     */
    public function addDays($d)
    {
        return $this->addDay($d);
    }

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
     * @return GenericDatetime
     */
    public function getCloned()
    {
        return clone $this;
    }

    public function getDayTwoDigits()
    {
        return $this->format("d");
    }

    public function addMonth($num = 1)
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

    public function getHourFloat()
    {
        return $this->getHourInt() + (((int)$this->getMinTwoDigits()) / 60);
    }

    /**
     * Janvier = "01"
     * @return string
     */
    public function getMonthTwoDigits()
    {
        return $this->format("m");
    }

    public function getSecDifference(GenericDatetime $date)
    {
        return $date->getTimestamp() - $this->getTimestamp();
    }

    public function getStrDateTimeFr()
    {
        return $this->format('d/m/Y H:i');
    }

    public function getDate()
    {
        return $this->format('Y-m-d');
    }

    public function getDateFr()
    {
        return $this->format('d/m/Y');
    }

    public function getTime()
    {
        return $this->format('H:i:s');
    }

    public function __toString()
    {
        return $this->getDatetime();
    }

    public function getDatetime()
    {
        return $this->format('Y-m-d H:i:s');
    }

    public function isLaterThan(GenericDatetime $DateTime)
    {
        return $this->getTimestamp() > $DateTime->getTimestamp();
    }

    public function isLaterOrEqualThan(GenericDatetime $DateTime)
    {
        return $this->getTimestamp() >= $DateTime->getTimestamp();
    }

    public function isEarlierOrEqualThan(GenericDatetime $DateTime)
    {
        return $this->getTimestamp() < $DateTime->getTimestamp();
    }


    /**
     * @param GenericDatetime $DateTime1
     * @param GenericDatetime $DateTime2
     * @return bool
     */
    public function isBetween(GenericDatetime $DateTime1, GenericDatetime $DateTime2)
    {
        return $this->isEarlierThan($DateTime2) && $DateTime1->isEarlierThan($this);
    }

    public function isEarlierThan(GenericDatetime $DateTime)
    {
        return $this->getTimestamp() < $DateTime->getTimestamp();
    }

    function isWeekEnd()
    {
        if (6 == $this->format('w') || 0 == $this->format('w')) {
            return true;
        }
        return false;
    }

    /**
     * Est-on dans un jour férié ?
     * @return bool
     * @throws Exception
     */
    function isHoliday()
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
     * @param $nbDays
     * @return GenericDatetime
     */
    public function cloneAddDays($nbDays)
    {
        $date = clone $this;
        $date->addDay($nbDays);
        return $date;
    }

    /**
     * Check if the current day is the same as the argument
     * @param GenericDatetime $obDate
     * @return bool
     */
    function isSameDay(GenericDatetime $obDate)
    {
        if (($this->getYear() == $obDate->getYear()) && ($this->getDayYear() == $obDate->getDayYear())) {
            return true;
        }
        return false;
    }

    /*@var $mockedNow GenericDatetime|null */

    /**
     * @return int
     */
    function getDayYear()
    {
        return ((int)$this->format("z")) + 1;
    }

    /**
     * @param $seconds
     * @return $this
     * @throws Exception
     */
    public function setSecondsTo($seconds)
    {
        $this->addSeconds(-(int)$this->getSecTwoDigits());
        $this->addSeconds($seconds);
        return $this;
    }


}