<?php
/*
 * Copyright (C) 2015 Michael Herold <quabla@hemio.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace hemio\edentata;

/**
 * Description of I10n
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class I10n
{
    public static $supportedLocales = ['en_US.utf-8'];
    public $locale                  = null;
    protected $domainsLoaded        = [];

    public function __construct()
    {
        $guessedLocale = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);

        $locales = array_filter(self::$supportedLocales
            ,
                                function ($supportedLocale) use ($guessedLocale) {
            return substr($supportedLocale, 0, 2) == substr($guessedLocale, 0, 2);
        }
        );

        if (count($locales) > 0)
            $this->locale = array_pop($locales);
        else
            $this->locale = self::$supportedLocales[0];

        putenv('LC_ALL='.$this->locale);
        if (!setlocale(LC_ALL, $this->locale))
            echo 'failed to set locale';

        bindtextdomain('edentata', 'locale');
        $this->setDomainMain();
    }

    public function setDomainModule(LoadModule $module)
    {
        $domain = 'edentata_'.$module->getId();

        if (!array_key_exists($domain, $this->domainsLoaded)) {
            bindtextdomain($domain, $module->getDir().'/locale');
            $this->domainsLoaded[$domain] = $domain;
        }

        textdomain($domain);
    }

    public function setDomainMain()
    {
        textdomain('edentata');
    }

    public function getLang()
    {
        return substr($this->locale, 0, 2);
    }
}
