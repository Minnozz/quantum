<?php
	class QTMTemplate {
		const SECTION_FORMAT = '[0-9A-Z_]+';
		const KEY_FORMAT = '[0-9A-Z_]+';

		const SECTION_START_START = '[[[';
		const SECTION_START_END = ']]]';
		const SECTION_END_START = '[[[/';
		const SECTION_END_END = ']]]';

		const KEY_START = '{';
		const KEY_END = '}';

		protected $sections = array();
		protected $data = array();

		public function __construct($file) {
			$this->addTemplate($file);
		}

		public function addKey($key, $value) {
			$this->data[$key] = $value;
		}

		public function concatKey($key, $value) {
			if(!isset($this->data[$key])) {
				$this->data[$key] = $value;
			} else {
				$this->data[$key] .= $value;
			}
		}

		public function clear($key) {
			unset($this->data[$key]);
		}

		public function getValue($key) {
			return isset($this->data[$key]) ? $this->data[$key] : '';
		}

		public function writeSection($section, $concat = true) {
			if(!isset($this->sections[$section])) {
				throw new QTMTemplateException('Section '. $section .' does not exist');
			}

			$output = $this->replaceChildSections($section);
			$output = $this->replaceKeys($output);

			if($concat) {
				$this->sections[$section]['output'] .= $output;
			} else {
				$this->sections[$section]['output'] = $output;
			}

			return $this->sections[$section]['output'];
		}

		public function insertSection($section, $key, $concat = true) {
			$this->addKey($key, $this->writeSection($section, $concat));
		}

		public function concatSection($section, $key, $concat = true) {
			$this->concatKey($key, $this->writeSection($section, $concat));
		}

		public function addTemplate($file) {
			if(!is_file($file) || !is_readable($file)) {
				throw new QTMTemplateException('Template file '. $file .' does not exist or is not readable');
			}

			$raw = file_get_contents($file);

			$this->parseSections($raw);
		}

		protected function parseSections($raw, $parent = null) {
			preg_match_all(
				'#'.
					preg_quote(self::SECTION_START_START, '#') .'('. self::SECTION_FORMAT .'?)'. preg_quote(self::SECTION_START_END, '#') .
					'(.+?)'.
					preg_quote(self::SECTION_END_START, '#') .'\1'. preg_quote(self::SECTION_END_END, '#') .
				'#ms', $raw, $matches
			);

			foreach($matches[1] as $i => $section) {
				if(isset($this->sections[$section])) {
					throw new QTMTemplateException('Duplicate template section '. $section);
				}

				$this->sections[$section] = array(
					'children'	=> array(),
					'raw'		=> $matches[2][$i],
					'output'	=> ''
				);

				if($parent) {
					$this->sections[$parent]['children'][] = $section;
				}

				$this->parseSections($matches[2][$i], $section);
			}
		}

		protected function replaceChildSections($section) {
			$raw = $this->sections[$section]['raw'];

			if(empty($this->sections[$section]['children'])) {
				return $raw;
			}

			foreach($this->sections[$section]['children'] as $child) {
				$raw = preg_replace(
					'#'.
						preg_quote(self::SECTION_START_START, '#') . $child . preg_quote(self::SECTION_START_END, '#') .
						'(.+?)'.
						preg_quote(self::SECTION_END_START, '#') . $child . preg_quote(self::SECTION_END_END, '#') .
					'#ms', $this->sections[$child]['output'], $raw
				);

				$this->sections[$child]['output'] = '';
			}

			return $raw;
		}

		protected function replaceKeys($raw) {
			return preg_replace_callback(
				'#'. preg_quote(self::KEY_START, '#') .'('. self::KEY_FORMAT .'?)'. preg_quote(self::KEY_END, '#') .'#',
				array($this, 'callbackReplaceKeys'), $raw
			);
		}

		private function callbackReplaceKeys($args) {
			return $this->getValue($args[1]);
		}
	}
?>
