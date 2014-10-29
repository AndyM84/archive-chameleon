<?php

	// Register with the system
	n2f_cls::getInstance()->registerExtension('sanitizers', 'Sanitizers', '0.1', 'Andrew Male', 'http://n2fysanitizers.codeplex.com/');

	/**
	 * Base class for sanitizers.
	 *
	 */
	abstract class sanitizer_base {
		/**
		 * Protected internal value.
		 *
		 * @var mixed
		 */
		protected $value;


		/**
		 * Public method to initialize the sanitizer.
		 *
		 * @param mixed $value	Mixed value to use for sanitizer.
		 */
		abstract public function __construct($value);
		/**
		 * Public method to determine if the internal value is valid.
		 *
		 * @return boolean	Boolean TRUE or FALSE.
		 */
		abstract public function isValid();
		/**
		 * Public method to return the sanitized version of the internal value.
		 *
		 */
		abstract public function getSanitized();
	}



	/**
	 * Validates a string as a domain based on the RFC specifications for domain names.
	 *
	 * @param string $addr			String to validate as a domain
	 * @param boolean $isEmailDomain	Whether or not to validate as an email domain
	 * @return boolean
	 */
	function validDomain($addr, $isEmailDomain = false) {
		$parts = explode('.', $addr);

		if (count($parts) < 2) {
			return(false);
		}

		if (!strcmp('www', strtolower($parts[0])) && count($parts) < 3) {
			return(false);
		}

		if (!strcmp('www', strtolower($parts[0])) && $isEmailDomain) {
			return(false);
		}

		if (strlen($parts[(count($parts) - 1)]) < 2 || strlen($parts[(count($parts) - 1)]) > 6) {
			return(false);
		}

		$chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9','-');

		$num = count($parts) - 1;

		for ($i = 0; $i < $num; ++$i) {
			$len = strlen($parts[$i]);

			if ($len < 1 || $len > 63) {
				return(false);
			}

			$last = '';

			for ($n = 0; $n < $len; ++$n) {
				$isGud = false;

				foreach (array_values($chars) as $char) {
					if (!strcmp($char, $parts[$i][$n])) {
						if (!strcmp($parts[$i][$n], '-')) {
							if ($n == 0 || $n == $len) {
								return(false);
							}

							if (!strcmp($last, '-')) {
								return(false);
							}
						}

						$isGud = true;

						break;
					}
				}

				if (false === $isGud) {
					return(false);
				}

				$last = $parts[$i][$n];
			}
		}

		$tlds = array('AC','AD','AE','AERO','AF','AG','AI','AL','AM','AN','AO','AQ','AR','ARPA','AS','ASIA','AT','AU','AW','AX','AZ','BA','BB','BD','BE','BF','BG','BH','BI','BIZ','BJ','BM','BN','BO','BR','BS','BT','BV','BW','BY','BZ','CA','CAT','CC','CD','CF','CG','CH','CI','CK','CL','CM','CN','CO','COM','COOP','CR','CU','CV','CX','CY','CZ','DE','DJ','DK','DM','DO','DZ','EC','EDU','EE','EG','ER','ES','ET','EU','FI','FJ','FK','FM','FO','FR','GA','GB','GD','GE','GF','GG','GH','GI','GL','GM','GN','GOV','GP','GQ','GR','GS','GT','GU','GW','GY','HK','HM','HN','HR','HT','HU','ID','IE','IL','IM','IN','INFO','INT','IO','IQ','IR','IS','IT','JE','JM','JO','JOBS','JP','KE','KG','KH','KI','KM','KN','KP','KR','KW','KY','KZ','LA','LB','LC','LI','LK','LR','LS','LT','LU','LV','LY','MA','MC','MD','ME','MG','MH','MIL','MK','ML','MM','MN','MO','MOBI','MP','MQ','MR','MS','MT','MU','MUSEUM','MV','MW','MX','MY','MZ','NA','NAME','NC','NE','NET','NF','NG','NI','NL','NO','NP','NR','NU','NZ','OM','ORG','PA','PE','PF','PG','PH','PK','PL','PM','PN','PR','PRO','PS','PT','PW','PY','QA','RE','RO','RS','RU','RW','SA','SB','SC','SD','SE','SG','SH','SI','SJ','SK','SL','SM','SN','SO','SR','ST','SU','SV','SY','SZ','TC','TD','TEL','TF','TG','TH','TJ','TK','TL','TM','TN','TO','TP','TR','TRAVEL','TT','TV','TW','TZ','UA','UG','UK','UM','US','UY','UZ','VA','VC','VE','VG','VI','VN','VU','WF','WS','XN--0ZWM56D','XN--11B5BS3A9AJ6G','XN--80AKHBYKNJ4F','XN--9T4B11YI5A','XN--DEBA0AD','XN--G6W251D','XN--HGBK6AJ7F53BBA','XN--HLCJ6AYA9ESC7A','XN--JXALPDLP','XN--KGBECHTV','XN--ZCKZAH','YE','YT','YU','ZA','ZM','ZW');

		foreach (array_values($tlds) as $tld) {
			if (!strcmp($tld, strtoupper($parts[(count($parts) - 1)]))) {
				return(true);
			}
		}

		return(false);
	}

	/**
	 * Validates a string as a domain based on the RFC specifications for domain names.
	 *
	 * @param string $addr			String to validate as a domain
	 * @param boolean $isEmailDomain	Whether or not to validate as an email domain
	 * @return boolean
	 */
	function validEmail($addr) {
		$parts = explode('@', $addr);

		if (count($parts) <> 2) {
			return(false);
		}

		$chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9','.','_','-','+','/','^','=','*','{','}','~');

		if (strlen($parts[0]) < 1) {
			return(false);
		}

		$len = strlen($parts[0]);

		for ($i = 0; $i < $len; $i++) {
			$isGud = false;

			foreach (array_values($chars) as $char) {
				if (!strcmp($char, strtolower($parts[0][$i]))) {
					$isGud = true;

					break;
				}
			}

			if (false === $isGud) {
				return(false);
			}
		}

		if (!validDomain($parts[1], true)) {
			return(false);
		}

		return(true);
	}



	/**
	 * Simple string type for basic removal of slashes and conversion of '/" characters.
	 *
	 */
	class sanit_simplestring extends sanitizer_base {
		/**
		 * Initializes the simple string.
		 *
		 * @param string $value	String value to use internally.
		 */
		public function __construct($value) {
			$this->value = stripslashes(trim($value));
		}

		/**
		 * Method to check if the string is valid.
		 *
		 * @return boolean	Boolean TRUE or FALSE.
		 */
		public function isValid() {
			if (empty($this->value) || strlen($this->value) < 1) {
				return(false);
			}

			return(true);
		}

		/**
		 * Method to retrieve the 'sanitized' string.  CAUTION: Not SQL safe!
		 *
		 * @return string	Cleaned version of value.
		 */
		public function getSanitized() {
			return(str_replace(array('"', "'"), array('&#34;', '&#39;'), $this->value));
		}
	}

	/**
	 * Email address type for verifying proper email format.
	 *
	 */
	class sanit_emailaddress extends sanitizer_base {
		/**
		 * Initializes the email address.
		 *
		 * @param string $value	String value to use internally.
		 */
		public function __construct($value) {
			$this->value = $value;
		}

		/**
		 * Method to check if the email address is valid.
		 *
		 * @return boolean	Boolean TRUE or FALSE.
		 */
		public function isValid() {
			if (empty($this->value) || strlen($this->value) < 1) {
				return(false);
			}

			if (!validEmail($this->value)) {
				return(false);
			}

			return(true);
		}

		/**
		 * Method to retrieve the sanitized email address.
		 *
		 * @return string	Cleaned version of email address.
		 */
		public function getSanitized() {
			return(trim($this->value));
		}
	}

	/**
	 * Domain name type for verifying proper format.
	 *
	 */
	class sanit_domain extends sanitizer_base {
		/**
		 * Initializes the domain name.
		 *
		 * @param string $value	String value to use internally.
		 */
		public function __construct($value) {
			$this->value = $value;
		}

		/**
		 * Method to check if the domain name is valid.
		 *
		 * @return boolean	Boolean TRUE or FALSE.
		 */
		public function isValid() {
			if (empty($this->value) || strlen($this->value) < 1) {
				return(false);
			}

			if (!validDomain($this->value)) {
				return(false);
			}

			return(true);
		}

		/**
		 * Method to retrieve the sanitized domain name.
		 *
		 * @return string	Cleaned version of the domain name.
		 */
		public function getSanitized() {
			return(trim($this->value));
		}
	}

	/**
	 * Integer type to ensure integer format.
	 *
	 */
	class sanit_integer extends sanitizer_base {
		/**
		 * Initializes the domain name.
		 *
		 * @param string $value	String value to use internally.
		 */
		public function __construct($value) {
			$this->value = $value;
		}

		/**
		 * Method to check if the integer is valid.
		 *
		 * @return boolean	Boolean TRUE or FALSE.
		 */
		public function isValid() {
			if (empty($this->value) || !is_int($this->value)) {
				return(false);
			}

			return(true);
		}

		/**
		 * Method to retrieve the sanitized integer.
		 *
		 * @return integer	Integer value of internal data.
		 */
		public function getSanitized() {
			return(intval($this->value));
		}
	}

	/**
	 * Float type to ensure float format.
	 *
	 */
	class sanit_float extends sanitizer_base {
		/**
		 * Initializes the float value.
		 *
		 * @param string $value	String value to use internally.
		 */
		public function __construct($value) {
			$this->value = $value;
		}

		/**
		 * Method to check if the floating number is valid.
		 *
		 * @return boolean	Boolean TRUE or FALSE.
		 */
		public function isValid() {
			if (empty($this->value) || !is_float($this->value)) {
				return(false);
			}

			return(true);
		}

		/**
		 * Method to retrieve the sanitized floating number.
		 *
		 * @return float	Floating value of internal data.
		 */
		public function getSanitized() {
			return(floatval($this->value));
		}
	}

	/**
	 * Boolean type to ensure boolean format.
	 *
	 */
	class sanit_boolean extends sanitizer_base {
		/**
		 * Initializes the boolean value.
		 *
		 * @param mixed $value	Value to use internally.
		 */
		public function __construct($value) {
			$this->value = $value;
		}

		/**
		 * Method to check if the boolean value is valid.
		 *
		 * @return boolean	Boolean TRUE or FALSE.
		 */
		public function isValid() {
			if (empty($this->value)) {
				return(false);
			}

			if ($this->value != '0' && $this->value != '1' && $this->value != 'true' && $this->value != 'false'
				&& $this->value != 0 && $this->value != 1 && $this->value != true && $this->value != false) {
				return(false);
			}

			return(true);
		}

		/**
		 * Method to retrieve the sanitized boolean value.
		 *
		 * @return boolean	Boolean TRUE or FALSE.
		 */
		public function getSanitized() {
			if ($this->value == '0' || $this->value == 'false' || $this->value == 0 || $this->value == false) {
				return(false);
			}

			return(true);
		}
	}

	/**
	 * Date string type to ensure date format.
	 *
	 */
	class sanit_datestring extends sanitizer_base {
		/**
		 * Protected collection of accepted date formats.
		 *
		 * @var array
		 */
		protected $patterns = array(
			"|^([0-9]{4})-([0-9]{2})-([0-9]{2})$|",
			"|^([0-9]{2})/([0-9]{2})/([0-9]{4})$|",
			"|^([0-9]{2})-([0-9]{2})-([0-9]{2})$|",
			"|^([0-9]{2})/([0-9]{2})/([0-9]{2})$|",
			"|^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-2]{1})([0-9]{1}):([0-5]{1})([0-9]{1})$|",
			"|^([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-2]{1})([0-9]{1}):([0-5]{1})([0-9]{1})$|",
			"|^([0-9]{2})-([0-9]{2})-([0-9]{2}) ([0-2]{1})([0-9]{1}):([0-5]{1})([0-9]{1})$|",
			"|^([0-9]{2})/([0-9]{2})/([0-9]{2}) ([0-2]{1})([0-9]{1}):([0-5]{1})([0-9]{1})$|",
			"|^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-2]{1})([0-9]{1}):([0-5]{1})([0-9]{1}):([0-5]{1})([0-9]{1})$|",
			"|^([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-2]{1})([0-9]{1}):([0-5]{1})([0-9]{1}):([0-5]{1})([0-9]{1})$|",
			"|^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-2]{1})([0-9]{1}):([0-5]{1})([0-9]{1}):([0-5]{1})([0-9]{1})$|",
			"|^([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-2]{1})([0-9]{1}):([0-5]{1})([0-9]{1}):([0-5]{1})([0-9]{1})$|"
		);

		/**
		 * Initializes the date string value.
		 *
		 * @param string $value	String value to use internally.
		 */
		public function __construct($value) {
			$this->value = $value;
		}

		/**
		 * Method to check if the date string value is valid.
		 *
		 * @return boolean	Boolean TRUE or FALSE.
		 */
		public function isValid() {
			if (empty($this->value) || strlen($this->value) < 1) {
				return(false);
			}

			foreach (array_values($this->patterns) as $pattern) {
				if (preg_match($pattern, $this->value)) {
					return(true);
				}
			}

			return(false);
		}

		/**
		 * Method to retrieve the sanitized date string.
		 *
		 * @return string	Sanitized date string.
		 */
		public function getSanitized() {
			return(trim($this->value));
		}
	}

?>