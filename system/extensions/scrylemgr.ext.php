<?php

	// Register our extension
	n2f_cls::getInstance()->registerExtension('scrylemgr', 'Scryle Manager', '2.0', 'Andrew Male & Chris Dougherty', 'http://n2fyscrylemgr.codeplex.com/');

	// Include our utility classes
	n2f_cls::getInstance()->requireExtensions(array('scrylemgr/jsmin'));

	// Attach to the core load event, because we REALLY should
	n2f_cls::getInstance()->hookEvent(N2F_EVT_CORE_LOADED, array('scrylemgr', '_handleCoreLoad'));

	// Our available options
	define('SCRYLEOPT_BASEURL_EXEMPT',			1);
	define('SCRYLEOPT_MINIFY_EXEMPT',			2);
	define('SCRYLEOPT_COMBINE_EXEMPT',			4);
	define('SCRYLEOPT_COMPRESS_EXEMPT',		8);

	/**
	 * Scryle Manager Class for N2F Yverdon v0.3+
	 *
	 */
	class scrylemgr {
		/**
		 * Protected list of javascript files.
		 *
		 * @var array
		 */
		protected static $jsFiles = array();
		/**
		 * Protected list of stylesheet files.
		 *
		 * @var array
		 */
		protected static $cssFiles = array();
		/**
		 * Protected list of meta tags.
		 *
		 * @var array
		 */
		protected static $metaTags = array();
		/**
		 * Protected status of tag registration.
		 *
		 * @var boolean
		 */
		protected static $registered = false;

		/**
		 * Static method to add a javascript file to the managed list.
		 *
		 * @param string $key		String value to represent this file (for dependencies).
		 * @param string $filename	String value of javascript file location.
		 * @param mixed $requires	String or array of strings for javascript files this file depends upon.
		 * @param integer $options	Integer with SCRYLEOPT_* options for this file.
		 */
		public static function addJsFile($key, $filename, $requires = null, $options = null) {
			self::registerTags();

			if (isset(self::$jsFiles[$key])) {
				return;
			}

			if ($requires !== null) {
				if (is_array($requires) && count($requires) > 0) {
					foreach (array_values($requires) as $req) {
						if (!isset(self::$jsFiles[$req])) {
							return;
						}
					}
				} else {
					if (!isset(self::$jsFiles[$requires])) {
						return;
					}
				}
			}

			self::$jsFiles[$key] = array('filename' => $filename, 'options' => $options);

			return;
		}

		/**
		 * Static method to add a stylesheet file to the managed list.
		 *
		 * @param string $key		String value to represent this file (for dependencies).
		 * @param string $filename	String value of stylesheet file location.
		 * @param mixed $requires	String or array of strings for stylesheet files this file depends upon.
		 * @param integer $options	Integer with SCRYLEOPT_* options for this file.
		 */
		public static function addCssFile($key, $filename, $requires = null, $options = null) {
			self::registerTags();

			if (isset(self::$cssFiles[$key])) {
				return;
			}

			if ($requires !== null) {
				if (is_array($requires) && count($requires) > 0) {
					foreach (array_values($requires) as $req) {
						if (!isset(self::$cssFiles[$req])) {
							return;
						}
					}
				} else {
					if (!isset(self::$cssFiles[$requires])) {
						return;
					}
				}
			}

			self::$cssFiles[$key] = array('filename' => $filename, 'options' => $options);

			return;
		}

		/**
		 * Static method to add a meta tag to the managed list.
		 *
		 * @param string $key		String value to represent this meta tag in list.
		 * @param array $attributes	Array of attribute=>value combinations for creating meta tag.
		 */
		public static function addMetaTag($key, array $attributes) {
			self::registerTags();

			if (isset(self::$metaTags[$key])) {
				return;
			}

			if (count($attributes) < 1) {
				return;
			}

			self::$metaTags[$key] = $attributes;

			return;
		}

		/**
		 * Internal method to process binding results in the template.  Shouldn't be used.
		 */
		public static function processJsTag(n2f_template $tpl, $currentContent, $originalContent) {
			$attribs = self::getAttributes('jsinc', $originalContent);
			$cache = new n2f_cache($attribs['cacheExpire'], 'jsinc');
			$ret = '';

			if (count(self::$jsFiles) > 0) {
				$ret = "<!-- JS Files (Generated by Scryle Manager) -->";

				if (!$attribs['doCombine']) {
					foreach (array_values(self::$jsFiles) as $js) {
						$file = ($js['options'] & SCRYLEOPT_BASEURL_EXEMPT) ? $js['filename'] : $attribs['baseUrl'] . $js['filename'];

						if (($attribs['doMinify'] && !($js['options'] & SCRYLEOPT_MINIFY_EXEMPT)) || ($attribs['doCompress'] && !($js['options'] & SCRYLEOPT_COMPRESS_EXEMPT))) {
							$fileHash = self::getFileHash($file, $attribs, $js['options']);

							if (!$cache->isCached($fileHash)) {
								if (file_exists($file)) {
									$contents = file_get_contents($file);

									if ($contents !== false) {
										if ($attribs['doMinify'] && !($js['options'] & SCRYLEOPT_MINIFY_EXEMPT)) {
											$contents = JSMin::minify($contents);
										}

										if ($attribs['doCompress'] && !($js['options'] & SCRYLEOPT_COMPRESS_EXEMPT)) {
											$contents = gzencode($contents);
										}

										$cache->startCaching($fileHash);

										echo($contents);

										$cache->endCaching(false);
									}
								}
							}

							$ret .= "\n{$attribs['tabPrefix']}<script type=\"text/javascript\" src=\"?jsinc={$fileHash}\"></sc" . "ript>";
						} else {
							$ret .= "\n{$attribs['tabPrefix']}<script type=\"text/javascript\" src=\"{$file}\"></sc" . "ript>";
						}
					}
				} else {
					$fileList = array();
					$bigFileHash = '';
					$bigFileContents = '';

					foreach (array_values(self::$jsFiles) as $js) {
						$file = ($js['options'] & SCRYLEOPT_BASEURL_EXEMPT) ? $js['filename'] : $attribs['baseUrl'] . $js['filename'];

						if ($js['options'] & SCRYLEOPT_COMBINE_EXEMPT) {
							if ($attribs['doMinify'] || $attribs['doCompress']) {
								$fileHash = self::getFileHash($file, $attribs, $js['options']);

								if (!$cache->isCached($fileHash)) {
									$contents = file_get_contents($file);

									if ($contents === false) {
										continue;
									}

									if ($attribs['doMinify'] && !($js['options'] & SCRYLEOPT_MINIFY_EXEMPT)) {
										$contents = JSMin::minify($contents);
									}

									if ($attribs['doCompress'] && !($js['options'] & SCRYLEOPT_COMPRESS_EXEMPT)) {
										$contents = gzencode($contents);
									}

									$cache->startCaching($fileHash);

									echo($contents);

									$cache->endCaching(false);
								}

								$ret .= "\n{$attribs['tabPrefix']}<script type=\"text/javascript\" src=\"?jsinc={$fileHash}\"></sc" . "ript>";

								continue;
							}

							$ret .= "\n{$attribs['tabPrefix']}<script type=\"text/javascript\" src=\"{$file}\"></sc" . "ript>";

							continue;
						}

						$fileHash = sha1($file);
						$fileList[$fileHash] = array('file' => $file, 'contents' => null, 'options' => $js['options']);
						$bigFileHash .= $fileHash;
					}

					$bigFileHash .= sha1($bigFileHash) . '|' . (($attribs['doMinify']) ? 'm' : '') . (($attribs['doCompress']) ? 'c' : '');

					if (!$cache->isCached($bigFileHash)) {
						foreach (array_values($fileList) as $info) {
							if (file_exists($info['file'])) {
								$info['contents'] = file_get_contents($info['file']);

								if ($info['contents'] !== false) {
									if ($attribs['doMinify'] && !($info['options'] & SCRYLEOPT_MINIFY_EXEMPT)) {
										$info['contents'] = JSMin::minify($info['contents']);
									}

									$bigFileContents .= "\n" . $info['contents'];
								}
							}
						}

						if ($attribs['doCompress']) {
							$bigFileContents = gzencode($bigFileContents);
						}

						$cache->startCaching($bigFileHash);

						echo($bigFileContents);

						$cache->endCaching(false);
					}

					$ret .= "\n{$attribs['tabPrefix']}<script type=\"text/javascript\" src=\"?jsinc={$bigFileHash}\"></sc" . "ript>";
				}

				$ret .= "\n{$attribs['tabPrefix']}<!-- End JS Files -->\n";
			}

			return($ret);
		}

		/**
		 * Internal method to process binding results in the template.  Shouldn't be used.
		 */
		public static function processCssTag(n2f_template $tpl, $currentContent, $originalContent) {
			$attribs = self::getAttributes('cssinc', $originalContent);
			$cache = new n2f_cache($attribs['cacheExpire'], 'cssinc');
			$ret = '';

			$attribs['doMinify'] = false;

			if (count(self::$cssFiles) > 0) {
				$ret = "<!-- CSS Files (Generated by Scryle Manager) -->";

				if (!$attribs['doCombine']) {
					foreach (array_values(self::$cssFiles) as $css) {
						$file = ($css['options'] & SCRYLEOPT_BASEURL_EXEMPT) ? $css['filename'] : $attribs['baseUrl'] . $css['filename'];

						if ($attribs['doCompress'] && !($css['options'] & SCRYLEOPT_COMPRESS_EXEMPT)) {
							$fileHash = self::getFileHash($file, $attribs, $css['options']);

							if (!$cache->isCached($fileHash)) {
								if (file_exists($file)) {
									$contents = file_get_contents($file);

									if ($contents !== false) {
										if ($attribs['doCompress'] && !($css['options'] & SCRYLEOPT_COMPRESS_EXEMPT)) {
											$contents = gzencode($contents);
										}

										$cache->startCaching($fileHash);

										echo($contents);

										$cache->endCaching(false);
									}
								}
							}

							$ret .= "\n{$attribs['tabPrefix']}<link rel=\"stylesheet\" type=\"text/css\" href=\"?cssinc={$fileHash}\" />";
						} else {
							$ret .= "\n{$attribs['tabPrefix']}<link rel=\"stylesheet\" type=\"text/css\" href=\"{$file}\" />";
						}
					}
				} else {
					$fileList = array();
					$bigFileHash = '';
					$bigFileContents = '';

					foreach (array_values(self::$cssFiles) as $css) {
						$file = ($css['options'] & SCRYLEOPT_BASEURL_EXEMPT) ? $css['filename'] : $attribs['baseUrl'] . $css['filename'];

						if ($css['options'] & SCRYLEOPT_COMBINE_EXEMPT) {
							if ($attribs['doCompress']) {
								$fileHash = self::getFileHash($file, $attribs, $css['options']);

								if (!$cache->isCached($fileHash)) {
									$contents = file_get_contents($file);

									if ($contents === false) {
										continue;
									}

									if ($attribs['doCompress'] && !($css['options'] & SCRYLEOPT_COMPRESS_EXEMPT)) {
										$contents = gzencode($contents);
									}

									$cache->startCaching($fileHash);

									echo($contents);

									$cache->endCaching(false);
								}

								$ret .= "\n{$attribs['tabPrefix']}<link rel=\"stylesheet\" type=\"text/css\" href=\"?cssinc={$fileHash}\" />";

								continue;
							}

							$ret .= "\n{$attribs['tabPrefix']}<link rel=\"stylesheet\" type=\"text/css\" href=\"{$file}\" />";

							continue;
						}

						$fileHash = sha1($file);
						$fileList[$fileHash] = array('file' => $file, 'contents' => null);
						$bigFileHash .= $fileHash;
					}

					$bigFileHash = sha1($bigFileHash) . '|' . (($attribs['doCompress']) ? 'c' : '');

					if (!$cache->isCached($bigFileHash)) {
						foreach ($fileList as $hash => $info) {
							if (file_exists($info['file'])) {
								$info['contents'] = file_get_contents($info['file']);

								if ($info['contents'] !== false) {
									$bigFileContents .= "\n" . $info['contents'];
								}
							}
						}

						if ($attribs['doCompress']) {
							$bigFileContents = gzencode($bigFileContents);
						}

						$cache->startCaching($bigFileHash);

						echo($bigFileContents);

						$cache->endCaching(false);
					}

					$ret .= "\n{$attribs['tabPrefix']}<link rel=\"stylesheet\" type=\"text/css\" href=\"?cssinc={$bigFileHash}\" />";
				}

				$ret .= "\n{$attribs['tabPrefix']}<!-- End CSS Files -->\n";
			}

			return($ret);
		}

		/**
		 * Internal method to process binding results in the template.  Shouldn't be used.
		 */
		public static function processMetaTag(n2f_template $tpl, $currentContent, $originalContent) {
			$attribs = self::getAttributes('metainc', $originalContent);
			$ret = '';

			if (count(self::$metaTags) > 0) {
				$ret = '<!-- Meta Tags (Generated by Scryle Manager) -->';

				foreach (array_values(self::$metaTags) as $meta) {
					$ret .= "\n{$attribs['tabPrefix']}<meta";

					foreach ($meta as $attrib => $value) {
						$ret .= " {$attrib}=\"{$value}\"";
					}

					$ret .= " />";
				}

				$ret .= "\n{$attribs['tabPrefix']}<!-- End Meta Tags -->";
			}

			return($ret);
		}

		/**
		 * Internal method to process requests for prepared versions of managed files.  Shouldn't be used.
		 */
		public static function _handleCoreLoad(n2f_cls $n2f, $results) {
			$_REQUEST['cacheExpire'] = (!isset($_REQUEST['cacheExpire'])) ? 1500 : intval($_REQUEST['cacheExpire']);
			$cache = new n2f_cache($_REQUEST['cacheExpire']);

			if (isset($_REQUEST['jsinc'])) {
				if (($cached = $cache->getCache($_REQUEST['jsinc'], 'jsinc')) !== false) {
					$parts = explode('|', $_REQUEST['jsinc']);

					if (!empty($parts[1]) && stripos($parts[1], 'c') !== false) {
						// Headers
						header("Content-type: text/javascript");
						header("Vary: Accept-Encoding");  // Handle proxies
						header("Expires: " . gmdate("D, d M Y H:i:s", time() + (3600 * 24 * 1)) . " GMT");
						header("Content-Encoding: gzip");
					}

					echo($cached);

					exit;
				}
			}

			if (isset($_REQUEST['cssinc'])) {
				if (($cached = $cache->getCache($_REQUEST['cssinc'], 'cssinc')) !== false) {
					$parts = explode('|', $_REQUEST['cssinc']);

					if (!empty($parts[1]) && stripos($parts[1], 'c') !== false) {
						// Headers
						header("Content-type: text/css");
						header("Vary: Accept-Encoding");  // Handle proxies
						header("Expires: " . gmdate("D, d M Y H:i:s", time() + (3600 * 24 * 1)) . " GMT");
						header("Content-Encoding: gzip");
					}

					echo($cached);

					exit;
				}
			}
		}

		/**
		 * Internal method for producing the hashed version of a filename.  Shouldn't be used.
		 */
		protected static function getFileHash($filename, $attribs, $options) {
			$ret = sha1($filename) . '|';

			if ($attribs['doMinify'] && !($options & SCRYLEOPT_MINIFY_EXEMPT)) {
				$ret .= 'm';
			}

			if ($attribs['doCompress'] && !($options & SCRYLEOPT_COMPRESS_EXEMPT)) {
				$ret .= 'c';
			}

			return($ret);
		}

		/**
		 * Internal method for template binding registration.  Shouldn't be used.
		 */
		protected static function registerTags() {
			if (self::$registered === true) {
				return;
			}

			if (!n2f_cls::getInstance()->hasExtension('n2f_template')) {
				return;
			}

			if (version_compare(N2F_VERSION, '0.4', '>=')) {
				n2f_template::setGlobalTag('jsinc', array('scrylemgr', 'processJsTag'));
				n2f_template::setGlobalTag('cssinc', array('scrylemgr', 'processCssTag'));
				n2f_template::setGlobalTag('metainc', array('scrylemgr', 'processMetaTag'));
			} else {
				if (class_exists('n2f_template_dynamic')) {
					n2f_template_dynamic::addGlobalBinding('#<jsinc(.*?)/>#is', array('scrylemgr', 'processJsTag'));
					n2f_template_dynamic::addGlobalBinding('#<cssinc(.*?)/>#is', array('scrylemgr', 'processCssTag'));
					n2f_template_dynamic::addGlobalBinding('#<metainc(.*?)/>#is', array('scrylemgr', 'processMetaTag'));
				}

				if (class_exists('n2f_template_static')) {
					n2f_template_static::addGlobalBinding('#<jsinc(.*?)/>#is', array('scrylemgr', 'processJsTag'));
					n2f_template_static::addGlobalBinding('#<cssinc(.*?)/>#is', array('scrylemgr', 'processCssTag'));
					n2f_template_static::addGlobalBinding('#<metainc(.*?)/>#is', array('scrylemgr', 'processMetaTag'));
				}
			}

			self::$registered = true;

			return;
		}

		/**
		 * Internal method for retrieving arguments from a returned tag.  Shouldn't be used.
		 */
		protected static function getAttributes($tagName, $tagContent) {
			if (class_exists('n2f_template_static')) {
				$attributes = n2f_template_static::getTagAttributes($tagName, $tagContent);
			} else {
				$attributes = self::_getAttributes($tagName, $tagContent);
			}

			$doCompress = false;
			$cacheExpire = 1500;
			$doCombine = false;
			$doMinify = false;
			$tabPrefix = '';
			$baseUrl = '';

			if (count($attributes) > 0) {
				foreach ($attributes as $name => $value) {
					switch (strtolower($name)) {
						case 'baseurl':
							$baseUrl = $value;
							break;
						case 'tabsize':
							$tabsize = intval($value);

							if ($tabsize > 0) {
								for ($i = 0; $i < $tabsize; $i++) {
									$tabPrefix .= "\t";
								}
							}

							break;
						case 'cacheexpire':
							$cacheExpire = intval($value);

							if ($cacheExpire < 1) {
								$cacheExpire = 1;
							}

							break;
						case 'minify':
							if (strtolower($value) == 'true') {
								$doMinify = true;
							}

							break;
						case 'combine':
							if (strtolower($value) == 'true') {
								$doCombine = true;
							}

							break;
						case 'compress':
							if (strtolower($value) == 'true') {
								$doCompress = true;
							}

							break;
					}
				}
			}

			return(array(
				'doCompress'	=> $doCompress,
				'cacheExpire'	=> $cacheExpire,
				'doCombine'	=> $doCombine,
				'doMinify'	=> $doMinify,
				'tabPrefix'	=> $tabPrefix,
				'baseUrl'		=> $baseUrl
			));
		}

		/**
		 * Internal helper method for retrieving arguments from a returned tag.  Shouldn't be used.
		 */
		protected static function _getAttributes($tagName, $tagContent) {
			$pattern = "#<{$tagName}(.*?)/>#is";
			$matches = array();
			$ret = array();

			if (preg_match_all($pattern, $tagContent, $matches)) {
				if (isset($matches[1]) && !empty($matches[1][0])) {
					$attribsString = trim($matches[1][0]);

					if (strlen($attribsString) > 0) {
						$attribGroups = explode(' ', $attribsString);

						if (count($attribGroups) > 0) {
							foreach (array_values($attribGroups) as $attrib) {
								$pair = explode('=', $attrib);

								if (($pair[1][0] == '"' && $pair[1][strlen($pair[1]) - 1] == '"') || ($pair[1][0] == "'" && $pair[1][strlen($pair[1]) - 1] == "'")) {
									$pair[1] = substr($pair[1], 1, (strlen($pair[1]) - 2));
								}

								$ret[$pair[0]] = $pair[1];
							}
						}
					}
				}
			}

			return($ret);
		}
	}

?>