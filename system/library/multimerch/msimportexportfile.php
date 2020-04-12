<?php
class MsImportExportFile extends Model {

    public function __construct($registry) {
		parent::__construct($registry);
	}

	public function getFieldCaption($file_name, $data = array()) {
		$result = array();
        if (isset($data['cell_separator'])){
            $cell_separator =  $data['cell_separator'];
        }else{
            $cell_separator =  '"';
        }

        if (isset($data['cell_container'])){
            $cell_container =   $data['cell_container'];
        }else{
            $cell_container =  ';';
        }

		if (($handle = fopen($file_name, 'r')) !== FALSE) {
			if (($field_caption = fgetcsv($handle, 1000, $cell_container, $cell_separator)) !== FALSE) {
				for($i = 0; $i < count($field_caption); $i++) {
					$field_caption[$i] = trim($field_caption[$i], " \t\n");
					if ($data['file_encoding'] == 1){
						$field_caption[$i] = iconv('windows-1251', 'UTF-8//IGNORE', $field_caption[$i]);
					}
				}
				$result = array_flip($field_caption);
			}
			fclose($handle);
		}
		return $result;
	}

	public function getSamplesData($file_name, $import_data, $only_active_columns = true) {
        $start_row = $import_data['start_row'];
		if ($import_data['finish_row']){
		    $finish_row =  $import_data['finish_row'];
        }else{
            $finish_row = $start_row+5;
        }
		if (isset($import_data['mapping'])){
			$active_columns = array_keys($import_data['mapping']);
		}else{
			$active_columns = array();
		}
		$row_num = 1;
        $result = array();
        if (($handle = fopen($file_name, 'r')) !== FALSE) {
            while(($data = fgetcsv($handle, 10*1024, $import_data['cell_container'],$import_data['cell_separator'])) !== FALSE) {
                if ($row_num < $start_row){
                    $row_num++;
                    continue;
                }
                if ($finish_row AND $row_num > $finish_row){
                    break;
                }
                foreach ($data as $col_num=>$col_value){
					if ($only_active_columns AND $active_columns AND !in_array($col_num,$active_columns)){
						continue;
					}
					if ($import_data['file_encoding'] == 1){
						$col_value = iconv('windows-1251', 'UTF-8//IGNORE', $col_value);
					}
					$result[$row_num][] = trim($col_value);
				}
                $row_num++;
			}
			fclose($handle);
		}
		return $result;
	}
}