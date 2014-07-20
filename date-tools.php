<?php /* Date Functions */
	if (!function_exists('IsWithinRange')) {
		function IsWithinRange($StartDate, $EndDate, $curdate = null)
		{
			$active = true;
	
			if ($curdate == null)
			{
				$curdate = date('Y-m-d H:i');
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
	}
?>