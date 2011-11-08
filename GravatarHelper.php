<?php
/**
 *
 * Gravatar Helper
 *
 * @package        app
 * @package        app.controller.component
 * @author         Chris Stevens
 * @link           https://github.com/stevenscg/cakephp-gravatar
 */
App::uses('AppHelper', 'View/Helper');
App::import(array('Security'));

class GravatarHelper extends AppHelper {
	/**
	 * Array of helpers needed
	 *
	 * @var array
	 */
	public $helpers = array('Html');

	/**
	 * Base gravatar url
	 *
	 * @var string
	 */
	private $base_url = 'http://www.gravatar.com/avatar/';

	/**
	 * Secure gravatar url
	 *
	 * @var string
	 */
	private $base_url_ssl = 'https://secure.gravatar.com/avatar/';

	/**
	 * Secure / SSL flag
	 *
	 * @var bool true to force ssl, false to force non-ssl, null to follow current proto
	 */
	private $ssl = null;

	/**
	 * Size of the image
	 *
	 * @var string
	 */
	private $size = 80;

	/**
	 * Gravatar rating
	 *
	 * @var string
	 */
	private $rating = 'pg';

	/**
	 * Array of possible ratings
	 *
	 * @var string
	 */
	private $possibleRatings = array('g', 'pg', 'r', 'x');

	/**
	 * Default image.  custom or [ 404 | mm | identicon | monsterid | wavatar ]
	 *
	 * @var string
	 */
	private $default = 'mm';


	/**
	 * Hash type used
	 *
	 * @var string
	 */
	private $hashType = 'md5';


	/**
	 * Validate the options array
	 *
	 * @param string $options
	 * @return void
	 */
	private function validateOptions($options) {
		// Define the 'is_ssl' method in AppHelper to
		// use auto-sensing of the current request protocol
		if (isset($options['ssl']) && ($options['ssl'] === true)) {
				$this->ssl = true;
		} else if (isset($options['ssl']) &&  ($options['ssl'] === false)) {
				$this->ssl = false;
		} else if (method_exists($this, 'is_ssl')) {
				$this->ssl = $this->is_ssl();
		}

		if (isset($options['rating'])) {
			if (in_array($this->rating, $this->possibleRatings)) {
				$this->rating = $options['rating'];
			}
		}

		if (isset($options['size'])) {
			if (is_numeric($options['size']) && $options['size'] >=1 && $options['size'] <= 512) {
				$this->size = $options['size'];
			}
		}

		if (isset($options['default'])) {
			if (strpos($options['default'], 'http') === 0) {
				$this->default = h($options['default']);
			} else {
				$this->default = hashEmail($options['default']);
			}
		}
	}


	/**
	 * Hash the email address
	 *
	 * @param string $email
	 * @return hash
	 * @author Chris Stevens
	 */
	private function hashEmail($email) {
		return Security::hash(strtolower(trim($email)), $this->hashType);
	}


	/**
	 * Generate the image url
	 *
	 * @param string $email 
	 * @param string $options 
	 * @return image url of the gravatar
	 * @author Chris Stevens
	 */
	public function url($email, $options=array()) {
		$this->validateOptions($options);

		$url_params = "?s=".$this->size."&r=".$this->rating;

		if (!empty($this->default)) {
			$url_params .= "&d=".$this->default;
		}

		if ($this->ssl) {
			return $this->base_url_ssl.$this->hashEmail($email).$url_params;
		}

		return $this->base_url.$this->hashEmail($email).$url_params;
	}


	/**
	 * Generate the image tag
	 *
	 * @param string $email 
	 * @param string $options 
	 * @param string $html_options 
	 * @return HTML image tag of the gravatar
	 * @author Chris Stevens
	 */
	public function image($email, $options, $html_options = array()) {
		return $this->Html->image($this->url($email, $options), $html_options);
	}
}
