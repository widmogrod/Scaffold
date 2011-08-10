<?php
/**
 * Conditional
 *
 * Inspiration: {@link http://www.conditional-css.com/}
 *
 * Conditional statements:
 * <code>
 *   [if {!} browser]
 *   [if {!} browser version]
 *   [if {!} condition browser version]
 * </code>
 *
 * Conditional examples:
 * <code>
 *   // Conditional-CSS syntax examples  
 *   [if IE] - Used if the browser is IE  
 *   [if ! Opera] - Used if the browser is not Opera  
 *   [if IE 5] - Used if browser is IE 5  
 *   [if lte IE 6] - Used if the browser is IE 6 or less (IE 5, IE 4 etc)  
 *   [if ! gt IE 6] - Same effect as the previous statement, if not greater than IE 6
 * </code>
 *
 *
 * Browser names:
 * <code>
 *   FF - FireFox
 *   Ch - Chrome
 *   O - Opera
 *   IE - MSIE
 *   N - netscape
 * </code>
 *
 *
 * Conditional operators:
 * <code>
 *   lt - Less than (<)
 *   lte - Less than or equal to (<=)
 *   eq - Equal to (==)
 *   gte - Greater than or equal to (>=)
 *   gt - Greater then (>)
 *   ngt - Not geater then (!=, <>)
 * </code>
 * 
 *
 * @package 		Scaffold
 * @author 			Gabriel Habryn <widmogrod@gmail.com>
 * @license 		LGPL
 */
