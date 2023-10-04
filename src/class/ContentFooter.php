<?php

/*
 * Copyright (C) 2016 Sophie Herold <sophie@hemio.de>
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

use hemio\html;

/**
 * Description of ContentFooter
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class ContentFooter extends html\Footer {

    public function __construct($config) {
        $version = file_get_contents('VERSION');

        if ($config->enabled('footer'))
            $this['text'] = new html\Str(_msg($config['footer'], ['version' => $version]));

        if ($config->enabled('site_information_url')) {
            $siteInfo = new html\A;
            $siteInfo->setAttribute('href', $config['site_information_url']);
            $siteInfo[] = new html\Str(_('Site Information'));

            if (isset($this['text']))
                $this[] = new html\Str(' – ');

            $this['site_information'] = $siteInfo;
        }
    }

}
