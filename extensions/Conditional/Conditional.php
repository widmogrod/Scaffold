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
	
	protected $_isClosing;

	protected $_isNegation;
	
	protected $_isVersion;
	
	protected $_isBrowser;
	
	protected $_isOperator;
	
	protected $_currentVersion;
	
	protected $_currentOperator;
	
	protected $_currentBrowserAbbreviation;
	
	protected $_availableOperators = array('<',  '>',  '<=',  '>=',  '==', '<>', '!=',
										   'lt', 'gt', 'lte', 'gte', 'eq', 'neq');
	
	protected $_userAgent;
	
	protected $_browserData;
	
	protected $_browserNameAsAbbreviation = array(
		'firefox' => 'ff',
		'chrome'  => 'ch',
		'opera'   => 'o',
		'safari'  => 's',
		'msie'	  => 'ie',
		'netscape'=> 'n'
	);
	
	/**
	 * Scaffold's process hook
	 * @access public
	 * @param Scaffold
	 * @return void
	 */
	public function process($source,$scaffold)
	{
		$browserVersion = $this->getBrowserVersion();
		$browserName = $this->getBrowserName();

		// matching inline conditions
		$regexp = '/(\[([^\[\]]*)\]([^\n]+))/ie';
		$source->contents = preg_replace($regexp, "\$this->_parse('$2', '$0')", $source->contents);
		
		// matching block conditions
		$regexp = '/\[([^\[\]]*)\]([^\[\]]+)\[\/([^\[\]]*)\]/ie';
		$source->contents = preg_replace($regexp, "\$this->_parse('$1', '$2')", $source->contents);
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
	
	public function getBrowserVersion()
	{
		$this->getBrowserData('majorver');
	}
	
	public function getBrowserName($asAbbreviation = true)
	{
		$browser = $this->getBrowserData('browser');
		$browser = strtolower($browser);

		return array_key_exists($browser, $this->_browserNameAsAbbreviation)
			? $this->_browserNameAsAbbreviation[$browser]
			: self::DEFAULT_BROWSER_NAME;
	}

	
	protected function _parse($condition, $content)
	{
		$isMached = false;
		
		$this->_prepareCondition($condition);

		$browserVersion = $this->getBrowserVersion();
		$browserName = $this->getBrowserName();
		
		// browser type is mached
		if ($this->_currentBrowserAbbreviation == $browserName)
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
		
		return sprintf('/* operator:%s browser:%s  version:%s  content: %s */', $this->_currentOperator, 
											  strtoupper($this->_currentBrowserAbbreviation), 
											  $this->_currentVersion, 
											  $content);
		
	}

	protected function _prepareCondition($condition)
	{
		$this->_isClosing  = false;
		$this->_isNegation = false;
		$this->_isVersion  = false;
		$this->_isBrowser  = false;
		$this->_isOperator = false;
		
		$this->_currentVersion  		    = null;
		$this->_currentOperator			    = null;
		$this->_currentBrowserAbbreviation  = null;

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

		while(($part = array_shift($conditionParts)) && (!$this->_isVersion || !$this->_currentBrowserAbbreviation || !$this->_isOperator))
		{
			if (!$this->_isOperator && in_array($part, $this->_availableOperators))
			{
				$this->_isOperator = true;
				$this->_currentOperator = $part;
			}
			
			if (!$this->_isBrowser && in_array($part, $this->_browserNameAsAbbreviation))
			{
				$this->_isBrowser = true;
				$this->_currentBrowserAbbreviation = $part;
			}
			
			if (!$this->_isVersion && is_numeric($part))
			{
				$this->_isVersion = true;
				$this->_currentVersion = $part;
			}
		}
	}
}