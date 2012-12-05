<?php

/**
 * Validator Klasse zum überprüfen von Formular Benutzereingaben
 *
 * @author     Andreas Skodzek <webmaster@phpbuddy.eu>
 * @link       http://www.phpbuddy.eu
 * @copyright  2008 Andreas Skodzek
 * @license    GNU Public License <http://www.gnu.org/licenses/gpl.html>
 * @version    v1.0 released 03.03.2008
 */

class Validator
{
   /**
    * @var string Suchmuster für Zahlen
	*/
	protected $muster_zahl = '/^\d+$/';

   /**
    * @var string Suchmuster für Postleitzahlen
	*/
	protected $muster_plz = '/^[0-9]{5}$/';

   /**
    * @var string Suchmuster für ein Datum (deutsches Format)
	*/
	protected $muster_datum = '/^[0-9]{1,2}(\.|\/|\-){1}[0-9]{1,2}(\.|\/|\-){1}[0-9]{4}$/';

   /**
    * @var string Suchmuster für Telefonnummern
	*/
	protected $muster_telefon = '/^[0-9\+\(\)\/\-\s]+$/';

   /**
    * @var string Suchmuster für Geldbetrag
	*/
	protected $muster_geldbetrag = '/^([1-9]{1}[0-9]{0,2}((\.)?[0-9]{3})*((\.|\,)?[0-9]{0,2})?|[1-9]{1}[0-9]{0,}((\.|\,)?[0-9]{0,2})?|0((\.|\,)?[0-9]{0,2})?|((\.|\,)?[0-9]{1,2})?)$/';

   /**
    * @var string Suchmuster für einzelne Wörter
	*/
	protected $muster_wort = '/^[a-zA-ZäÄöÖüÜß\xc0-\xc2\xc8-\xcf\xd2-\xd4\xd9-\xdb\xe0-\xe2\xe8-\xef\xf2-\xf4\xf9-\xfb\x9f\xff\']+$/';

   /**
    * @var string Suchmuster für Namen
	*/
	protected $muster_name = '/^([a-zA-ZäÄöÖüÜß\xc0-\xc2\xc8-\xcf\xd2-\xd4\xd9-\xdb\xe0-\xe2\xe8-\xef\xf2-\xf4\xf9-\xfb\x9f\xff\.\'\-_]?(\s)?)+$/';

   /**
    * @var string Suchmuster für Emailadressen (wird im Konstruktor erzeugt)
	*/
	protected $muster_email = '';

   /**
    * @var string Suchmuster für Internetadressen
	*/
	protected $muster_url = '/^(http|https)\:\/\/([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&%\$\-]+)*@)?((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.[a-zA-Z]{2,6})(\:[0-9]+)?(\/[a-zA-Z0-9\.\,\?\'\\/\+&%\$#\=~_\-@]*)*$/';

   /**
    * @var array Verbotene Ausdrücke die auf SQL-Injection hindeuten
    */
	protected $sql_injection = array( 'delete from',
									  'drop table',
									  'insert into',
									  'select from',
									  'select into',
									  'update set' );

   /**
    * @var array Verbotene Ausdrücke die auf Email-Injection hindeuten
    */
	protected $email_injection = array( 'bcc:',
										'boundary',
										'cc:',
										'content-transfer-encoding:',
										'content-type:',
										'mime-version:',
										'subject:' );

   /**
    * @var array Suchmuster für zu bereinigende Variablen
	*/
	protected $bereinigen_suchmuster = array( "/(\r\n)|(\r)/m",
											  "/(\n){3,}/m",
											  "/\s{3,}/m",
											  "/(.)\\1{15,}/im" );

   /**
    * @var array Ersatz für zu bereinigende Variablen
	*/
	protected $bereinigen_ersatz = array( "\n",
										  "\n\n",
										  " ",
										  "\\1" );

   /**
    * @var array Nimmt die fehlerhaften Felder auf
	*/
	protected $feld_error = array();

   /**
    * Fehlermeldung für falsches Format
	*/
	const FELD_ERROR_FORMAT = 'Die Eingabe hat das falsche Format.';

   /**
    * Fehlermeldung für zu kurzen Feld-Inhalt
	*/
	const FELD_ERROR_ZUKURZ = 'Die Eingabe ist zu kurz.';

   /**
    * Fehlermeldung für zu kurzen Feld-Inhalt
	*/
	const FELD_ERROR_ZULANG = 'Die Eingabe ist zu lang.';

