<?php 

include_once 'CompatibilityType.php';

class VelocityEngine {


	// Get CompatibilityType enum based on $raw being a VelocitySource instance or a string. Returns CompatibilityType.
    public function getCompatibilityType($raw) {

        if($raw instanceof VelocitySource) {

			return CompatibilityType::Source;

		} else {

			if(is_string($raw)) {

				return CompatibilityType::String;

			} else {

				return CompatibilityType::Incompatible;

			}

		}


    }

	// Replace function to replace multiple types of occurences to the same string. Returns replaced string.
    public function replaceMult(Array $search, $replace, $subject) {

        $return = $subject;

        foreach($search as $searchable) {

            $subject = str_replace($searchable,$replace,$subject);

        }

        return $subject;

    }

	// Parse count() function. Returns parsed string.
    public function parseCount($content, $variables) {

        $pattern = '/count\s*(\(\$[a-zA-Z0-9]+\))/s';
		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

		foreach ($matches as $match) {
			
			$match[1] = $this->replaceMult(["$", "(", ")"],"",$match[1]);

			$forOutput = '';
			$forArray = $variables[$match[1]];
			
			if(is_array($forArray)) {

				$content = str_replace($match[0], count($forArray), $content);

			}
		}

        return $content;

    }

	// Parse include function. Returns parsed string.
    public function parseInclude($content, $variables) {

        $content = preg_replace_callback('/@\s*include\s+[\'"](.+?)[\'"]\s*/', function($matches) {
			ob_start();
			include $matches[1];
			return ob_get_clean();
		}, $content);

        return $content;

    }

	// Parse for loops. Returns parsed string.
    public function parseForLoop($content, $variables) {

        $content = preg_replace_callback('/@\s*for\s+(\$[a-zA-Z0-9]+)\s*=\s*([0-9]+)\s*;\s*(\$[a-zA-Z0-9]+)\s*<=\s*([0-9]+)\s*;\s*(\$[a-zA-Z0-9]+)\s*(\+\+|--)\s*(.*?)\@\s*endfor\s*/s', function($matches) {
			$loopContent = '';
			$i = (int) $matches[2];
			$limit = (int) $matches[4];
			$increment = $matches[6] == '++' ? 1 : -1;
			for (; $i <= $limit; $i += $increment) {
				$loopContent .= str_replace($matches[1], $i, $matches[7]);
			}
			return $loopContent;
		}, $content);

        return $content;

    }

	// Parse foreach. Returns parsed string.
    public function parseForEach($type, $content, $variables) {

        if($type == 'LEGACY') {
            $pattern = '/@\s*foreach\s+(\$[a-zA-Z0-9]+)\s+in\s+(\$[a-zA-Z0-9]+)\s*(.*?)\@\s*endforeach\s*/s';
        }

        if($type == 'PHP') {
            $pattern = '/@\s*foreach\s+(\$[a-zA-Z0-9]+)\s+as\s+(\$[a-zA-Z0-9]+)\s*(.*?)\@\s*endforeach\s*/s';
        }

		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

		foreach ($matches as $match) {

            if($type == 'PHP') {

                $match1 = $match[1];
                $match2 = $match[2];

                $match[2] = $match1;
                $match[1] = $match2;

            }

			$match[2] = str_replace("$","",$match[2]);
			$match[1] = str_replace("$","",$match[1]); 
			$forOutput = '';
			$forArray = $variables[$match[2]];
			
			foreach ($forArray as $item) {
				$forOutput .= str_replace('$' . $match[1], $item, $match[3]);
			}
			$content = str_replace($match[0], $forOutput, $content);
		}

        return $content;

    }

	// Parse multi level variables. Returns parsed string.
    public function parseMultiLevelVariables($content, $variables) {

        foreach ($variables as $name => $value) {
			if(is_array($value)) {

				foreach($value as $name2 => $value2) {
					$content = str_replace('$' . $name . '[\''. $name2 .'\']', $value2, $content);
				}

				foreach($value as $name2 => $value2) {
					$content = str_replace('$' . $name . '["'. $name2 .'"]', $value2, $content);
				}

				foreach($value as $name2 => $value2) {
					$content = str_replace('$' . $name . '['. $name2 .']', $value2, $content);
				}
			}
		}

        return $content;

    }

	// Parse variables. Returns parsed string.
    public function parseVariables($content, $variables) {

        foreach ($variables as $name => $value) {
			if(is_string($value)) {
			$content = str_replace('$' . $name, $value, $content);
			}
		}

        return $content;

    }

}

?>