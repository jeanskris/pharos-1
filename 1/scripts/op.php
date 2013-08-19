<?php
require_once('./all_data_0819.php');
require_once('./medoo.min.php');
header('Content-Type:text/html;charset=utf8');

$db = new medoo(
	array(
		'database_type' => 'mysql',
		'database_name' => 'pharos',
		'server' => 'localhost',
		'username' => 'root',
		'password' => '123456',
		'charset' => 'utf8',
		'option' => array(
			PDO::ATTR_CASE => PDO::CASE_NATURAL
			)	
		)
	);

$code_file="com.txt";
$code_array=file($code_file);


$op = $_SERVER['argv'][1];
$arg[0] = $_SERVER['argv'][2];
$arg[1] = $_SERVER['argv'][3];
switch ($op) {
	case 'download':
		foreach ($code_array as $key => $value) {
			if($key<$arg[0]) continue;
			download_by_code(trim($value));
			echo trim($value).": ok\n";
			if($key>=$arg[1]) break;
		}
		break;
	case 'exceldata':
		foreach ($code_array as $key => $value) {
			if($key<$arg[0]) continue;
			print_r(get_report_data_by_code(trim($value)));
			echo trim($value).": ok\n";
			if($key>=$arg[1]) break;
		}
		break;
	case 'yahoodata':
		foreach ($code_array as $key => $value) {
			if($key<$arg[0]) continue;
			print_r(get_trade_data_by_code(trim($value)));
			//get_trade_data_by_code(trim($value));
			echo trim($value).": ok\n";
			if($key>=$arg[1]) break;
		}
		break;
	case 'incinfo':
		foreach ($code_array as $key => $value) {
			if($key<$arg[0]) continue;
			print_r(get_inf_by_code(trim($value)));
			echo trim($value).": ok\n";
			if($key>=$arg[1]) break;
		}
		break;
	case 'cap':
		foreach ($code_array as $key => $value) {
			if($key<$arg[0]) continue;
			print_r(get_capitalization_by_code(trim($value)));
			echo trim($value).": ok\n";
			if($key>=$arg[1]) break;
		}
		break;
	case 'tbl_insert':
		foreach ($code_array as $key => $value) {
			$value=trim($value);
			if($key<$arg[0]) continue;
			$data=array();
			$data = get_report_data_by_code($value);
			foreach ($data['bdate'] as $data_key => $data_value) {

				//echo 'benifit table id:'.
				$db->insert('ph_inc_benifit',array(
					'inc_code'=>$value,
					'bdate'=>$data_value,
					'income'=>(double)$data['income'][$data_key],
					'cost'=>(double)$data['cost'][$data_key]
					));
				// echo ' ['.$value.' '.$data_value.' '.$data['income'][$data_key].' '.$data['cost'][$data_key].']';
				// echo "\n";
			}
			echo '-->'.'benifit table ok'.'<--'."\n";
			foreach ($data['ddate'] as $data_key => $data_value) {
				//echo 'debt table id:'.
				$db->insert('ph_inc_debt',array(
					'inc_code'=>$value,
					'ddate'=>$data_value,
					'dtax'=>(double)$data['dtax'][$data_key],
					'funds'=>(double)$data['funds'][$data_key],
					'debt'=>(double)$data['debt'][$data_key]
					));
				//echo ' ['.$value.' '.$data_value.' '.$data['dtax'][$data_key].' '.$data['funds'][$data_key].' '.$data['debt'][$data_key].']';
				//echo "\n";
			}
			echo '-->'.'debt table ok'.'<--'."\n";

			echo '===========>'.$value.": ok\n";
			if($key>=$arg[1]) break;
		}
		break;

	case 'trade_insert':
		foreach ($code_array as $key => $value) {
			$value=trim($value);
			if($key<$arg[0]) continue;
			$data=array();
			$data=get_trade_data_by_code($value);
			foreach ($data['tradedate'] as $data_key => $data_value) {
				//echo 'trade table id:'.
				$db->insert('ph_inc_trade',array(
						'inc_code'=>$value,
						'tdate'=>$data_value,
						'open'=>(double)$data['open'][$data_key],
						'close'=>(double)$data['close'][$data_key],
						'high'=>(double)$data['high'][$data_key],
						'low'=>(double)$data['low'][$data_key],
						'adjclose'=>(double)$data['adjclose'][$data_key],
						'change'=>(double)$data['change'][$data_key]
					));
				// echo ' ['.$value.' '.$data_value.' '.$data['tradedate'][$data_key].' '.$data['open'][$data_key].' '.$data['close'][$data_key].' '.$data['high'][$data_key].' '.$data['low'][$data_key].' '.$data['adjclose'][$data_key].']';
				// echo "\n";
			}
			
			
			echo $key.'===========>'.$value.": ok\n";
			if($key>=$arg[1]) break;
		}
		break;
	case 'cap_insert':
		foreach ($code_array as $key => $value) {
			$value=trim($value);
			if($key<$arg[0]) continue;
			$data=array();
			$data=get_capitalization_by_code($value);
			foreach ($data['date'] as $data_key => $data_value) {
				if($data['cap'][$data_key]=='-'){$data['cap'][$data_key]='';}
				echo 'trade table id:'.$db->insert('ph_inc_cap',array(
						'inc_code'=>$value,
						'date1'=>$data_value,
						'cap'=>(double)$data['cap'][$data_key]
					));
				echo ' ['.$value.' '.$data_value.' '.$data['date'][$data_key].' '.$data['cap'][$data_key].']'."\n";
			}
			echo $key.'===========>'.$value.": ok\n";
			if($key>=$arg[1]) break;
		}
		break;
	case 'incinfo_insert':
		foreach ($code_array as $key => $value) {
			$value=trim($value);
			if($key<$arg[0]) continue;
			$data=array();
			$data=get_inf_by_code($value);
			//inc info insert
			echo $key.'===========>'.$value.": ok\n";
			if($key>=$arg[1]) break;
		}
		break;
	
	default:
		echo 'input error';
		break;
}