class Scaffold_Extension_Conditional extends Scaffold_Extension
{
	/**
     * Default User Agent chain to prevent empty value
     */
    const DEFAULT_HTTP_USER_AGENT = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)';

	const DEFAULT_BROWSER_NAME = 'Firefox';
	
	const DEFAULT_BROWSER_ABBR_NAME = 'FF';
	
	const DEFAULT_BROWSER_VERSION = 3;
	
	protected $_commentNotMatched = true;
	
	protected $_isClosing;

	protected $_isNegation;
	
	protected $_isVersion;
	
	protected $_isBrowser;
	
	protected $_isOperator;
	
	protected $_currentVersion;
	
	protected $_currentOperator;
	
	protected $_currentBrowserAbbrName;
	
	protected $_availableOperators = array('<',  '>',  '<=',  '>=',  '==', '<>', '!=',
										   'lt', 'gt', 'lte', 'gte', 'eq', 'neq');
	
	protected $_userAgent;
	
	protected $_browserData;
	
	protected $_browserName;

	protected $_browserVersion;
	
	protected $_browserNameAsAbbrName = array();
	
	/**
	 * Default settings
	 * @var array
	 */
	public $_defaults = array(
		'browserName' => null,
		'browserVersion' => null,
		'browserNameAsAbbrName' => array(
			'firefox' => 'ff',
			'chrome'  => 'ch',
			'opera'   => 'o',
			'safari'  => 's',
			'msie'	  => 'ie',
			'netscape'=> 'n'
		),
		'commentNotMatched' => true
	);

	/**
	 * Scaffold's process hook
	 *
	 * @access public
	 * @param Scaffold_Source $source
	 * @param Scaffold $scaffold
	 * @return void
	 */
	public function process(Scaffold_Source $source, Scaffold $scaffold)
	{
		// filter pair key => value
		$browserNameAsAbbrName = $this->config['browserNameAsAbbrName'];
		$browserNamesKeys = array_keys($browserNameAsAbbrName);
		$browserNamesKeys = array_map('strtolower', $browserNamesKeys);
		$browserNamesValues = array_values($browserNameAsAbbrName);
		$browserNamesValues = array_map('strtolower', $browserNamesValues);
		$this->_browserNameAsAbbrName = array_combine($browserNamesKeys, $browserNamesValues);
		
		$this->_commentNotMatched = (bool) $this->config['commentNotMatched'];

		$this->setBrowserName($this->config['browserName']);
		$this->setBrowserVersion($this->config['browserVersion']);

		// matching inline conditions
		$regexp = '/(\[([^\[\]]+)\]([^\n]+))/ie';
		$source->contents = preg_replace($regexp, "\$this->_parse('$2', '$0')", $source->contents);
		
		// matching block conditions
		$regexp = '/\[([^\[\]]+)\]([^\[\]]+)\[\/([^\[\]]+)\]/ie';
		$source->contents = preg_replace($regexp, "\$this->_parse('$1', '$2', '$3')", $source->contents);
	}
	
	public function getUserAgent()
	{
		if (null === $this->_userAgent)
		{	
			$this->_userAgent = (isset($_SERVER) && isset($_SERVER['HTTP_USER_AGENT']))
				? $_SERVER['HTTP_USER_AGENT']
				: self::DEFAULT_HTTP_USER_AGENT;
		}

		return $this->_userAgent;
	}
	
	public function getBrowserData($key = null, $default = null)
	{
		if (null === $this->_browserData)
		{
			/*
			 * Prevents from error: get_browser() [function.get-browser]: browscap ini directive not set.
			 */ 
			// 
			if (ini_get('browscap'))
			{
				$this->_browserData = get_browser($this->getUserAgent(), true);
			}
			else
			{
				/*
				 * Alternative implementation {@link http://www.php.net/manual/en/function.get-browser.php#101125}
				 */

				$userAgent = $this->getUserAgent(); 
			    $browserName = self::DEFAULT_BROWSER_NAME;
			    $browserVersion = -1;

			    // Next get the name of the useragent yes seperately and for good reason
			    if(preg_match('/MSIE/i',$userAgent) && !preg_match('/Opera/i',$userAgent)) 
			    { 
			        $browserName = "MSIE"; 
			    } 
			    elseif(preg_match('/Firefox/i',$userAgent)) 
			    { 
			        $browserName = "Firefox"; 
			    } 
			    elseif(preg_match('/Chrome/i',$userAgent)) 
			    { 
			        $browserName = "Chrome"; 
			    } 
			    elseif(preg_match('/Safari/i',$userAgent)) 
			    { 
			        $browserName = "Safari"; 
			    } 
			    elseif(preg_match('/Opera/i',$userAgent)) 
			    { 
			        $browserName = "Opera"; 
			    } 
			    elseif(preg_match('/Netscape/i',$userAgent)) 
			    { 
			        $browserName = "Netscape"; 
			    } 

			    // finally get the correct browserVersion number
			    $known = array('Version', $browserName, 'other');
			    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
			    if (!preg_match_all($pattern, $userAgent, $matches)) {
			        // we have no matching number just continue
			    }

			    // see how many we have
			    $i = count($matches['browser']);
			    if ($i != 1) 
				{
			        //we will have two since we are not using 'other' argument yet
			        //see if version is before or after the name
			        if (strripos($userAgent,"Version") < strripos($userAgent, $browserName))
					{
			            $browserVersion= $matches['version'][0];
			        }
			        else 
					{
			            $browserVersion= $matches['version'][1];
			        }
			    }
			    else 
				{
			        $browserVersion= $matches['version'][0];
			    }

			    $this->_browserData = array(
			        'browser'   => $browserName,
			        'version'   => $version,
					'majorver'  => (($dotPosition = strpos($version, '.')) ? substr($version, 0, $dotPosition) : $version)
			    );
			}
		}

		return (null === $key) 
			? $this->_browserData
			: (array_key_exists($key, $this->_browserData)
				? $this->_browserData[$key]
				: $default);
	}
	
	public function setBrowserVersion($version)
	{
		$this->_browserVersion = (is_numeric($version)) ? $version : null;
	}
	
	public function getBrowserVersion()
	{
		if (null === $this->_browserVersion)
		{
			$this->_browserVersion = $this->getBrowserData('majorver', self::DEFAULT_BROWSER_VERSION);
		}

		return $this->_browserVersion;
	}
	
	public function setBrowserName($browser)
	{
		$browser = strtolower($browser);
		$this->_browserName = array_key_exists($browser, $this->_browserNameAsAbbrName) ? $browser : null;
	}
	
	public function getBrowserName()
	{
		if (null === $this->_browserName)
		{
			$this->_browserName = $this->getBrowserData('browser', self::DEFAULT_BROWSER_NAME);
			$this->_browserName = strtolower($this->_browserName);
		}

		return $this->_browserName;
	}
	
	public function getBrowserAbbrName()
	{
		if (null === $this->_browserAbbrName)
		{
			$browserName = $this->getBrowserName();
			$this->_browserAbbrName = array_key_exists($browserName, $this->_browserNameAsAbbrName)
				? $this->_browserNameAsAbbrName[$browserName]
				: self::DEFAULT_BROWSER_ABBR_NAME;
		}
		
		return $this->_browserAbbrName;
	}

	
	protected function _parse($condition, $content, $closingTag = null)
	{
		// parseCondition
		$this->_parseCondition($condition);
		
		if (!$this->_isBrowser)
		{
			$message = 'Undefined condition "%s". '."\n\t".'Available conditions: %s';
			$message = sprintf($message, $condition, implode(', ', array_map('strtoupper', $this->_browserNameAsAbbrName)));
			return $this->_errorNear($message, $content);
		}
		
		if (null !==$closingTag)
		{
			$closingTag = strtolower($closingTag);
			if (!in_array($closingTag, $this->_browserNameAsAbbrName) 
				|| $this->_currentBrowserAbbrName != $closingTag)
			{
				$message = 'Invalid closing tag "%s".'."\n\t".'Expecting tag is "%s.';
				$message = sprintf($message, strtoupper($closingTag), strtoupper($this->_currentBrowserAbbrName));
				return $this->_errorNear($message, $content);
			}
		}
		
		$isMached = false;

		$browserVersion = $this->getBrowserVersion();
		$browserAbbrName = $this->getBrowserAbbrName();
		
		// var_dump($browserAbbrName);
		
		// browser type is mached
		if ($this->_currentBrowserAbbrName == $browserAbbrName)
		{
			$isMached = true;
		}

		if ($isMached && $this->_isVersion)
		{
			switch ($this->_currentOperator)
			{
				default:
				case '=':
				case 'eq':
					if ($browserVersion == $this->_currentVersion)
					{
						$isMached = true;
					}
					break;

				case '<':
				case 'lt':
					if ($browserVersion < $this->_currentVersion)
					{
						$isMached = true;
					}
					break;

				case '<=':
				case 'lte':
					if ($browserVersion <= $this->_currentVersion)
					{
						$isMached = true;
					}
					break;

				case '>':
				case 'gt':
					if ($browserVersion > $this->_currentVersion)
					{
						$isMached = true;
					}
					break;

				case '>=':
				case 'gte':
					if ($browserVersion >= $this->_currentVersion)
					{
						$isMached = true;
					}
					break;
				
				case '<>':
				case '!=':
				case 'neq':
					if ($browserVersion != $this->_currentVersion)
					{
						$isMached = true;
					}
					break;
			}
		}
		
		// negation of result
		if ($this->_isNegation)
		{
			$isMached = ! $isMached;
		}
		
		
		// condition is mached - show CSS
		if ($isMached)
		{
			return $content;
		}
		
		if ($this->_commentNotMatched)
		{
			//return sprintf('/* %s */', $content);
		}

		/**/
		return sprintf('/* operator:%s browser:%s  version:%s  content: %s */', $this->_currentOperator, 
											  strtoupper($this->_currentBrowserAbbrName), 
											  $this->_currentVersion, 
											  $content);
		/**/
	}

	protected function _parseCondition($condition)
	{
		$this->_isClosing  = false;
		$this->_isNegation = false;
		$this->_isVersion  = false;
		$this->_isBrowser  = false;
		$this->_isOperator = false;
		
		$this->_currentVersion  		= null;
		$this->_currentOperator			= null;
		$this->_currentBrowserAbbrName  = null;

		// remove "empty" keywords
		$condition = str_replace('if', '', $condition);
		$condition = trim($condition);

		// check it's condition close
		$this->_isClosing = ($condition{0} == '/');
		if ($this->_isClosing)
		{
			$condition = substr($condition, 1);
			$condition = trim($condition);
		}
		
		// check if its negation
		$this->_isNegation = ($condition{0} == '!');
		if ($this->_isNegation)
		{
			$condition = substr($condition, 1);
			$condition = trim($condition);
		}
		
		// find browser & version
		$condition = strtolower($condition);
		$conditionParts = explode(' ', $condition);
		$conditionParts = array_filter($conditionParts);

		while (($part = array_shift($conditionParts)) && (!$this->_isVersion || !$this->_currentBrowserAbbrName || !$this->_isOperator))
		{
			if (!$this->_isOperator && in_array($part, $this->_availableOperators))
			{
				$this->_isOperator = true;
				$this->_currentOperator = $part;
				continue;
			}

			if (!$this->_isBrowser && in_array($part, $this->_browserNameAsAbbrName))
			{
				$this->_isBrowser = true;
				$this->_currentBrowserAbbrName = $part;
				continue;
			}
			
			if (!$this->_isVersion && is_numeric($part))
			{
				$this->_isVersion = true;
				$this->_currentVersion = $part;
				continue;
			}
		}
	}
	
	protected function _errorNear($errorMessage, $content)
	{
		return sprintf("\n\n".'/*!!!'."\n\t".'ERROR: %s '."\n".'*/'."\n".'%s', $errorMessage, $content);
	}
}