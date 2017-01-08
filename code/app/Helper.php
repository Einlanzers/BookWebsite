<?php

namespace App;

class Helper
{
	public static function fieldErrorDisplay($fieldname, $errors)
	{
		if($errors->has($fieldname))
		{
			$error = $errors->first($fieldname);
			return "<span class=\"help-block\">
				<strong>{$error}</strong>
			</span>";
		}

		return null;
	}
	
	public static function formatDate($date, $format)
	{
		$date = new \Carbon\Carbon($date, "America/Detroit");
		return $date->format($format);
	}
}