   /**
    * Fehlermeldung für ungültiges Datum
	*/
	const FELD_ERROR_DATUM = 'Das eingegebene Datum ist ung&uuml;ltig.';

   /**
    * Fehlermeldung für mögliche Injection
	*/
	const FELD_ERROR_INJECTION = 'Die Eingabe enth&auml;lt m&ouml;glicherweise einen sch&auml;dlichen Ausdruck.';


	/**
	 * Konstruktor
	 */
	public function __construct()
	{
		// Suchmuster für Email-Validierung zusammenstellen
		$nonascii      = "\x80-\xff";	
		$nqtext        = "[^\\\\$nonascii\015\012\"]";
		$qchar         = "\\\\[^$nonascii]";
		$normuser      = '[a-zA-Z0-9][a-zA-Z0-9_.-]*';
		$quotedstring  = "\"(?:$nqtext|$qchar)+\"";
		$user_part     = "(?:$normuser|$quotedstring)";
		$dom_mainpart  = '[a-zA-Z0-9][a-zA-Z0-9._-]*\\.';
		$dom_subpart   = '(?:[a-zA-Z0-9][a-zA-Z0-9._-]*\\.)*';
		$dom_tldpart   = '[a-zA-Z]{2,5}';
		$domain_part   = "$dom_subpart$dom_mainpart$dom_tldpart";
		$pattern       = "$user_part\@$domain_part";
		$this->muster_email = "/^{$pattern}$/";
	}


	/**
	 * Methode zum bereinigen von Variablen
	 *
	 * @param  mixed  $eingabe Benutzereingabe
	 * @return mixed  Gibt die bereinigte Variable zurück
	 */
	final private function bereinigen( &$eingabe )
	{
		if (get_magic_quotes_gpc()) { $eingabe = stripslashes( $eingabe ); }
		$eingabe = trim( $eingabe );
		$eingabe = preg_replace( $this->bereinigen_suchmuster, $this->bereinigen_ersatz, $eingabe );
		$eingabe = wordwrap( $eingabe, 45, " ", true );
		return $eingabe;
	}


	/**
	 * Methode zum prüfen auf Injections
	 *
	 * @param  mixed  $eingabe Benutzereingabe
	 * @param  string $label Bezeichnung des Feldes - wird für die Fehlermeldung benötigt
	 * @return mixed  Gibt die bereinigte Variable zurück
	 */
	final public function injection( &$eingabe, $label )
	{
		// Auf potentielle SQL Injections prüfen
		foreach ($this->sql_injection as $injection)
		{
			if (preg_match( "/{$injection}/i", $eingabe ))
			{
				$this->feld_error[$label] = self::FELD_ERROR_INJECTION;
				$eingabe = '';
			}
		}

		// Auf potentielle Email Injections prüfen
		foreach ($this->email_injection as $injection)
		{
			if (preg_match( "/{$injection}/i", $eingabe ))
			{
				$this->feld_error[$label] = self::FELD_ERROR_INJECTION;
				$eingabe = '';
			}
		}
		return $eingabe;
	}


	/**
	 * Prüft die Eingabe auf eine gültige Zahl
	 *
	 * @param  integer $eingabe Benutzereingabe
	 * @param  string  $label Bezeichnung des Feldes - wird für die Fehlermeldung benötigt
	 * @return mixed   Gibt die bereinigte Variable zurück oder FALSE
	 */
	final public function pruefeZahl( &$eingabe, $label )
	{
		self::bereinigen( &$eingabe );
		self::injection( $eingabe, $label );
		if (preg_match( $this->muster_zahl, $eingabe ))
		{
			return $eingabe;
		}
		else
		{
			$this->feld_error[$label] = self::FELD_ERROR_FORMAT;
			return false;
		}
	}


	/**
	 * Prüft die Eingabe auf eine gültige Postleitzahl
	 *
	 * @param  integer $eingabe Benutzereingabe
	 * @param  string  $label Bezeichnung des Feldes - wird für die Fehlermeldung benötigt
	 * @return mixed   Gibt die bereinigte Variable zurück oder FALSE
	 */
	final public function pruefePLZ( &$eingabe, $label )
	{
		self::bereinigen( $eingabe );
		self::injection( $eingabe, $label );
		if (preg_match( $this->muster_plz, $eingabe ))
		{
			return $eingabe;
		}
		else
		{
			$this->feld_error[$label] = self::FELD_ERROR_FORMAT;
			return false;
		}
	}


