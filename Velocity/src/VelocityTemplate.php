
<?php

include_once 'VelocitySource.php';
include_once 'VelocityEngine.php';

class VelocityTemplate {
	private $variables = array();
	
	public function __construct() {
		// Constructor
	}
	
	// Set variables.
	public function set($name, $value) {
		$this->variables[$name] = $value;
        return $this;
	}
	
	// Whole rendering process, decides whether to render a string or read from a file.
	public function render($template) {

		$engine = new VelocityEngine();

		ob_start();

		if($engine->getCompatibilityType($template) == CompatibilityType::Source) {

			include $template->getFile();
			$content = ob_get_clean();

		} else {

			if($engine->getCompatibilityType($template) == CompatibilityType::String) {

				$content = $template;

			} else if($engine->getCompatibilityType($template) == CompatibilityType::Incompatible) {

				throw new Exception('You have to pass either a string or a VelocitySource instance in render(), not a(n) '.gettype($template).'');

			}

		}
		
		// Handle includes
		$content = $engine->parseInclude($content, $this->variables);

		// Handle count() function
		$content = $engine->parseCount($content, $this->variables);

		// Handle loops
		$content = $engine->parseForLoop($content, $this->variables);

		// Handle foreach
		$content = $engine->parseForEach('LEGACY', $content, $this->variables);
		$content = $engine->parseForEach('PHP', $content, $this->variables);

		// Replace multi level variables
		$content = $engine->parseMultiLevelVariables($content, $this->variables);

		// Replace single level variables
		$content = $engine->parseVariables($content, $this->variables);
		
		return $content;
	}
}

?>