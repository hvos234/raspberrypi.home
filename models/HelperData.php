<?php

namespace app\models;

use Yii;
use yii\base\Model;

class HelperData extends Model {
	
	/**
	 * Explode the string by the :: and , delimiter
	 * 
	 * @param type $string (string to explode)
	 * @return type
	 */
	public static function dataExplode($string){
		/*Yii::info('dataExplode $string: ' . $string, 'HelperData');
		echo('dataExplode $string: ' . $string) . '<br/>' . PHP_EOL;
		var_dump($string);
		echo('<br/>') . PHP_EOL;*/
		
		$return = [];
		$keys = HelperData::dataExplodeKey($string);
		foreach($keys as $key => $value){
			/*Yii::info('dataExplode $value: ' . $value, 'HelperData');
			echo('dataExplode $value: ' . $value) . '<br/>' . PHP_EOL;
			var_dump($value);
			echo('<br/>') . PHP_EOL;*/
			
			if(is_array($keyvalue = HelperData::dataExplodeKeyValue($value))){
				/*Yii::info('dataExplode $keyvalue: ' . json_encode($keyvalue), 'HelperData');
				echo('dataExplode $keyvalue: ' . json_encode($keyvalue)) . '<br/>' . PHP_EOL;
				var_dump($keyvalue);
				echo('<br/>') . PHP_EOL;*/
				
				$return = array_merge($return, $keyvalue);
			}else {
				/*Yii::info('dataExplode $keyvalue: ' . $keyvalue, 'HelperData');
				echo('dataExplode $keyvalue: ' . $keyvalue) . '<br/>' . PHP_EOL;
				var_dump($keyvalue);
				echo('<br/>') . PHP_EOL;*/
				
				$return[] = $keyvalue;
			}
		}
		
		//Yii::info('dataExplode $return: ' . json_encode($return), 'RuleExtra');
		//echo('dataExplode $return: ' . json_encode($return)) . '<br/>' . PHP_EOL;
		
		return $return;
	}
	
	public static function dataExplodeKey($string){
		/*Yii::info('dataExplodeKey $string: ' . $string, 'HelperData');
		echo('dataExplodeKey $string: ' . $string) . '<br/>' . PHP_EOL;
		var_dump($string);
		echo('<br/>') . PHP_EOL;*/
		
		// explode returns a empty array if the string is false
		if($string === true){
			$string = '1';
		}
		if($string === false){
			$string = '0';
		}
		
		$return = [];
		$array = explode(',', $string);
		
		/*Yii::info('dataExplodeKey $array: ' . json_encode($array), 'RuleExtra');
		echo('dataExplodeKey $array: ' . json_encode($array)) . '<br/>' . PHP_EOL;
		var_dump($array);
		echo('<br/>') . PHP_EOL;*/
		
		foreach($array as $key => $value){
			$return[] = trim($value);
		}
		
		//Yii::info('dataExplodeKey $return: ' . json_encode($return), 'RuleExtra');
		//echo('dataExplodeKey $return: ' . json_encode($return)) . '<br/>' . PHP_EOL;
		
		return $return;
	}
	
	public static function dataExplodeKeyValue($string){
		$return = [];
		if(false !== strpos($string, '::')){
			$strpos = strpos($string, '::');
			$before = substr($string, 0, $strpos);
			$after = substr($string, $strpos+2, strlen($string));
			$return[trim($before)] = HelperData::dataExplodeKeyValue($after);
		}else {
			$return = trim($string);
		}
		
		return $return;
	}
	
	/**
	 * Implode the array by :: and , delimiter
	 * 
	 * @param type $array (array to implode)
	 * @return type
	 */
	public static function dataImplode($array){
		$return = HelperData::dataImplodeKey($array);		
		return $return;
	}
	
	public static function dataImplodeKey($array){
		$return = '';
		foreach($array as $key => $value){
			if(!is_int($key)){
				if(!is_array($value)){
					$return .= $key . '::'  . $value . ',';
				}else {
					$return .= $key . '::' . HelperData::dataImplodeKeyValue($value) . ',';
				}
			
			}else {
				if(!is_array($value)){
					$return .= $value . ',';
				}else {
					$return .= $key . '::' . HelperData::dataImplodeKeyValue($value) . ',';
				}
			}
		}
		
		return substr($return, 0, -1);
	}
	
	public static function dataImplodeKeyValue($array){
		$return = '';
		foreach($array as $key => $value){
			if(!is_array($value)){
				$return .= $key . '::' . $value;
			}else {
				$return .= $key . '::' . HelperData::dataImplodeKeyValue($value);
			}
		}
		
		return $return;
	}
	
	/**
	 * Trims all the data
	 * 
	 * @param type $data (string to trim)
	 * @return type
	 */
	public static function dataTrim($data){
		$data = trim($data);
		$array = HelperData::dataExplode($data);
		return HelperData::dataImplode($array);		
	}
	
	/**
	 * Replace all the , for a return
	 * 
	 * @param type $data (string to replace)
	 * @return type
	 */
	public static function dataExplodeReturn($data){
		return str_replace(',', "\r\n", $data);
	}
	
	/**
	 * Replace all the return with a ,
	 * 
	 * @param type $data (string to replace)
	 * @return type
	 */
	public static function dataImplodeReturn($data){
		return str_replace(array("\r\n", "\r", "\n"), ',', $data);
	}
	
