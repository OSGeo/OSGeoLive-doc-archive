<?php
# $Id: class_locale.php 8404 2012-07-03 07:22:14Z tbaschetti $
# http://www.mapbender.org/index.php/class_locale.php
# Copyright (C) 2002 CCGIS
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2, or (at your option)
# any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA

require_once(dirname(__FILE__)."/../../core/globalSettings.php");

/**
 * sets the locale, depending on various settings:
 * 1) a language ID passed to the constructor
 * 2) the browser settings $_SERVER["HTTP_ACCEPT_LANGUAGE"]
 * 3) a default language ID
 *
 */
class Mb_locale {
	var $knownLanguages = null;
	var $systemLocales = null;
	var $browserLanguages = null;
	var $os = null;
	var $name = null;
	var $defaultLanguage = "en";
	var $status = "No locale set.";

	function Mb_locale($languageId) {
		if (!$languageId) {
			$languageId = LANGUAGE;
		}
		$e = new Mb_notice("class_locale: setting locale to " . $languageId);
		if (USE_I18N) {
			if (!$this->setCurrentLocale($languageId)) {
				$e = new Mb_exception("Locale could not be set. Language ID: '" . $languageId . "'");
			}
		}
		else {
			$this->setCurrentLocale($this->defaultLanguage);
		}
	}

    /**
	 * Get the current locale, evaluating GET/POST variables, browser languages
     * and a default locale (in that preference)
	 *
	 * @returns current locale
	 */
	function setCurrentLocale($languageId) {

		// try to set the locale to $languageId
		if ($this->checkAndSetLocale($languageId)) {
			return true;
		}
		else {
			$e = new Mb_notice("class_locale: no input parameter specified.");
		}

		// determine the browser setting and try to set locale according to that
		if ($this->browserLanguage == null) {
			$this->setBrowserLanguages();
		}
		foreach ($this->browserLanguages as $lang) {
			$e = new Mb_notice("trying browser setting " . $lang);
			if ($this->checkAndSetLocale($lang)) {
				return true;
			}
		}

		// set to default language
		$e = new Mb_notice("trying default language " . $this->defaultLanguage);
		return $this->checkAndSetLocale($this->defaultLanguage);
	}

	/**
	 * checks if a locale is available; if yes, it is set via setlocale
	 *
	 * @returns true if the the locale is set successfully; otherwise false
	 */
	function checkAndSetLocale($languageId) {
		if ($this->os == null) {
			$this->os = $this->guessHostOS();
		}
		
		if (!USE_I18N || ($this->os != null && isset($languageId))) {
			if ($this->isKnownLanguage($languageId)) {
		
				if ($this->systemLocales == null) {
					$this->setSystemLocales();
				}
		
				$locale = $this->systemLocales[$this->knownLanguages[$languageId]][$this->os];
				$selectedLocale = setlocale(LC_MESSAGES, $locale);

				if ($selectedLocale) {
					$this->name = $selectedLocale;
					Mapbender::session()->set("mb_lang",$languageId);
					Mapbender::session()->set("mb_locale",$this->name);
					$e = new Mb_notice("locale " . $this->name . " ok on " . $this->os);

					// from http://de3.php.net/manual/de/function.gettext.php
					$path = bindtextdomain("Mapbender", dirname(__FILE__)."/../../resources/locale/");
					$enc = bind_textdomain_codeset("Mapbender", "UTF-8");
					$dom = textdomain("Mapbender");
					return true;
				}
			}
		}
		$e = new Mb_notice("locale " . $locale . " not found.");
		return false;
	}

	/**
	* Guess the operating system which on which this code is running
	* multiple methods are tested for reliably guessing the os
	*
	* @private
	* @returns string with os name
	*/
	function guessHostOS(){
		if (strncasecmp(php_uname(), 'Windows', 7) == 0) {
			return 'windows';
		}
		else if (strncasecmp(php_uname(), 'Linux', 5) == 0) {
			return 'linux';
		}
		else if (strncasecmp(php_uname(), 'OpenBSD', 7) == 0) {
			return 'bsd';
		}
		else if (strncasecmp(php_uname(), 'FreeBSD', 7) == 0) {
			return 'bsd';
		}
		new mb_exception('unknown platform: could not interpret uname. php_uname() returned '. php_uname().'. Please report to MB developers');
		return null;
	}

