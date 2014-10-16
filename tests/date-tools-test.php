<?php

include_once('../date-tools.php');

class DateToolsTest extends PHPUnit_Framework_TestCase
{
    // ...

    public function testIsWithinRange()
    {
        date_default_timezone_set('UTC');
        // Arrange
        $startDate = MmmToolsNamespace\DateTools::GetCurDate();
        $endDate = MmmToolsNamespace\DateTools::GetCurDate();
        $rangeDate = MmmToolsNamespace\DateTools::GetCurDate();

        // Act
        $endDate = MmmToolsNamespace\DateTools::addMinutesToDate($startDate, 15);
        $rangeDate = MmmToolsNamespace\DateTools::addMinutesToDate($startDate, 5);

        //echo "s:" . $startDate . " e:" . $endDate . " r:" . $rangeDate;

        // Assert
        $this->assertEquals(true, MmmToolsNamespace\DateTools::IsWithinRange($startDate, $endDate, $rangeDate));
        $this->assertEquals(false, MmmToolsNamespace\DateTools::IsWithinRange($startDate, $rangeDate, $endDate));
    }

    // ...
}

?>