	public static function dataRemoveDoubleReturnsAndTrim($data){
		// replace returns with ,
		$data = HelperData::dataImplodeReturn($data);
		// trim all the spaces between the , like ,   , , ,, to ,,
		$data = HelperData::dataTrim($data);
		// replace all the ,, for one ,
		do {
			$done = strpos($data, ',,');
			$data = str_replace(',,', ',', $data);
		} while ($done);
		
		return $data;
	}


	/**
	 * Trims all the data
	 * 
	 * @param type $data (string to trim)
	 * @return type
	 */
	/*public static function dataTrim($data){
		$data = trim($data);
		echo('dataTrim $data ');
		var_dump($data);
		echo '<br/>' . PHP_EOL;
		
		$array = HelperData::dataExplode($data);
		echo('dataTrim $array ');
		var_dump($array);
		echo '<br/>' . PHP_EOL;
		//$array = HelperData::dataTrimArray($array);
		//$array = HelperData::dataImplode($array);
		
		return $array;
	}*/
	
	/*public static function dataTrimArray($array){
		$return = [];
		if(is_array($array)){
			foreach($array as $key => $value){
				$return[trim($key)] = HelperData::dataTrimArray($value);
			}
			return $return;
		}
		return trim($array);
	}*/
	
	/**
	 * Replace all the , for a return
	 * 
	 * @param type $data (string to replace)
	 * @return type
	 */
	/*public static function dataExplodeReturn($data){
		return str_replace(',', "\r\n", $data);
	}*/
	
	/**
	 * Replace all the return with a ,
	 * 
	 * @param type $data (string to replace)
	 * @return type
	 */
	/*public static function dataImplodeReturn($data){
		$string = str_replace(array("\r\n", "\r", "\n"), ',', $data);
		// replace all the double returns, also the returns with with space in between
		// make a array and remove all the with spaces
		$array = explode(',', $string);
		$array = HelperData::dataTrimArray($array);
		$string = implode(',', $array);
		// replace all the ,
		do {
			$done = strpos($string, ',,');
			$string = str_replace(',,', ',', $string);
		} while ($done);
		
		return $string;
	}*/
	
	/**
	 * Explode the Vos string to a array
	 * 
	 * @param type $string (string to explode)
	 * @return type
	 */
	/*public static function dataExplode($string){
		
		$string = HelperData::dataJsonDecode($string);
		echo('dataExplode $string ');
		echo '<br/>' . PHP_EOL;
		return json_decode($string, true);
	}*/
	
	/**
	 * Implode the array to a Vos string
	 * 
	 * @param type $array (array to implode)
	 * @return type
	 */
	/*public static function dataImplode($array){
		$string = json_encode($array, true);
		return HelperData::dataJsonEncode($string);
	}*/
	
	/**
	 * Encode a json string to a Vos string
	 * 
	 * @param type $json (sjon string to encode to a Vos string)
	 * @return type
	 */
	/*public static function dataJsonEncode($json){
		// first remove the first and the last {} 
		$string = substr($json, 1, -1);
		// remove the first "
		if('"' == substr($string, 0, 1)){
			$string = substr($string, 1);
		}
		// remove the last "
		if('"' == substr($string, strlen($string)-1, 1)){
			$string = substr($string, 0, -1);
		}
		$string = str_replace('{"', '[', $string);
		$string = str_replace('"}', ']', $string);
		
		$string = str_replace('","', ',', $string);
		$string = str_replace('":"', ':', $string);
		
		$string = str_replace(',"', ',', $string);
		$string = str_replace('",', ',', $string);
		
		$string = str_replace(':"', ':', $string);
		$string = str_replace('":', ':', $string);
		
		// everything thats left
		$string = str_replace('{', '[', $string);
		$string = str_replace('}', ']', $string);
		
		return $string;
	}*/
	
	/**
	 * Decode a Vos string to a json string
	 * 
	 * @param type $string (decode a Vos string to a json string)
	 * @return type
	 */
	/*public static function dataJsonDecode($string){
		// json do not like \r or'\n
		$string = HelperData::dataImplodeReturn($string);
		echo('dataJsonDecode $string ');
		var_dump($string);
		echo '<br/>' . PHP_EOL;
		
		// json needs a array, if there is no , make a array from it
		if(false === strpos($string, ',')){
			$string = '0:' . $string;
		}
		echo('dataJsonDecode $string2 ');
		var_dump($string);
		echo '<br/>' . PHP_EOL;
		
		$json = str_replace('[', '{"', $string);
		$json = str_replace(']', '"}', $json);
		
		$json = str_replace(',', '","', $json);
		$json = str_replace(':', '":"', $json);
		
		// correct the last two
		$json = str_replace('":"{"', '":{"', $json);
		$json = str_replace('"}","', '"},"', $json);
		
		$json = '{"' . $json . '"}';
		
		// correct the "}"}
		do {
			$done = strpos($json, '"}"}');
			$json = str_replace('"}"}', '"}}', $json);
		} while ($done);
		
		do {
			$done = strpos($json, '}"}');
			$json = str_replace('}"}', '}}', $json);
		} while ($done);
		
		echo('dataJsonDecode $json ');
		var_dump($json);
		echo '<br/>' . PHP_EOL;
		
		return $json;
	}*/
}