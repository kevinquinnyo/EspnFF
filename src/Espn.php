<?php

namespace FF;

class Espn {

	protected $_options = [

		'url' => null,
		'username' => null,
		'password' => null
	
	];

	protected $_postfields = [

		'failedAttempts' => '0',
		'SUBMIT' => '1',
		'cookieDomain' => '.go.com',
		'multipleDomains' => 'true',
		'aff_code' => 'espn_fantgames',
	];

	public function __construct($login_url, array $options = []) {

		if(is_array($login_url)) {
			$this->setOption('login_url', 'https://r.espn.go.com/espn/memberservices/pc/login');
			$options = $login_url;
		} else {
			$this->setOption('login_url', $login_url);
		}
		

		if(!isset($options['username']) || !isset($options['password'])) {
			throw new \Exception('You must specify a username and password when instantiating the Espn class');
		}

		$this->setOption('username', $options['username']);
		$this->setOption('password', $options['password']);
		$this->setOption('cookie', dirname(__DIR__).'/tmp/cookies.txt');

	}

	public function setOption($name, $value = null) {
		if(is_array($name)) {
			foreach($name as $key => $value) {
				$this->setOption($key, $value);
			}
			return $this;
		}
	
		$this->_options[$name] = $value;
		return $name;
	}

	public function setPostfield($name, $value = null) {
		if(is_array($name)) {
			foreach($name as $key => $value) {
				$this->setPostfield($key, $value);
			}
			return $this;
		}
		$this->_postfields[$name] = $value;
		return $name;
	}

	public function getOption($name = null, $default = null) {
		if ($name === null) {
			$options = $this->_options;
			return $options;
		}
		if (array_key_exists($name, $this->_options)) {
			return $this->_options[$name];
		}
		return $default;
	}

	public function login() {

		$data = [

			'url' => $this->getOption('login_url'),	
			'params' => [	
				'submit' => 'Sign In',
			]
		];

		$result = $this->call($data);

		return isset($result) ? $result : false;
		
	}

	public function call(array $options = []) {
		
		$this->setPostfield('username', $this->getOption('username'));
		$this->setPostfield('password', $this->getOption('password'));

		$this->setPostfield($options['params']);

		$this->setOption('url', $options['url']);

		$Curl = curl_init($options['url']);
		$query_string = http_build_query($this->_postfields);

		curl_setopt($Curl, CURLOPT_URL, $this->getOption('url'));
		curl_setopt($Curl, CURLOPT_POST, 1);
		curl_setopt($Curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($Curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($Curl, CURLOPT_POSTFIELDS, $query_string);
		curl_setopt($Curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($Curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($Curl, CURLOPT_COOKIEJAR, $this->getOption('cookie'));
		
		$response = curl_exec($Curl);
		if (!$response) {
			$message = curl_error($Curl);
			curl_close($Curl);
			throw new \RuntimeException($message);
		}

		$this->_content['size'] = curl_getinfo($Curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
		$this->_content['type'] = curl_getinfo($Curl, CURLINFO_CONTENT_TYPE);

		//curl_close($Curl);
		return($response);
	}

}

?>
