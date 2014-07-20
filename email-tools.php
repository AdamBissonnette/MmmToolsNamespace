<?php
	if (!function_exists('SendMail')) {
		function SendMail($to, $subject, $from, $message, $replyto = '')
		{
			if ($replyto == '') {$replyto = $from;}
		
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= sprintf('From: %s' . "\r\n" . 'Reply-To: %s' . "\r\n", $from, $replyto) .
				'X-Mailer: PHP/' . phpversion();
		
			return mail($to, $subject, $message, $headers);
		}
	}
?>