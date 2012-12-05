<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sofort
 *
 * @author Abadon
 */
class sofort {
private $user_id = "";
private $project_id = "";
private $sender_holder = "";
private $sender_account_number = "";
private $sender_bank_code = "";
private $sender_country_id = "";
private $amount = "";
private $currency_id = "EUR";
private $reason_1 = "";
private $reason_2 = "";
private $user_variable_0 = "";
private $user_variable_1 = "";
private $user_variable_2 = "";
private $user_variable_3 = "";
private $user_variable_4 = "";
private $user_variable_5 = "";
private $project_password = "";
private $hash;
    function __construct(){}
    
    public function set_value($key,$value){
        $this->$key = $value;
    }
    
    private function get_hash(){
        $data = array(
            $this->user_id, // user_id
            $this->project_id, // project_id
            $this->sender_holder, // sender_holder
            $this->sender_account_number, // sender_account_number
            $this->sender_bank_code, // sender_bank_code
            $this->sender_country_id, // sender_country_id
            $this->amount, // amount
            $this->currency_id, // currency_id, Pflichtparameter bei Hash-Berechnung
            $this->reason_1,// reason_1
            $this->reason_2, // reason_2
            $this->user_variable_0, // user_variable_0
            $this->user_variable_1, // user_variable_1
            $this->user_variable_2, // user_variable_2
            $this->user_variable_3, // user_variable_3
            $this->user_variable_4, // user_variable_4
            $this->user_variable_5, // user_variable_5
            $this->project_password // project_password
        );
        $data_implode = implode('|', $data);
        return sha1($data_implode);
    }
    
