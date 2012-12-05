<?php

/**
 * phpBuddy.eu Bcrypt Klasse
 *
 * Erzeugt einen Passwort-Hash mittels Blowfish Algorithmus.
 *
 * @author     Andreas Skodzek <webmaster@phpbuddy.eu>
 * @link       http://www.phpbuddy.eu/
 * @copyright  2011 Andreas Skodzek
 * @license    GNU Public License <http://www.gnu.org/licenses/gpl.html>
 * @package    phpBuddy Bcrypt
 * @version    1.0 released 10.02.2011
 */

class Bcrypt
{
	/**
	 * Konstante mit erlaubten Zeichen für den Passwortzusatz
	 */
	const SALTCHARS = './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/**
	 * @param int  Anzahl der Wiederholungen (Cost). minimal 4, maximal 31
	 */
	public static $cost = 12;

	/**
	 *
	 * @param array  Nimmt die Informationen Passwort, Salt, Wiederholungen und Hash auf
	 */
	public static $info = array();


	/**
	 * Erzeugt einen Hash aus einem Passwort
	 *
	 * @param string  $passwort Passwort
	 * @param int  $cost Anzahl der Wiederholungen
	 * @return array
	 */
	public static function hash($passwort, $cost = NULL)
	{
		// Prüfen ob Blowfish vom Server unterstützt wird
		self::check_blowfish();

		// Blowfish Algorithmus
		self::$info['algo'] = '$2a$';

		// 22-stelligen Salt erzeugen der für Blowfish erforderlich ist
		$tmp_salt = '';
		for ($i = 0; $i <= 21; $i++)
		{
			$tmp_str   = str_shuffle(self::SALTCHARS);
			$tmp_salt .= $tmp_str[0];
		}

		// Salt im Info-Array ablegen
		self::$info['salt_string'] = $tmp_salt;

		// Anzahl der Wiederholungen ermitteln
		if ($cost)
		{
			// Führende Null voranstellen bei einstelliger Wiederholung
			self::$info['cost'] = sprintf('%02d', min(31, max(intval($cost), 4)));
		}
		else
		{
			// Standard verwenden
			self::$info['cost'] = self::$cost;
		}

		// Komplettes Salt mit Algorithmus und Wiederholungen
		self::$info['salt_komplett'] =	self::$info['algo'] . self::$info['cost'] . '$' . self::$info['salt_string'] . '$';

		// Passwort Hash erzeugen
		self::$info['hash'] = crypt($passwort, self::$info['salt_komplett']);

		// Info-Array zurückgeben
		return self::$info;
	}


	/**
	 * Prüft ein Passwort gegen einen Hash
	 *
	 * @param string  $passwort Passwort aus dem der zu vergleichende Hash erzeugt werden soll
	 * @param string  $hash Der zu vergleichende Hash mit komplettem Salt
	 * @return bool
	 */
	public static function check_hash($passwort, $hash)
	{
		// Prüfen ob Blowfish vom Server unterstützt wird
		self::check_blowfish();

		// Komplettes Salt mit Algorithmus und Wiederholungen aus dem Hash extrahieren
		$tmp_salt = substr($hash, 0, 29);
		
		// Vergleichshash erzeugen
		$tmp_hash = crypt($passwort, $tmp_salt . '$');

		// Stimmt das Passwort mit dem Hash überein ist der Rückgabewert TRUE, ansonsten FALSE
		return ($tmp_hash == $hash) ? TRUE : FALSE;
	}


	/**
	 * Prüft ob der Blowfish Algorithmus unterstützt wird
	 */
	private static function check_blowfish()
	{
		if ( ! defined('CRYPT_BLOWFISH'))
		{
			throw new Exception('Bcrypt wird von diesem Server leider nicht unterstützt!');
		}
	}
}
