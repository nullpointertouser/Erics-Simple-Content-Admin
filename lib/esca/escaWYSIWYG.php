<?
class AutoWYSIWYG {
	/*
		$locations['resources'] should contain:
		 - js/lib/nicEdit.js
		 - img/nicEditIcons.gif
	*/

	// All classglobal variables (Yup, I just made up a term :P)
	private $backend; // ESContentAdmin
	private $editMode; // boolean
	private $loadedBlips = array();
	private $locations = array();
	private $blipDefaults = array();

	// Instatiating
	private function __construct($backend) {
		$this->backend = $backend;
		$this->locations['resources'] = "res";
	}
	public static function getInstance($backend) {
		if (is_a($backend,"ESContentAdmin")) {
			return new AutoWYSIWYG($backend);
		} else {
			return false;
		}
	}

	// Public functions
		// Settings
		public function setEditMode($value) {
			$this->editMode = $value;
		}
		public function setBlipDefaults($params) {
			if (!is_array($params)) return false;
			foreach ($params as $key => $value) {
				$this->blipDefaults[$key] = $value;
			}
		}
		public function setLocations() {
			if (!is_array($params)) return false;
			foreach ($params as $key => $value) {
				setLocation($key, $value);
			}
		}
		public function setLocation($name, $newLoc) {
			$this->locations[$name] = $newLoc;
		}
	public function acceptPostSubmission() {
		if (!array_key_exists('esca_mod_blip', $_POST)) return false;
		$dataGood = true;
		foreach(array('esca_blip_name','esca_blip_text') as $field) {
			// If this field doesn't exist, set dataGood false and break
			if (!array_key_exists($field, $_POST)) { $dataGood = false; break; }
			// If field is not a string, or field is an empty string, set dataGood false and break
			if (!is_string($_POST[$field]) || !strlen($_POST[$field]) > 0) { $dataGood = false; break; }
		} if (!ctype_alnum($_POST['esca_blip_name'])) $dataGood = false;
		if (!$dataGood) throw new Exception('User sent us invalid data D:',1);

		// Valid, not formatted:
		$name = $_POST['esca_blip_name'];
		$text = $_POST['esca_blip_text'];

		if (!$this->backend->setBlip($name, $text)) {
			throw new Exception('Generic backend error',2);
		}
		return true;
	}
	public function loadBlips() {
		foreach (func_get_args() as $arg) {
			if (is_array($arg)) foreach ($arg as $argElem) {
				$this->loadBlip($argElem);
			} else {
				$this->loadBlip($arg);
			}
		}
	}
	public function outputHead($params = array()) {
		// --- CSS File
		//$src = $locations['resources'];
			//print('<link rel="stylesheet" type="text/css" href="'.$src.'">');
		// --- nicEdit javascript library
		if ($this->editMode) {
			// Config
				$buttonList = array('bold','italic','underline','left','center','right','justify','ol','ul',
					'fontFamily','fontSize','indent','outdent','hr','link','unlink','forecolor','xhtml');
				// If parameter "excludeButtons" was passed, exclude the specified buttons
				if (array_key_exists('excludeButtons', $params)) {
					foreach($params['excludeButtons'] as $bToExclude) {
						if(($key = array_search($bToExclude, $buttonList)) !== false) {
							unset($buttonList[$key]);
						}
					} unset($bToExclude);
				}
				// Put single quotes on button names
				foreach($buttonList as &$buttonName) {
					$buttonName = "'".$buttonName."'";
				} unset($buttonName);

			$src = $this->locations['resources']."/js/lib/nicEdit.js";
			print('<script type="text/javascript" src="'.$src.'"></script>');
		
			print('<script type="text/javascript">bkLib.onDomLoaded(function() {');
			print("$('.esca').each(function() {
				new nicEditor({iconsPath : '".$this->locations['resources']."/img/nicEditIcons.gif"."', buttonList : [".implode(',',$buttonList)."]}).panelInstance(this.id);
			});");
			print('});</script>');
		}
		
	}
	public function outputBlip($params) {
		// Check parameter validity
		if (!is_array($params)) return false;
		if (!array_key_exists('name', $params)) return false;
		$name = $params['name'];
		// Conveniently reusable settings snippet
		$setParams = array();
		foreach(array('style','class','action','data') as $param) {
			if (array_key_exists($param, $params)) { // set from function parameter
				$setParams[$param] = $params[$param];
			} else if (array_key_exists($param, $this->blipDefaults)) { // set from function parameter
				$setParams[$param] = $this->blipDefaults[$param];
			}
		}

		// Set HTML attributes to write later
		$class = ' class="esca'.(array_key_exists('class', $setParams) ? ' '.$setParams['class'] : '').'"';
		$style = (array_key_exists('style', $setParams) ? ' style="'.$setParams['style'].'"' : '');
		$id = ' id="esca_'.$params['name'].'"';

		if (!array_key_exists($name, $this->loadedBlips)) {
			$this->loadBlip($name);
		}

		$class = ' class="esca'.(array_key_exists('class', $setParams) ? ' '.$setParams['class'] : '').'"';
		$style = (array_key_exists('style', $setParams) ? ' style="'.$setParams['style'].'"' : '');
		$id = ' id="esca_'.$name.'"';
		$action= (array_key_exists('action', $setParams)) ? $setParams['action'] : $_SERVER['REQUEST_URI'];
		if ($this->editMode == 0) {
			print('<div'.$class.$id.$style.'>');
			print($this->loadedBlips[$name]['content']);
			print('</div>');
		} else if ($this->editMode == 1) {
			print('<form action="'.$action.'" method="post">');
			print('<textarea'.$class.$id.$style.' name="esca_blip_text">');
			print($this->loadedBlips[$name]['content']);
			print('</textarea><br />');
			print('<input type="hidden" name="esca_blip_name" value="'.$name.'" />');
			// Programmer-specified input boxes
				if (array_key_exists('data', $setParams)) {
					if (is_array($setParams['data'])) {
						foreach ($setParams['data'] as $key => $dat)
							print('<input type="hidden" name="esca_data_'.$key.'" value="'.$dat.'" />');
					}
				}
			print('<input type="submit" name="esca_mod_blip" value="Update" />');
			print('</form>');

		}
		return true;
	}
	// Private Functions
	private function loadBlip($name) {
		$thisBlip = array();
		$thisBlip['content']=$this->backend->getBlipHTML($name);
		$this->loadedBlips[$name] = $thisBlip;
	}
}