    public function go_to(){
        $this->user_variable_5 = sha1($this->user_variable_0.$this->user_variable_1.$this->user_variable_2);
        $this->db_entry();
        $this->hash = $this->get_hash();
        echo '
<form method="post" action="https://www.sofortueberweisung.de/payment/start" name="ftw">
	<input name="user_id"               type="hidden" value="'.$this->user_id.'"/>
	<input name="project_id"            type="hidden" value="'.$this->project_id.'"/>
	<input name="amosender_holderunt"   type="hidden" value="'.$this->sender_holder.'"/>
	<input name="sender_account_number" type="hidden" value="'.$this->sender_account_number.'"/>
	<input name="sender_bank_code"      type="hidden" value="'.$this->sender_bank_code.'"/>
	<input name="sender_country_id"     type="hidden" value="'.$this->sender_country_id.'"/>
	<input name="amount"                type="hidden" value="'.$this->amount.'"/>
	<input name="currency_id"           type="hidden" value="'.$this->currency_id.'"/>
	<input name="reason_1"              type="hidden" value="'.$this->reason_1.'"/>
	<input name="reason_2"              type="hidden" value="'.$this->reason_2.'"/>
	<input name="user_variable_0"       type="hidden" value="'.$this->user_variable_0.'"/>
	<input name="user_variable_1"       type="hidden" value="'.$this->user_variable_1.'"/>
	<input name="user_variable_2"       type="hidden" value="'.$this->user_variable_2.'"/>
	<input name="user_variable_3"       type="hidden" value="'.$this->user_variable_3.'"/>
	<input name="user_variable_4"       type="hidden" value="'.$this->user_variable_4.'"/>
	<input name="user_variable_5"       type="hidden" value="'.$this->user_variable_5.'"/>
	<input name="project_password"      type="hidden" value="'.$this->project_password.'"/>
	<input name="hash" type="hidden" value="'.$this->get_hash().'"/>
</form>
<script> document.ftw.submit(); </script>';
    }
    private function db_entry(){}
    public function check(){
        global $DB,$TIME;
        $item = $DB->query_fetch("SELECT * FROM transactions WHERE hash = '".$_POST["user_variable_5"]."' LIMIT 1;");
		$user = $DB->select_pair ("permissions","permission","value",FALSE,FALSE, "name = '".$item["user_id"]."'" );
		$usr_id = $DB->query_fetch_single("SELECT id FROM permissions_entity WHERE name = '".$item["user_id"]."' limit 1");
		$packet = $DB->query_fetch("SELECT * FROM `member_boxes` WHERE id = '".$item["packet"]."' limit 1");
		$mwst = $item["ammount"] / 119 * 19;
		$brutto = $item["ammount"] - $mwst;
		if ($item["type"] == 1) $art = "PayPal"; elseif ($item["type"] == 2) $art = "Sofortüberweisung";
		switch($item["new"]){
		case 0:
			$msg = '
				<h1>Ihre Bestellung bei Hotel-Circle.</h1>
				<div style="width:100%, text-align:center;">
					<font style="font-size:14px; font-weight:bold; color:black;width:100%, text-align:center;">Rechnungsadresse</font><br/>
					<font style="font-size:16px; color:black;">Firma<br/>
					<b>'.$user["company"].'</b><br/>
					'.$user["street"].'<br/>
					<b>'.$user["zip"].' '.$user["city"].'</b><br/>
					<br/><br/><br/><br/>
					Ihre Bestellung vom '.$TIME->convertDate($item["date"]).'<br/>
					KD-Nr. '.str_pad($usr_id, 10 ,'0', STR_PAD_LEFT).'
					</font>
				</div><br/>
				<font style="font-size:16px; color:black;width:100%, text-align:center;">
					Bestellungsdetails<br/><br/>
				</font><br/>
<table width="100%" cellspacing="0">
  <tr>
    <th bgcolor="#CCCCCC" style="padding:5px 0px 5px 0px;">Paket</th>
    <th bgcolor="#CCCCCC" style="padding:5px 0px 5px 0px;">Laufzeit</th>
    <th bgcolor="#CCCCCC" style="padding:5px 0px 5px 0px;">Preis</th>
  </tr>
  <tr>
    <td bgcolor="#f5f5f5" style="padding:5px 10px 5px 10px;" ><b>'.$packet["name"].'</b></td>
    <td bgcolor="#f5f5f5" style="padding:5px 0px 5px 10px;" >'.$TIME->convertDate(date("Y-m-d")).' - '.$TIME->convertDate(date("Y-m-d", strtotime("$datum+1 year"))).'</td>
    <td bgcolor="#f5f5f5" style="text-align:right;">'.number_format ( $packet["price"] , 2 , ',','').' Euro</td>
  </tr>
  <tr>
    <td rowspan="4"></td>
    <td bgcolor="#f5f5f5" style="padding:5px 0px 5px 10px;" >Abzüglich Guthaben aus früherer Bestellung</td>
    <td bgcolor="#f5f5f5" style="text-align:right;">'.number_format ( $item["bonus"] , 2 , ',','').' Euro</td>
  </tr>
  <tr>
    <td bgcolor="#f5f5f5" style="padding:5px 0px 5px 10px;" >Paketpreis für Upgrade</td>
    <td bgcolor="#f5f5f5" style="text-align:right;">'.number_format ( $item["ammount"] , 2 , ',','').' Euro</td>
  </tr>
  <tr>
    <td bgcolor="#f5f5f5" style="padding:5px 0px 5px 10px;" >Im Preis enthaltene MwSt. 19%</td>
    <td bgcolor="#f5f5f5" style="text-align:right;">'.number_format ( $mwst , 2 , ',','').' Euro</td>
  </tr>
  <tr>
    <td bgcolor="#dbdbdb" style="padding:5px 0px 5px 10px;" ><b>Rechnungsbetrag</b></td>
    <td bgcolor="#dbdbdb" style="text-align:right;"><b>'.number_format ($item["ammount"] , 2 , ',','').' Euro</b></td>
  </tr>
</table><br/>
					<div style="width:100%, text-align:center;">
					<font style="font-size:16px; color:black;width:100%, text-align:center;">
						Der Rechnungsbertag wurde am '.$TIME->convertDate(date("Y-m-d")).' per '.$art.' bezahlt.<br/><br/>
						Vielen Dank für Ihre Bestellung.<br/><br/>
						Hotel-Circle
					</font>
				</div>
				USt-IdNr.: DE128804501<br/>
				St-Nr.: 123/253/40272<br/><br/>

				<b>Bankverbindung:</b><br/>
				Sparkasse Allgäu<br/>
				Kto-Nr. 514 669 159<br/>
				BLZ 733 500 00<br/>
				IBAN: DE50733500000514669159<br/>
				BIC: BYLADEM1ALG<br/>
				';
			break;
			case 1:
			$msg = '
				<h1>Ihre Bestellung bei Hotel-Circle.</h1>
				<div style="width:100%, text-align:center;">
					<font style="font-size:14px; font-weight:bold; color:black;width:100%, text-align:center;">Rechnungsadresse</font><br/>
					<font style="font-size:16px; color:black;width:100%, text-align:center;">Firma<br/>
					<b>'.$user["company"].'</b><br/>
					'.$user["street"].'<br/>
					<b>'.$user["zip"].' '.$user["city"].'</b><br/>
					<br/><br/><br/><br/>
					Ihre Bestellung vom '.$TIME->convertDate($item["date"]).'<br/>
					KD-Nr. '.str_pad($usr_id, 10 ,'0', STR_PAD_LEFT).'
					</font>
				</div><br/>
				<font style="font-size:16px; color:black;width:100%, text-align:center;">
					Bestellungsdetails<br/><br/>
				</font>
<table width="100%" cellspacing="0">
  <tr>
    <th bgcolor="#CCCCCC" style="padding:5px 0px 5px 0px;">Paket</th>
    <th bgcolor="#CCCCCC" style="padding:5px 0px 5px 0px;">Laufzeit</th>
    <th bgcolor="#CCCCCC" style="padding:5px 0px 5px 0px;">Preis</th>
  </tr>
  <tr>
    <td bgcolor="#f5f5f5" style="padding:5px 10px 5px 10px;" ><b>'.$packet["name"].'</b></td>
    <td bgcolor="#f5f5f5" style="padding:5px 0px 5px 10px;" >'.$TIME->convertDate(date("Y-m-d")).' - '.$TIME->convertDate(date("Y-m-d", strtotime("$datum+1 year"))).'</td>
    <td bgcolor="#f5f5f5" style="text-align:right;">'.number_format ( $packet["price"] , 2 , ',','').' Euro</td>
  </tr>
  <tr>
    <td></td>
    <td bgcolor="#f5f5f5" style="padding:5px 0px 5px 10px;" >Im Preis enthaltene MwSt. 19%</td>
    <td bgcolor="#f5f5f5" style="text-align:right;">'.number_format ( $mwst , 2 , ',','').' Euro</td>
  </tr>
  <tr>
    <td></td>
    <td bgcolor="#dbdbdb" style="padding:5px 0px 5px 10px;" ><b>Rechnungsbetrag</b></td>
    <td bgcolor="#dbdbdb" style="text-align:right;"><b>'.number_format ($item["ammount"] , 2 , ',','').' Euro</b></td>
  </tr>
</table><br/>
					<div style="width:100%, text-align:center;">
					<font style="font-size:16px; color:black;width:100%, text-align:center;">
						Der Rechnungsbertag wurde am '.$TIME->convertDate(date("Y-m-d")).' per '.$art.' bezahlt.<br/><br/>
						Vielen Dank für Ihre Bestellung.<br/><br/>
						Hotel-Circle
					</font>
				</div>
				USt-IdNr.: DE128804501<br/>
				St-Nr.: 123/253/40272<br/><br/>

				<b>Bankverbindung:</b><br/>
				Sparkasse Allgäu<br/>
				Kto-Nr. 514 669 159<br/>
				BLZ 733 500 00<br/>
				IBAN: DE50733500000514669159<br/>
				BIC: BYLADEM1ALG<br/>
				';
			break;
		}
		
        if ($item){
			send_mail("info@hotel-circle.de",$user["email"], $msg, "Ihre Bestellung bei Hotel-Circle");
			send_mail("info@hotel-circle.de","info@hotel-circle.de", $msg, "Zahlungseingang bei Hotel-Circle");
            $DB->query("UPDATE permissions SET value = '".$item["packet"]."' WHERE name = '".$item["user_id"]."' AND permission = 'packet' LIMIT 1");
            $datum=date("Y-m-d"); 
            $datum=date("Y-m-d", strtotime("$datum+1 year"));     
            $DB->query("UPDATE permissions SET value = '".$datum."' WHERE name = '".$item["user_id"]."' AND permission = 'packet_till' LIMIT 1");
            $DB->query("DELETE FROM `transactions` WHERE hash = '".$_POST["user_variable_5"]."' LIMIT 1;");
        }
    }
}

?>
