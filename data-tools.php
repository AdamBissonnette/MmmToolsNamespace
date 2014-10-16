<?php
namespace MmmToolsNamespace;

if (!function_exists('WPInsertStatement')) {
	function WPInsertStatement($table, $array, $format)
	{
		global $wpdb;
		$wpdb->insert($table, $array, $format);
		
		return $wpdb->insert_id;
	}
}
if (!function_exists('WPExecuteStatement')) {
	function WPExecuteStatement($statement)
	{
		global $wpdb;
		$wpdb->query($statement);
	}
}
if (!function_exists('WPExecuteQuery')) {
	function WPExecuteQuery($query)
	{
		global $wpdb;
		$result = $wpdb->get_results($query);
		
		return $result;
	}
}
if (!function_exists('arr_to_obj')) {	
	function arr_to_obj($array = array()) {
		$return = new stdClass();
		foreach ($array as $key => $val) {
			if (is_array($val)) {
				$return->$key = $this->convert_array_to_object($val);
			} else {
				$return->{$key} = $val;
			}
		}
		return $return;
	}
}
if (!function_exists('arr_to_associative')) {	
	//Convert array to be associative if necessary
	function arr_to_associative($array = array()) {
		if ((bool)count(array_filter(array_keys($array), 'is_string')))
		{
			$assoc = array();
			foreach ($array as $key)
			{
				$assoc[$key] = $key;
			}

			$array = $assoc;
		}

		return $array;
	}
}

function OutputMetaData($tabs, $values=null, $data=null)
{
	OutputThemeData($tabs, $values, $data);
}

//Theme Data Functions
function OutputThemeData($tabs, $values=null, $data=null)
{
	$isFirst = true;

	$output = "";
	$wrapperTemplate = '<div class="col-sm-12 tabbable">%s</div>';
	$tabHeadingTemplate = '<ul class="nav nav-tabs">%s</ul>';
	$tabContentTemplate = '<div class="row tab-content">%s</div>';

	$tabHeadings = "";
	$tabContent = "";

	$tabCount = count($tabs);

	foreach ($tabs as $tab)
	{
		if ($tabCount > 1)
		{
			$tabHeadings .= OutputTabNav($tab["id"], $tab["name"], $tab["icon"], $isFirst);			
		}

		$tabContent .= OutputTabContent($tab["id"], $tab["sections"], $isFirst, $values, $data);
		
		if ($isFirst)
		{
			$isFirst = false;
		}
	}

	$output = sprintf($wrapperTemplate,
					sprintf($tabHeadingTemplate, $tabHeadings) .
					sprintf($tabContentTemplate, $tabContent));

	return $output;
}

function OutputTabNav($id, $name, $icon, $isFirst)
{
	 $tabTemplate = '<li%s><a href="#%s" data-toggle="tab"><i class="icon-%s"></i> %s</a></li>';
	 
	 $class = "";
	 
	 if ($isFirst)
	 {
	 	$class = ' class="active"';
	 }
	 
	 return sprintf($tabTemplate, $class, $id, $icon, $name);
}

function OutputTabContent($id, $sections, $isFirst, $values, $data=null)
{
	$class = "";

	if ($isFirst)
	{
	 	$class = ' active';
	}

	$tabContentTemplate = sprintf('<div class="tab-pane%s" id="%s">', $class, $id) . "%s</div>";

	$tabContent = "";
	
	if ($sections != null)
	{
		foreach ($sections as $section)
		{
			$tabContent .= OutputSection($section["name"], $section["size"], $section["fields"], $values, $data);
		}
	}
	else
	{
		$tabContent .=  "Missing section content for " . $class . " - " . $id;
	}
	
	$output = sprintf($tabContentTemplate, $tabContent);

	return $output;
}

function OutputSection($name, $size, $fields, $values, $data=null)
{
	$sectionTemplate = sprintf('<div class="col-sm-%s meta-section"><legend>%s</legend>', $size, $name) . "%s</div>";
	$sectionContent = "";

	foreach ($fields as $field)
	{
		$options = isset($field["options"])?$field["options"]:array();
		$sectionContent .= MMRootsField($field["id"], $field["label"], $field["type"], $options, $values, $data);
	}
	
	$output = sprintf($sectionTemplate, $sectionContent);

	return $output;
}

function GetMetaDataFields($tabs)
{
	return GetThemeDataFields($tabs);
}

function GetThemeDataFields($tabs)
{
	$fields = array();

	foreach ($tabs as $tab)
	{

		foreach ($tab["sections"] as $section)
		{
			$fields = array_merge($fields, $section["fields"]);
		}
	}

	return $fields;
}

function MetaField($id, $label, $type, $options=null, $values=null, $data=null)
{
	return MMRootsField($id, $label, $type, $options, $values, $data);
}

function MMRootsField($id, $label, $type, $options=null, $values=null, $data = null)
{
	if ($data == null)
	{
		global $MMM_Roots;
		$data = $MMM_Roots;
	}
	
	$output = "";

	if (isset($values))
	{
		$value = isset($values[$id])?stripslashes($values[$id]):"";
		$output = createFormField($id, $label, $value, $type, $options);
	}
	else
	{
		$output = createFormField($id, $label, $data->get_setting($id), $type, $options);
	}

	return $output;
}


/*
Search through the child taxonomies for matching slugs in the parent
Merge the Parent and child sections together
Output the merged list*/

function MergeChildTaxonomies($parentTaxonomies, $childTaxonomies)
{
	foreach ($childTaxonomies as $childTaxonomy) {
		for ($i = 0; $i < count($parentTaxonomies); $i++)
		{
			if ($childTaxonomy["slug"] == $parentTaxonomies[$i]["slug"])
			{
				$parentTaxonomies[$i]["options"][0]["sections"] =
					array_merge($parentTaxonomies[$i]["options"][0]["sections"],
								$childTaxonomy["options"][0]["sections"]);
			}
		}
	}

	return $parentTaxonomies;
}


function OutputPostProperties($post, $content = "")
{
	global $MMM_Data_Library;

	$output = $content;

	if ($MMM_Data_Library != null)
	{
		try {
			foreach ($MMM_Data_Library as $Library) {
				$variables = $Library->get_post_variables($post);

				foreach ($variables as $key => $value) {
					$output = str_replace($key, $value, $output);
				}
			}
		} catch (Exception $e) {
			
		}
	}

	return $output;
}

function getPostThumbnailUrl($post)
{
	$imageUrl = "";

	if (has_post_thumbnail($post->ID))
	{
		$thumb =  wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID), 'thumbnail');
		$imageUrl = $thumb[0];
	}

	return $imageUrl;
}

function getKeyValueFromArray($array, $key, $default)
{
	$output = $default;

	if (isset($array))
	{
		if (isset($array[$key]))
		{
			$output = $array[$key];
		}
	}

	return $output;	
}

function getIntegerValueFromArray($array, $key)
{
	return getKeyValueFromArray($array, $key, -1);
}

function getStringValueFromArray($array, $key)
{
	return getKeyValueFromArray($array, $key, "");
}
?>