<? // Eric's Simple PHP Content Admin

final class ESCA {
	// Final class with empty private constructor = enum :D
	private function __construct() {}

	// Constants for blipExists()
	const No = 0;
	const Yes = 1;
	const Error = 3;

}

class ESContentAdmin {
	public static function getVersion() {
		return 0.1;
	}
	/*
	Wanted to extend mysqli, but wanted to avoid confictions and PHP-specific
	difficulties, so a public mysqli object seemed the best option.
	*/
	public $mysqli;
	private $hasConnection;
	public function __construct() {
		$this->hasConnection = true; // Let's be optimistic
		switch (func_num_args()) {
			case 4:
				$params = func_get_args();
				$this->mysqli = new mysqli($params[0], $params[1], $params[2], $params[3]);
				if ($this->mysqli->connect_errno) {
					$this->hasConnection = false;
				}
				break;
			case 1:
				$params = func_get_args();
				if (is_a($params[0],"mysqli")) {
					$this->mysqli = $params[0];
					if ($this->mysqli->connect_errno) {
						$this->hasConnection = false;
					}
				} else {
					$this->hasConnection = false;
				}
				break;
			default:

				break;
		}
		return $this->hasConnection;
	}

	public function hasConnection() { return $this->hasConnection; }

	// WRITE
	public function createTable() {
		if (!$this->hasConnection) return false;
		$tblSql="CREATE TABLE IF NOT EXISTS esca_blips
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        name VARCHAR(32),
        content TEXT,
        edit_mode TINYINT NOT NULL,
        last_change datetime NOT NULL default '0000-00-00 00:00:00',
        PRIMARY KEY (id)
        );";
		if (!$this->mysqli->query($tblSql)) {
			return false;
		}
		return true;
	}

	public function setBlip($name,$content) {
		$blip = $this->blipExists($name);
		if ($blip) {
			if ($blip == -1) return false;
			return $this->editBlip($blip,$content);
		} else {
			return $this->newBlip($name,$content);
		}
	}
	public function setBlipMode($name,$mode) {
		$blip = $this->blipExists($name);
		if ($blip) {
			if ($blip == -1) return false;
			$this->editBlip($blip,$content);
		} else {
			return $this->newBlip($name,$content);
		}
	}

	// Read
	public function getBlipHTML($name) {
		$sql = "SELECT content FROM esca_blips WHERE name=?";

		// Prepare
	 	if (!( $stmt = $this->mysqli->prepare($sql) )) {
	 		return false;
	 	}

	 	// Bind
	 	if (!( $stmt->bind_param("s", $name) )) {
	 		return false;
	 	}

	 	// Execute
	 	if (!( $stmt->execute() )) {
	 		return false;
	 	}
	 	$stmt->store_result();

	 	// Check if user exists
	 	if ( $stmt->num_rows ) {
		 	$content = "";
		 	$stmt->bind_result($content);
		 	if (!$stmt->fetch()) {
		 		return false;
		 	}
		 	$stmt->close();
		 	return $content;

	 	} else {
	 		$this->newBlip($name,"");
	 		return "";
	 	}
	}

	// PRIVATE FUNCTIONS ===================================================================================================================
	private function newBlip($name,$content) {
		$sql = "INSERT INTO esca_blips (name,content,edit_mode,last_change) VALUES (?,?,1,now())";

		// Prepare
	 	if (!( $stmt = $this->mysqli->prepare($sql) )) { 
	 		$this->mysqli->close();
	 		return false;
	 	}

	 	// Bind
	 	if (!( $stmt->bind_param("ss", $name, $content) )) {
	 		$this->mysqli->close();
	 		return false;
	 	}

	 	// Execute
	 	if (!( $stmt->execute() )) {
	 		$this->mysqli->close();
	 		return false;
	 	}
	 	return true;
	}
	private function editBlip($id,$content) {
		$sql = "UPDATE esca_blips SET content=?, last_change=now() WHERE id=?";

		// Prepare
	 	if (!( $stmt = $this->mysqli->prepare($sql) )) { 
	 		return false;
	 	}

	 	// Bind
	 	if (!( $stmt->bind_param("si", $content, $id) )) {
	 		return false;
	 	}

	 	// Execute
	 	if (!( $stmt->execute() )) {
	 		return false;
	 	}
	 	return true;
	}
	private function blipExists($name) {
		$sql = "SELECT id FROM esca_blips WHERE name=?";

		// Prepare
	 	if (!( $stmt = $this->mysqli->prepare($sql) )) {
	 		return -1;
	 	}

	 	// Bind
	 	if (!( $stmt->bind_param("s", $name) )) {
	 		return -1;
	 	}

	 	// Execute
	 	if (!( $stmt->execute() )) {
	 		return -1;
	 	}
	 	$stmt->store_result();

	 	// Check if user exists
	 	if ( $stmt->num_rows ) {
		 	
		 	$id = -1;
		 	$stmt->bind_result($id);
		 	if (!$stmt->fetch()) {
		 		return -1;
		 	}
		 	$stmt->close();
		 	return $id;

	 	} else {
	 		return false;
	 	}

	}
}
?>