	/**
	 * Prüft die Eingabe auf eine gültige Datum
	 *
	 * @param  mixed  $eingabe Benutzereingabe
	 * @param  string $label Bezeichnung des Feldes - wird für die Fehlermeldung benötigt
	 * @param  string $trennzeichen Trennzeichen an dem das Datum zerlegt wird
	 * @return mixed  Gibt die bereinigte Variable zurück oder FALSE
	 */
	final public function pruefeDatum( &$eingabe, $label, $trennzeichen )
	{
		self::bereinigen( $eingabe );
		self::injection( $eingabe, $label );
		if (preg_match( $this->muster_datum, $eingabe ))
		{
			$datum_elemente = explode( $trennzeichen, $eingabe );
			// Datum auf Gültigkeit prüfen
			if (checkdate( $datum_elemente[1], $datum_elemente[0], $datum_elemente[2] ))
			{
				return $eingabe;			
			}
			else
			{
				$this->feld_error[$label] = self::FELD_ERROR_DATUM;
				return false;			
			}
		}
		else
		{
			$this->feld_error[$label] = self::FELD_ERROR_DATUM;
			return false;
		}
	}


	/**
	 * Prüft die Eingabe auf eine gültige Telefonnummer
	 *
	 * @param  mixed  $eingabe Benutzereingabe
	 * @param  string $label Bezeichnung des Feldes - wird für die Fehlermeldung benötigt
	 * @return mixed  Gibt die bereinigte Variable zurück oder FALSE
	 */
	final public function pruefeTelefon( &$eingabe, $label )
	{
		self::bereinigen( $eingabe );
		self::injection( $eingabe, $label );
		if (preg_match( $this->muster_telefon, $eingabe ))
		{
			return $eingabe;
		}
		else
		{
			$this->feld_error[$label] = self::FELD_ERROR_FORMAT;
			return false;
		}
	}


	/**
	 * Prüft die Eingabe auf einen gültigen Geldbetrag
	 *
	 * @param  mixed  $eingabe Benutzereingabe
	 * @param  string $label Bezeichnung des Feldes - wird für die Fehlermeldung benötigt
	 * @return mixed  Gibt die bereinigte Variable zurück oder FALSE
	 */
	final public function pruefeGeldbetrag( &$eingabe, $label )
	{
		self::bereinigen( $eingabe );
		self::injection( $eingabe, $label );
		if (preg_match( $this->muster_geldbetrag, $eingabe ))
		{
			return $eingabe;
		}
		else
		{
			$this->feld_error[$label] = self::FELD_ERROR_FORMAT;
			return false;
		}
	}


	/**
	 * Prüft die Eingabe auf ein gültiges Wort
	 *
	 * @param  string  $eingabe Benutzereingabe
	 * @param  string  $label Bezeichnung des Feldes - wird für die Fehlermeldung benötigt
	 * @param  integer $min_laenge Mindestlänge der Eingabe
	 * @param  integer $max_laenge Maximallänge der Eingabe
	 * @return mixed   Gibt die bereinigte Variable zurück oder FALSE
	 */
	final public function pruefeWort( &$eingabe, $label, $min_laenge=999, $max_laenge=999 )
	{
		self::bereinigen( $eingabe );
		self::injection( $eingabe, $label );
		// Mindestlänge prüfen falls gesetzt
		if ($min_laenge != 999)
		{
			if (strlen( $eingabe ) < $min_laenge)
			{
				$this->feld_error[$label] = self::FELD_ERROR_ZUKURZ;
				return false;
			}
		}
		// Maximallänge prüfen falls gesetzt
		if ($max_laenge != 999)
		{
			if (strlen( $eingabe ) > $max_laenge)
			{
				$this->feld_error[$label] = self::FELD_ERROR_ZULANG;
				return false;
			}
		}
		// Auf Suchmuster prüfen
		if (preg_match( $this->muster_wort, $eingabe ))
		{
			return $eingabe;
		}
		else
		{
			$this->feld_error[$label] = self::FELD_ERROR_FORMAT;
			return false;
		}
	}


