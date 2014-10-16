<?php /* Date Functions */
namespace MmmToolsNamespace;

class DateTools
{
	public static $DateFormat = 'Y-m-d H:i';

	function DateTools()
	{
	}

    static function IsWithinRange($StartDate, $EndDate, $curdate = null)
    {
        $active = true;

        if ($curdate == null)
        {
            $curdate = DateTools::getCurDate();
        }

        if ($StartDate >= $curdate)
        {
            $active = false;
        }
        elseif ($EndDate <= $curdate)
        {
            $active = false;
        }
        
        return $active;
    }

    static function getCurDate()
    {
    	return date(DateTools::$DateFormat);
    }

    static function getCurDateTime()
    {
    	return new \DateTime(date(DateTools::$DateFormat));
    }

    static function getDateTimeStamp($datetime)
    {
    	return $datetime->format(DateTools::$DateFormat);
    }

    static function addMinutesToDate($date, $minutes)
    {
    	$datetime = new \DateTime($date);
    	$minutesDateTime = new \DateInterval('PT' . $minutes . 'M');
    	$datetime->add($minutesDateTime);

    	return $datetime->format(DateTools::$DateFormat);
    }
}
?>