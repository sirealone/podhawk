<?php

class SE_Blowfish
// a wrapper class for blowfish
{
	private $log; // instance of LO_ErrorLog
	private $cipher; // instance of Crypt_Blowfish
	private $initialised = TRUE; // object properly instantiated?

	public function __construct()
	{
		$this->log = LO_ErrorLog::instance();

		try
		{
			if (!defined('BLOWFISH_KEY'))
			{
				throw new Exception('Failed to construct object of class SE_Blowfish - the Blowfish Key is not defined');
			}
			if (defined('USE_BLOWFISH_ENCRYPTION') && USE_BLOWFISH_ENCRYPTION == FALSE)
			{
				throw new Exception('Failed to construct object of class SE_Blowfish - the constant USE_BLOWFISH_ENCRYPTION is set to FALSE');
			}

			require_once PATH_TO_ROOT . '/podhawk/lib/blowfish.php';

			$this->cipher = new Crypt_Blowfish(BLOWFISH_KEY);
		}
		catch (Exception $e)
		{
			$this->log->write($e->getMessage());
			$this->initialised = FALSE;
		}	
	}

	public function encrypt($value)
	{
		if ($this->initialised && !empty($value))
		{
			return Eencrypt($this->cipher, $value);
		}
		else
		{
			return $value;
		}
	}

	public function decrypt($value)
	{
		if ($this->initialised && !empty($value))
		{
			return 	trim(Edecrypt($this->cipher, $value));
		}
		else
		{
			return $value;
		}
	}
}
?>