	/**
	 * Prüft die Eingabe auf einen gültigen Name
	 *
	 * @param  string  $eingabe Benutzereingabe
	 * @param  string  $label Bezeichnung des Feldes - wird für die Fehlermeldung benötigt
	 * @param  integer $min_laenge Mindestlänge der Eingabe
	 * @param  integer $max_laenge Maximallänge der Eingabe
	 * @return mixed   Gibt die bereinigte Variable zurück oder FALSE
	 */
	final public function pruefeName( &$eingabe, $label, $min_laenge=999, $max_laenge=999 )
	{
		self::bereinigen( $eingabe );
		self::injection( $eingabe, $label );
		// Mindestlänge prüfen falls gesetzt
		if ($min_laenge != 999)
		{
			if (strlen( $eingabe ) < $min_laenge)
			{
				$this->feld_error[$label] = self::FELD_ERROR_ZUKURZ;
				return false;
			}
		}
		// Maximallänge prüfen falls gesetzt
		if ($max_laenge != 999)
		{
			if (strlen( $eingabe ) > $max_laenge)
			{
				$this->feld_error[$label] = self::FELD_ERROR_ZULANG;
				return false;
			}
		}
		// Auf Suchmuster prüfen
		if (preg_match( $this->muster_name, $eingabe ))
		{
			return $eingabe;
		}
		else
		{
			$this->feld_error[$label] = self::FELD_ERROR_FORMAT;
			return false;
		}
	}


	/**
	 * Prüft die Eingabe auf mögliche Injections in Langtext
	 *
	 * @param  string  $eingabe Benutzereingabe
	 * @param  string  $label Bezeichnung des Feldes - wird für die Fehlermeldung benötigt
	 * @param  integer $min_laenge Mindestlänge der Eingabe
	 * @param  integer $max_laenge Maximallänge der Eingabe
	 * @return mixed   Gibt die bereinigte Variable zurück oder FALSE
	 */
	final public function pruefeText( &$eingabe, $label, $min_laenge=999, $max_laenge=999 )
	{
		self::bereinigen( $eingabe );
		self::injection( $eingabe, $label );
		// Mindestlänge prüfen falls gesetzt
		if ($min_laenge != 999)
		{
			if (strlen( $eingabe ) < $min_laenge)
			{
				$this->feld_error[$label] = self::FELD_ERROR_ZUKURZ;
				return false;
			}
		}
		// Maximallänge prüfen falls gesetzt
		if ($max_laenge != 999)
		{
			if (strlen( $eingabe ) > $max_laenge)
			{
				$this->feld_error[$label] = self::FELD_ERROR_ZULANG;
				return false;
			}
		}
		return $eingabe;
	}


	/**
	 * Prüft die Eingabe auf einen gültige Email-Adresse
	 *
	 * @param  string  $eingabe Benutzereingabe
	 * @param  string  $label Bezeichnung des Feldes - wird für die Fehlermeldung benötigt
	 * @return mixed   Gibt die bereinigte Variable zurück oder FALSE
	 */
	final public function pruefeEmail( &$eingabe, $label )
	{
		self::bereinigen( $eingabe );
		self::injection( $eingabe, $label );
		// Auf Suchmuster prüfen
		if (preg_match( $this->muster_email, $eingabe ))
		{
			return $eingabe;
		}
		else
		{
			$this->feld_error[$label] = self::FELD_ERROR_FORMAT;
			return false;
		}
	}


	/**
	 * Prüft die Eingabe auf einen gültige Web-Adresse
	 *
	 * @param  string  $eingabe Benutzereingabe
	 * @param  string  $label Bezeichnung des Feldes - wird für die Fehlermeldung benötigt
	 * @return mixed   Gibt die bereinigte Variable zurück oder FALSE
	 */
	final public function pruefeURL( &$eingabe, $label )
	{
		self::bereinigen( $eingabe );
		self::injection( $eingabe, $label );
		// Auf URL Prefix prüfen und ggfs. hinzufügen
		if (strtolower( substr( $eingabe, 0, 7 ) ) != "http://" &&
			strtolower( substr( $eingabe, 0, 8 ) ) != "https://")
		{
			$eingabe = "http://" .$eingabe;
		}
		// Auf Suchmuster prüfen
		if (preg_match( $this->muster_url, $eingabe ))
		{
			return $eingabe;
		}
		else
		{
			$this->feld_error[$label] = self::FELD_ERROR_FORMAT;
			return false;
		}
	}


	/**
	 * Gibt das Fehlermeldungen-Array zurück
	 *
	 * @return array Liefert das Array mit den Fehlermeldungen zurück
	 */
	final public function getError()
	{
		return $this->feld_error;
	}
}

?>