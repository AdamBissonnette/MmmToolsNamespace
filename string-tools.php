<?php
function delimitList($data, $format = 'comma')
{
	$formattedList = array();

	switch ($format)
	{
		case 'comma':
			$formattedList = createStringList($data, ", ");
		break;
		case 'comma-and':
			$formattedList = createStringList($data, ", ", " and ");
		break;
	}

	return $formattedList;
}

function createStringList($data, $delimiter, $lastTermException=NULL)
{
	if (!isset($lastTermException))
	{
		$lastTermException = $delimiter;
	}

	$output = '';

	for ($i = 0; $i < count($data); $i++ )
	{
		$item = $data[$i];

		if ($i == (count($data) - 2)) //Second Last Term
		{
			$output .= $item . $lastTermException;
		}
		else if ($i == (count($data) - 1)) //Last Term
		{
			$output .= $item;
		}
		else
		{
			$output .= $item . $delimiter;
		}
	}

	return $output;
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