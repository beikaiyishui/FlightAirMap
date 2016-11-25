<?php
require_once(dirname(__FILE__).'/settings.php');
require_once(dirname(__FILE__).'/class.Connection.php');

class ATC {
	public $db;
	public function __construct($dbc = null) {
		$Connection = new Connection($dbc);
		$this->db = $Connection->db;
	}

       public function getAll() {
                $query = "SELECT * FROM atc";
                $query_values = array();
                 try {
                        $sth = $this->db->prepare($query);
                        $sth->execute($query_values);
                } catch(PDOException $e) {
                        return "error : ".$e->getMessage();
                }
                $all = $sth->fetchAll(PDO::FETCH_ASSOC);
                return $all;
        }

       public function add($ident,$frequency,$latitude,$longitude,$range,$info,$date,$type = '',$ivao_id = '',$ivao_name = '',$format_source = '',$source_name = '') {
    		$info = preg_replace('/[^(\x20-\x7F)]*/','',$info);
    		$info = str_replace('^','<br />',$info);
    		$info = str_replace('&amp;sect;','',$info);
    		$info = str_replace('"','',$info);
    		if ($type == '') $type = NULL;
                $query = "INSERT INTO atc (ident,frequency,latitude,longitude,atc_range,info,atc_lastseen,type,ivao_id,ivao_name,format_source,source_name) VALUES (:ident,:frequency,:latitude,:longitude,:range,:info,:date,:type,:ivao_id,:ivao_name,:format_source,:source_name)";
                $query_values = array(':ident' => $ident,':frequency' => $frequency,':latitude' => $latitude,':longitude' => $longitude,':range' => $range,':info' => $info,':date' => $date,':ivao_id' => $ivao_id,':ivao_name' => $ivao_name, ':type' => $type,':format_source' => $format_source,':source_name' => $source_name);
                 try {
                        $sth = $this->db->prepare($query);
                        $sth->execute($query_values);
                } catch(PDOException $e) {
                        return "error : ".$e->getMessage();
                }
        }

       public function deleteById($id) {
                $query = "DELETE FROM atc WHERE atc_id = :id";
                $query_values = array(':id' => $id);
                 try {
                        $sth = $this->db->prepare($query);
                        $sth->execute($query_values);
                } catch(PDOException $e) {
                        return "error : ".$e->getMessage();
                }
        }

       public function deleteAll() {
                $query = "DELETE FROM atc";
                $query_values = array();
                 try {
                        $sth = $this->db->prepare($query);
                        $sth->execute($query_values);
                } catch(PDOException $e) {
                        return "error : ".$e->getMessage();
                }
        }

	public function deleteOldATC() {
                global $globalDBdriver;
                if ($globalDBdriver == 'mysql') {
                        $query  = "DELETE FROM atc WHERE DATE_SUB(UTC_TIMESTAMP(),INTERVAL 1 HOUR) >= atc.atc_lastseen";
                } else {
                        $query  = "DELETE FROM atc WHERE NOW() AT TIME ZONE 'UTC' - '1 HOUR'->INTERVAL >= atc.atc_lastseen";
                }
                try {
                        $sth = $this->db->prepare($query);
                        $sth->execute();
                } catch(PDOException $e) {
                        return "error";
                }
                return "success";
        }
}
?>