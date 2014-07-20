<?php
	function genSelect($inputName, $labelText, $options, $DefaultOptionText, $selectedIndex = 0) //extend for key value options
	{
		$selectTemplate = '<div class="control-group"><label class="control-label" for="%s">%s</label><div class="controls"><select id="%s">%s</select></div></div>';
		
		$selectOptions = genOption(0, $DefaultOptionText);
		
		for ($i = 0; $i < count($options); $i++)
		{
			if (($i + 1) == $selectedIndex)
			{
				$selectOptions .= genOption(($i + 1), $options[$i], true);
			}
			else
			{
				$selectOptions .= genOption(($i + 1), $options[$i]);
			}
		}
		
		return sprintf($selectTemplate, $inputName, $labelText, $inputName, $selectOptions);
	}
	
	function genOption($value, $text, $selected = false)
	{
		$optionTemplate = '<option value="%s">%s</option>';
		
		if ($selected)
		{
			$optionTemplate = '<option value="%s" selected="selected">%s</option>';
		}
		
		return sprintf($optionTemplate, $value, $text);
	}
	
	
	function genInput($inputName, $labelText, $placeholder = "", $validationType = "req", $value = "", $note = "")
	{
		$inputWrapper = '<div class="control-group"><div class="controls">%s</div></div>';
		$inputTemplate = '<label class="control-label" for="%s">%s</label><input id="%s" class="%s" placeholder="%s" type="text" value="%s" />%s';
		$noteTemplate = '<p class="help-block">%s</p>';
		
		if ($note != "")
		{
			$note = sprintf($noteTemplate, $note);
		}
		
		$input = sprintf($inputTemplate, $inputName /* for */, $labelText, $inputName /* id */, $validationType /* class */, $placeholder, $value, $note);
		
		return $input;
	}
	
	function genTextArea($inputName, $labelText, $placeholder = "", $validationType = "req")
	{
		return sprintf('<div class="control-group"><label class="control-label" for="%s">%s</label><div class="controls"><textarea id="%s" class="%s" placeholder="%s" /></textarea></div></div>',
								$inputName /* for */, $labelText, $inputName /* id */, $validationType /* class */, $placeholder);
	}
?>