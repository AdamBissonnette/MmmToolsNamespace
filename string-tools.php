<?php
function delimitList($data, $format = 'comma')
{
	$formattedList = array();

	switch ($format)
	{
		case 'comma':
			$formattedList = createCommaList($data);
		break;
		case 'comma-and':
			$formattedList = createCommaAndList($data);
		break;
	}

	return $formattedList;
}


function createCommaList($data)
{
	$output = '';

	for ($i = 0; $i < count($data); $i++ )
	{
		$item = $data[$i];

		if ($i == (count($data) - 1)) //Last Term
		{
			$output .= $item;
		}
		else
		{
			$output .= $item . ', ';
		}
	}

	return $output;
}

function createCommaAndList($data)
{
	$output = '';

	for ($i = 0; $i < count($data); $i++ )
	{
		$item = $data[$i];

		if ($i == (count($data) - 2)) //Second Last Term
		{
			$output .= $item . ' and ';
		}
		else if ($i == (count($data) - 1)) //Last Term
		{
			$output .= $item;
		}
		else
		{
			$output .= $item . ', ';
		}
	}

	return $output;
}

?>