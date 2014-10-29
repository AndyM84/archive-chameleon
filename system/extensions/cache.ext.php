<?php

	/***********************************************\
	 * N2F Yverdon v0                              *
	 * Copyright (c) 2009 Zibings Incorporated     *
	 *                                             *
	 * You should have received a copy of the      *
	 * Microsoft Reciprocal License along with     *
	 * this program.  If not, see:                 *
	 * <http://opensource.org/licenses/ms-rl.html> *
	\***********************************************/

	/*
	 * $Id: cache.ext.php 161 2011-07-10 05:46:54Z amale@EPSILON $
	 */

	// Our global variable(s)
	global $cfg;

	// Cache configuration
	$cfg['cache']['dir']		= './n2f_cache/';		# Cache File Directory (Must be writeable; Will be created automatically if it doesn't exist)
	$cfg['cache']['prefix']		= '';				# Cache File Prefix
	$cfg['cache']['ext']		= '.cache';			# Cache File Extension
	$cfg['cache']['ttl']		= 3600;				# Default amount of time before cache expires  (in seconds, 3600 = 1 hour)
	$cfg['cache']['gc'] 		= true;				# Clean up old cache files occasionally
	$cfg['cache']['memcached'] 	= false;				# Use MemCache by default
	$cfg['cache']['mc_persist']	= true;				# Use persistent Memcache connections
	$cfg['cache']['mc_compress']	= true;				# Use compression for MemCache (requires that zlib be installed)
	$cfg['cache']['mc_threshold']	= 15000;				# String length required before using compression in MemCache
	$cfg['cache']['mc_savings']	= 0.2;				# Minimum savings required to actually store the value compressed in MemCache (value between 0 an 1,  0.2 = 20%)
	$cfg['cache']['mc_servers']	= array(				# MemCache Servers - format:  'server:port' => 'weight'
		'localhost:11211' => '64'
	);

	// Global variable(s)
	$n2f = n2f_cls::getInstance();

	// Register extension
	$n2f->registerExtension(
		'cache',
		'n2f_cache',
		0.2,
		'Chris Dougherty',
		'http://n2framework.com/'
	);

	// Error constants
	define('CACHE_ERROR_MEMCACHED',			'0001');

	// English error strings
	L('en', 'CACHE_ERROR_MEMCACHED', "The Cache extension was unable to store '_%1%_' in Memcache.");

	// German error strings
	L('de', 'CACHE_ERROR_MEMCACHED', "The Cache extension was unable to store '_%1%_' in Memcache.");

	// Spanish error strings
	L('es', 'CACHE_ERROR_MEMCACHED', "The Cache extension was unable to store '_%1%_' in Memcache.");

	// Swedish error strings
	L('se', 'CACHE_ERROR_MEMCACHED', "The Cache extension was unable to store '_%1%_' in Memcache.");

	/**
	 * Cache class for N2 Framework Yverdon
	 *
	 */
	class n2f_cache {
		/**
		 * Config settings for n2f_cache object.
		 *
		 * @var array
		 */
		private $cfg;

		/**
		 * The Unique ID for the current cache item.
		 *
		 * @var string
		 */
		private $id;

		/**
		 * Use Memcache by default for items stored with the current cache object.
		 *
		 * @var boolean
		 */
		private $memcached;

		/**
		 * Memcache Object.
		 *
		 * @var object
		 */
		private $memcObj;

		/**
		 * The Time-to-Live for the current cache item (in seconds).
		 *
		 * @var integer
		 */
		private $ttl;

		/**
		 * Optional Cache item tag. This will be appended to the current cache item ID.
		 *
		 * @var string
		 */
		private $tag;

		/**
		 * Initializes a new n2f_cache object.
		 *
		 * @param integer $ttl		Default Time-to-Live for cache items created with this object.
		 * @param string $tag		Optional tag to make the cache item more unique (the same tag must be used to retrieve the cache)
		 * @param boolean $memc		Set true to use Memcache by default for all items cached through this object.
		 * @return n2f_cache
		 */
		public function __construct($ttl = null, $tag = "", $memc = null) {
			global $cfg;

			if (isset($cfg['cache']) && is_array($cfg['cache'])) {
				$this->cfg			= $cfg['cache'];
			}

			// Default Settings (if others cannot be found)
			// Cache File Directory (Must be writeable; Will be created automatically if it doesn't exist)
			$this->cfg['dir']			= ((isset($this->cfg['dir'])) ? $this->cfg['dir'] : './n2f_cache/');
			// Cache File Prefix
			$this->cfg['prefix']		= ((isset($this->cfg['prefix'])) ? $this->cfg['prefix'] : '');
			// Cache File Extension
			$this->cfg['ext']			= ((isset($this->cfg['ext'])) ? $this->cfg['ext'] : '.cache');
			// Default amount of time before cache expires  (in seconds, 3600 = 1 hour)
			$this->cfg['ttl']			= ((isset($this->cfg['ttl'])) ? $this->cfg['ttl'] : 3600);
			// Clean up old cache files occasionally
			$this->cfg['gc']			= ((isset($this->cfg['gc'])) ? $this->cfg['gc'] : true);
			// Use Memcache by default
			$this->cfg['memcached']		= ((isset($this->cfg['memcached'])) ? $this->cfg['memcached'] : false);
			// Use persistent Memcache connections
			$this->cfg['mc_persist']		= ((isset($this->cfg['mc_persist'])) ? $this->cfg['mc_persist'] : true);
			// Use compression for MemCache (requires that zlib be installed)
			$this->cfg['mc_compress']	= ((isset($this->cfg['mc_compress'])) ? $this->cfg['mc_compress'] : true);
			// String length required before using compression in MemCache
			$this->cfg['mc_threshold']	= ((isset($this->cfg['mc_threshold'])) ? $this->cfg['mc_threshold'] : 15000);
			// Minimum savings required to actually store the value compressed in MemCache (value between 0 an 1,  0.2 = 20%)
			$this->cfg['mc_savings']		= ((isset($this->cfg['mc_savings'])) ? $this->cfg['mc_savings'] : 0.2);
			// MemCache Servers - format:  'server:port' => 'weight'
			$this->cfg['mc_servers']		= ((isset($this->cfg['mc_servers'])) ? $this->cfg['mc_servers'] : array('localhost:11211' => '64'));

			$this->memcached = $this->cfg['memcached'];

			if (($memc != null) && ($memc != $this->cfg['memcached'])) {
				$this->memcached = $memc;
			}

			if (($ttl != null) && (is_numeric($ttl))) {
				$this->ttl = $ttl;
				$this->cfg['ttl'] = $ttl;
			}

			$this->tag = $tag;

			if (function_exists("memcache_connect")) {
				$this->memcObj = new Memcache;

				foreach ($this->cfg['mc_servers'] as $server => $weight) {
					list($host, $port) = explode(':', $server);
					$this->memcObj->addServer($host, $port, $this->cfg['mc_persist'], $weight);
				}
			} else {
				$this->memcached = false;
				$this->cfg['memcached'] = false;
			}

			return $this;
		}


		/**
		 * Write function wrapper
		 *
		 * @param string $id		Unique ID for this item
		 * @param integer $ttl		Amount of time before this cache item expires (in seconds)
		 * @param mixed $data		Data to be cached
		 * @param boolean $memcache	Whether or not to use Memcache
		 */
		protected function writer($id, $ttl, $data, $memcache = null) {
			if ($memcache === true) {
				$this->mwrite($id, $ttl, $data);
				return;
			}

			$this->write($id, $ttl, $data);
		}

		/**
		 * Stores cached data
		 *
		 * @param string $id		Unique ID for this data
		 * @param integer $ttl		Amount of time before this cache expires (in seconds)
		 * @param mixed $data		Data to be cached
		 */
		protected function write($id, $ttl, $data) {
			$filename = $this->makeFilename($id);

			if ($filep = fopen($filename, 'wb')) {
				if (flock($filep, LOCK_EX)) {
					fwrite($filep, $data);
				}

				fclose($filep);
				touch($filename, time() + $ttl);
			}
		}

		/**
		 * Stores data to Memcache
		 *
		 * @param string $id		Unique ID for this data
		 * @param integer $ttl		Amount of time before this cache item expires (in seconds)
		 * @param mixed $data		Data to be cached
		 */
		protected function mwrite($id, $ttl, $data) {
			$n2f = n2f_cls::getInstance();
			$key = $this->makeKey($id);

			$mem_compress = false;
			$result = null;

			if ($this->cfg['mc_compress'] === true) {
				if (function_exists("memcache_set_compress_threshold")) {
					$this->memcObj->setCompressThreshold($this->cfg['mc_threshold'], $this->cfg['mc_savings']);
					$mem_compress = true;
				}
			}

			if (!$this->isCached($id, "", true)) {
				if (!$result = $this->memcObj->replace($key, $data, (($mem_compress === false) ? 0 : MEMCACHE_COMPRESSED), $ttl)) {
					$result = $this->memcObj->set($key, $data, (($mem_compress === false) ? 0 : MEMCACHE_COMPRESSED), $ttl);
				}
			}

			// If $result == false, we failed.. Need to throw an error or something...
			if ($result === false) {
				$n2f->debug->throwError(CACHE_ERROR_MEMCACHED, S('CACHE_ERROR_MEMCACHED', array($id)), 'system/extensions/cache.ext.php');
			}
		}

		/**
		 * Builds the /path/filename  -  Creates the cache directory if it does not exist
		 *
		 * @param string $id		Unique ID for this file
		 * @param string $tag		Optional tag to make this cache file more unique (the same tag must be used to retrieve the cache)
		 * @return string			Path and Filename of cache file
		 */
		protected function makeFilename($id, $tag = "") {
			// Create the cache directory if it doesn't exist
			if (!is_dir($this->cfg['dir'])) {
				mkdir($this->cfg['dir'], 0777, true);
			}

			clearstatcache();
			$ftag = "";

			if ($tag != "") {
				$this->tag = $tag;
			}

			if ($this->tag != "") {
				$ftag = "-{$this->tag}";
			}

			$hash = sha1($id);

			return "{$this->cfg['dir']}{$this->cfg['prefix']}{$hash}{$ftag}{$this->cfg['ext']}";
		}

		/**
		 * Formats a Key string for storing an item in Memcache
		 *
		 * @param string $id		Unique ID for this item
		 * @param string $tag		Optional tag to make this cache item key more unique (the same tag must be used to retrieve this cache item)
		 * @return string			Key for this cache item
		 */
		protected function makeKey($id, $tag = "") {
			$ftag = "";

			if ($tag != "") {
				$this->tag = $tag;
			}

			if ($this->tag != "") {
				$ftag = "-{$this->tag}";
			}

			$hash = sha1($id);

			return "{$this->cfg['prefix']}{$hash}{$ftag}";
		}

		/**
		 * Read function wrapper
		 *
		 * @param string $id		Unique ID for this item
		 * @param boolean $memcache	Whether or not to use Memcache
		 * @return string			Contents of the cached item
		 */
		protected function reader($id, $memcache = null) {
			if ($memcache === true) {
				return $this->mread($id);
			}
			return $this->read($id);
		}
		/**
		 * Reads data from cache
		 *
		 * @param string $id		Unique ID for this file
		 * @return string			Contents of cache file
		 */
		protected function read($id) {
			$filename = $this->makeFilename($id);

			return file_get_contents($filename);
		}

		/**
		 * Reads data from Memcache
		 *
		 * @param string $id		Unique ID for this item
		 * @return string			Contents of cache item
		 */
		protected function mread($id) {
			$key = $this->makeKey($id);

			return $this->memcObj->get($key);
		}

		/**
		 * Cleans the cache directory periodically.
		 *
		 */
		public function cleanCache() {
			if ($this->cfg['gc'] === true) {
				if (!is_dir($this->cfg['dir'])) {
					return false;
				}

				if ((time() % 5) == 0) {
					if ($dh = @opendir($this->cfg['dir'])) {
						clearstatcache();

						while (($file = readdir($dh)) !== false) {
							$del_file = false;

							if (!is_dir($file)) {
								if (($this->cfg['ext'] != "") && (strpos($file, $this->cfg['ext']) !== false)) {
									$del_file = true;
								} elseif ($this->cfg['ext'] == "") {
									$del_file = true;
								} else {
									$del_file = false;
								}

								if ($del_file) {
									if (file_exists($this->cfg['dir'].$file) && (@filemtime($this->cfg['dir'].$file) < time())) {
										@unlink($this->cfg['dir'].$file);
									}
								}
							}
						}
					}
				}
			}
		}

		/**
		 * Checks if $id is cached
		 *
		 * @param string $id		Unique ID for this cache
		 * @param string $tag		Optional tag that was used when this cache was created
		 * @param boolean $memcache	Use Memcache
		 * @return boolean
		 */
		public function isCached($id, $tag = "", $memcache = null) {
			if (($memcache !== true) && ($memcache !== false)) {
				$memcache = $this->memcached;
			}

			if ($memcache === true) {
				$key = $this->makeKey($id, $tag);
				return $this->memcObj->get($key);
			}

			$filename = $this->makeFilename($id, $tag);

			if (file_exists($filename) && (filemtime($filename) > time())) {
				return true;
			}

			// Delete cache file if it's older than the Time-to-Live
			@unlink($filename);
			$this->cleanCache();

			return false;
		}

		/**
		 * Checks if $id is cached, if true it returns the cache.
		 * If false, it returns FALSE.
		 *
		 * @param string $id		Unique ID for this item
		 * @param string $tag		Optional tag to make this cache item ID more unique (the sam tag must be used to retrieve the cache)
		 * @param boolean $memcache	If true, force fetching this item from Memcache; if false, use the default storage method (which may well be Memcache)
		 * @return mixed			Will return the cached data if available. Otherwise it returns FALSE.
		 */
		public function getCache($id, $tag = "", $memcache = null) {
			if ($tag != "") {
				$this->tag = $tag;
			}

			if (($memcache !== true) && ($memcache !== false)) {
				$memcache = $this->memcached;
			}

			if ($this->isCached($id, $tag, $memcache)) {
				$contents = $this->reader($id, $memcache);

				return $contents;
			}

			return false;
		}

		/**
		 * Checks if $id is already cached, if true it returns the cache.
		 * If false, it starts buffering everything until $this->endCaching() is called.
		 * Use this for caching compiled PHP, HTML, Text, etc..
		 *
		 * @param string $id		Unique ID for this item
		 * @param integer $ttl		Amount of time before this cache expires (in seconds)
		 * @param string $tag		Optional tag to make this cache item ID more unique (the same tag must be used to retrieve the cache)
		 * @param boolean $memcache	If true, store this item in Memcache; if false, store in a file; otherwise use the default storage method (which may well be Memcache)
		 * @param boolean $return	If true and the item is cached, return the contents of the cache; otherwise echo the contents of the cache
		 */
		public function startCaching($id, $ttl = null, $tag = "", $memcache = null, $return = false) {
			if ($tag != "") {
				$this->tag = $tag;
			}

			if (($memcache !== true) && ($memcache !== false)) {
				$memcache = $this->memcached;
			}

			if ($contents = $this->getCache($id, $tag, $memcache)) {
				if ($return) {
					return $contents;
				} else {
					echo $contents;
					return true;
				}
			} else {
				ob_start();

				if ($ttl == null) {
					$ttl = $this->cfg['ttl'];
				}

				$this->id = $id;
				$this->ttl = $ttl;

				return false;
			}
		}

		/**
		 * Ends caching and writes the buffer data to disk.
		 *
		 * @param boolean $return	Determines if you want the buffer to be output.
		 * @param boolean $memcache	If true, store this item in Memcache; if false, store in a file; otherwise use the default storage method (which may well be Memcache)
		 */
		public function endCaching($return = true, $memcache = null) {
			$data = ob_get_contents();
			ob_end_clean();

			if (($memcache !== true) && ($memcache !== false)) {
				$memcache = $this->memcached;
			}

			$this->writer($this->id, $this->ttl, $data, $memcache);

			if ($return) {
				echo $data;
			}
		}

		/**
		 * Gets serialized data from the cache.
		 *
		 * @param string $id		Unique ID for this item.
		 * @param string $tag		The tag used when this data was cached (Optional)
		 * @param boolean $memcache	Whether or not to use Memcache
		 * @return mixed			Resulting data or null.
		 */
		public function getObject($id, $tag = "", $memcache = null) {
			if ($tag != "") {
				$this->tag = $tag;
			}

			if (($memcache !== true) && ($memcache !== false)) {
				$memcache = $this->memcached;
			}

			if ($this->isCached($id, $tag, $memcache)) {
				$retVal = $this->reader($id, $memcache);

				if ($memcache !== true) {
					$retVal = unserialize($retVal);
				}

				return $retVal;
			}

			return null;
		}

		/**
		 * Caches data in serialized form.
		 * Use this for caching Arrays/Objects, such as MySQL Query result arrays.
		 *
		 * @param string $id		Unique ID for this data
		 * @param mixed $data		Data to be cached
		 * @param integer $ttl		Amount of time before this cache expires (in seconds)
		 * @param string $tag		Optional tag to make this cache item ID more unique (the same tag must be used to retrieve the cache)
		 * @param boolean $memcache	If true, store this item in Memcache; if false, store in a file; otherwise use the default storage method (which may well be Memcache)
		 */
		public function setObject($id, $data, $ttl = null, $tag = "", $memcache = null) {
			if (($memcache !== true) && ($memcache !== false)) {
				$memcache = $this->memcached;
			}

			if (is_int($data) && !is_int($ttl)) {
				$tmp = $data;
				$data = $ttl;
				$ttl = $tmp;
			}

			if ($ttl == null) {
				$ttl = $this->cfg['ttl'];
			}

			if ($tag != "") {
				$this->tag = $tag;
			}

			$this->writer($id, $ttl, (($memcache === true) ? $data : serialize($data)), $memcache);
		}

		/**
		 * Delete function wrapper
		 *
		 * @param string $id		Unique ID for this item
		 * @param string $tag		The tag used when this data was cached (Optional)
		 * @param boolean $memcache	Whether or not to use Memcache
		 * @param integer $timeout	How log to wait before deleting the item (in seconds;  Only applies if $memcache = true)
		 * @return boolean
		 */
		public function delete($id, $tag = "", $memcache = null, $timeout = 0) {
			if ($tag != "") {
				$this->tag = $tag;
			}

			if ($memcache === true) {
				return $this->mdelete($id, $timeout);
			}

			return $this->fdelete($id);
		}

		/**
		 * Deletes data from file cache (Use the delete wrapper function)
		 *
		 * @param string $id		Unique ID for this item
		 * @param string $tag		The tag used when this data was cached (Optional)
		 * @return boolean
		 */
		protected function fdelete($id) {
			$filename = $this->makeFilename($id);

			$retVal = false;

			if (file_exists($filename)) {
				// Delete cache file
				$retVal = @unlink($filename);
				$this->cleanCache();
			}

			return $retVal;
		}

		/**
		 * Deletes data from Memcache (Use the delete wrapper function)
		 *
		 * @param string $id		Unique ID for this item
		 * @param integer $timeout	How log to wait before deleting the item (in seconds)
		 * @return boolean
		 */
		protected function mdelete($id, $timeout = 0) {
			$key = $this->makeKey($id);

			return $this->memcObj->delete($key, $timeout);
		}
	}

?>