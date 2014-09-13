<?php
namespace MmmToolsNamespace;

	class Absolute_to_Relative_URL
	{
		protected $site_domain;
		protected $site_url;
		
		
		public function __construct()
		{
			$this->getSiteURL();
		}
		
		
		protected function getDomainPath($url)
		{
			$prefixes = array('http://www.', 'https://www.', 'http://', 'https://', 'www.');
			
			foreach ($prefixes as $value)
			{
				if (strpos($url, $value) === 0)
				{
					$url = substr($url, strlen($value));
					break;
				}
			}
			
			$separators = array('/', '?', '#');
			$separatorIndex = strlen($url);
			
			foreach ($separators as $value)
			{
				$pos = strpos($url, $value);
				
				if ($pos !== false)
				{
					if ($pos < $separatorIndex)
					{
						$separatorIndex = $pos;
					}
				}
			}
			
			$domain = substr($url, 0, $separatorIndex);
			$path   = substr($url, $separatorIndex);
			
			return array($domain, $path);
		}
		
		
		protected function getSiteURL()
		{
			$this->site_url = (!isset($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'];
			
			$path = $this->getDomainPath($this->site_url);
			
			$this->site_domain = $path[0];
		}
		
		
		public function relateRUL($url)
		{
			// Avoid unmatched protocols and already-relative URLs
			if (!isset($_SERVER['HTTPS']))
			{
				if (strpos($url, 'http://') !== 0) return $url;
			}
			else
			{
				if (strpos($url, 'https://') !== 0) return $url;
			}
			
			$url_split = $this->getDomainPath($url);
			
			if ($url_split[0] == $this->site_domain)
			{
				if ($url_split[1] != '')
				{
					return $url_split[1];
				}
				else
				{
					return '/';
				}
			}
			else
			{
				// Different domain, /, or unknown format
				return $url;
			}
		}
	}
	
	
	function absolute_to_relative($url)
	{
		global $absolute_to_relative_url_instance;
		
		if (is_null($absolute_to_relative_url_instance))
		{
			$absolute_to_relative_url_instance = new Absolute_to_Relative_URL();
		}
		
		return $absolute_to_relative_url_instance->relateURL($url);
	}
	
	
	$absolute_to_relative_url_instance = null;


	function get_admin_folder_path()
	{
		// First step: Current filepaths
		$current_file = str_replace('\\','/',__FILE__);

		// Second step: if the path contains the root path, lets remove it
		if (stristr($current_file, 'public_html'))
		    { $current_file='/'. preg_replace('/(.*)public_html\//i','','/'. $current_file); }
		elseif (stristr($current_file, 'wp-content'))
			{ $current_file='/wp-content/'. preg_replace('/(.*)wp-content\//i','',$current_file); }

		//echo dirname($current_file);
		//exit(1);

		return dirname($current_file);
	}
?>