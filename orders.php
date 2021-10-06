<?php

$db = new mysqli("localhost","root","","test");

class orders{
	const api_url = "https://api.site.com";
	
	/**
	 * Cоздание нового заказа
	 * 
	 * @param int $event_id - id
	 * @param string $event_date - время в формате "YYYY-MM-DD hh:mm:ss"
	 * @param int $ticket_adult_price - цена взрослых билетов
	 * @param int $ticket_adult_quantity - кол-во взрослых билетов
	 * @param int $ticket_kid_price - цена детских билетов
	 * @param int $ticket_kid_quantity - кол-во детских билетов
	 * @return string|bool - 12значный баркод в случае успеха
	*/
	public static function create(int $event_id, string $event_date, int $ticket_adult_price, int $ticket_adult_quantity, int $ticket_kid_price, int $ticket_kid_quantity){
		global $db;

		$barcode = self::generateCode(16); // длинну бы уточнить

		$bookreq = self::httpPost(self::api_url."/book",[
			"event_id" => $event_id,
			"event_date" => $event_date,
			"ticket_adult_price" => $ticket_adult_price,
			"ticket_adult_quantity" => $ticket_adult_quantity,
			"ticket_kid_price" => $ticket_kid_price,
			"ticket_kid_quantity" => $ticket_kid_quantity,
			"barcode" => $barcode
		]);

		$bookres = json_decode($bookreq);

		if(isset($bookres->message)){

			$approvereq = self::httpPost(self::api_url."/approve",[
				"barcode" => $barcode
			]);

			$approveres = json_decode($approvereq);

			if(isset($approveres->message)){
				$equal_price = $ticket_adult_price + $ticket_kid_price;
				$userid = 0; // а какой? 
				$event_date_esc = $db->real_escape_string($event_date);
				
				$ins_event = $db->query("INSERT INTO `events`
					(
						`id`
						`event_id`
						`event_date`
						`ticket_adult_price`
						`ticket_adult_quantity`
						`ticket_kid_price`
						`ticket_kid_quantity`
						`barcode`
						`user_id`
						`equal_price`
						`created`
					)
					VALUES
					(
						null,
						'".$event_id."',
						'".$event_date_esc."',
						'".$ticket_adult_price."',
						'".$ticket_adult_quantity."',
						'".$ticket_kid_price."',
						'".$ticket_kid_quantity."',
						'".$barcode."',
						'".$userid."',
						'".(int)$equal_price."',
						NOW()
					)
				");

				if($ins_event){
					throw new Exception("Ошибка БД",5);
				}

				return $barcode;
			}
			else if(isset($approveres->error)){
				switch($approveres->error){
					case "event cancelled":
						throw new Exception("Мероприятие отменено",1);
						break;
					case "no tickets":
						throw new Exception("Билеты закончились",2);
						break;
					case "no seats":
						throw new Exception("Нет мест",3);
						break;
					case "fan removed":
						throw new Exception("fan removed",4);
						break;
				}
			}
		} else { // Значит что-то не так, повторяем запрос. Имеет смысл сделать задержку между запросами и ограничить количество повторов.
			return self::create($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity);
		}
		return false;
	}
	
	private static function generateCode($length){
		$out = "";

		for($i = 0; $i < $length; $i++){
			$out .= rand(0,9);
		}

		return $out;
	}

	private static function httpPost($url, $data){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

}

?>