        /**
         * checks if a language is supported
         *
         * @returns true if the language is supported; otherwise false
         */
        function isKnownLanguage($languageId) {
			if ($this->knownLanguages == null) {
				$this->setKnownLanguages();
			}
			if (array_key_exists($languageId, $this->knownLanguages)) {
				return true;
			}
			else {
				$e = new Mb_notice("language " . $languageId . " not supported.");
			}
			return false;
        }



        /**
         * determines the available Locales on this system
         */
        function setSystemLocales() {
			$this->systemLocales['pt_PT'] = array(
				'linux' => 'pt_PT.utf8',
				'windows' => 'Portuguese_Portugal.1252',
				'bsd' => 'pt_PT',
				'posix' => 'pt_PT'
			);
			$this->systemLocales['fr_FR'] = array(
				'linux' => 'fr_FR.utf8',
				'windows' => 'French_France.1252',
				'bsd' => 'fr_FR',
				'posix' => 'fr_FR'
			);
			$this->systemLocales['es_ES'] = array(
				'linux' => 'es_ES.utf8',
				'windows' => 'Spanish_Spain.1252',
				'bsd' => 'es_ES',
				'posix' => 'es_ES'
			);
			$this->systemLocales['it_IT'] = array(
				'linux' => 'it_IT.utf8',
				'windows' => 'Italian_Italy.1252',
				'bsd' => 'it_IT',
				'posix' => 'it_IT'
			);
			$this->systemLocales['de_DE'] = array(
				'linux' => 'de_DE.utf8',
				'windows' => 'German_Germany.1252',
				'bsd' => 'de_DE',
				'posix' => 'de_DE'
			);
			$this->systemLocales['en_US'] = array(
				'linux' => 'en_US.utf8',
				'windows' => 'English_United States.1252',
				'bsd' => 'en_US',
				'posix' => 'en_US'
			);
			$this->systemLocales['bg_BG'] = array(
				'linux' => 'bg_BG.utf8',
				'windows' => 'Bulgarian_Bulgaria.1251',
				'bsd' => 'bg_BG',
				'posix' => 'bg_BG'
			);
			$this->systemLocales['el_GR'] = array(
				'linux' => 'el_GR.utf8',
				'windows' => 'Greek_Greece.1253',
				'bsd' => 'el_GR',
				'posix' => 'el_GR'
			);
			$this->systemLocales['hu_HU'] = array(
				'linux' => 'hu_HU.utf8',
				'windows' => 'hu_HU.1250',
				'bsd' => 'hu_HU',
				'posix' => 'hu_HU'
			);
        }

        /**
         * set the known languages
         */
        function setKnownLanguages() {
                $this->knownLanguages = array(
						'en_US' => 'en_US',
						'en' => 'en_US',
						'de_DE' => 'de_DE',
						'de' => 'de_DE',
						'bg_BG' => 'bg_BG',
						'bg' => 'bg_BG',
						'es_ES' => 'es_ES',
						'es' => 'es_ES',
						'nl_NL' => 'nl_NL',
						'nl' => 'nl_NL',
						'fr_FR' => 'fr_FR',
						'fr' => 'fr_FR',
						'el_GR' => 'el_GR',
						'gr' => 'el_GR',
						'hu_HU' => 'hu_HU',
						'hu' => 'hu_HU',
						'pt_PT' => 'pt_PT',
						'pt' => 'pt_PT',												
						'it_IT' => 'it_IT',
						'it' => 'it_IT');
        }

        /**
         * sets the languages accepted by the client browser
         */
        function setBrowserLanguages () {
                $this->browserLanguages = array();

            $bLangs = explode(',', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
            foreach ($bLangs as $lang) {
                        if (strpos($lang, ';') === false)
                                array_push($this->browserLanguages, $lang);
                        else
                                array_push($this->browserLanguages, substr($lang, 0, strpos($lang, ';')));
            }
        }
}
?>
