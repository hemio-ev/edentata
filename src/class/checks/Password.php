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

namespace hemio\edentata\checks;

/**
 * Description of Password
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Password extends \hemio\form\Check {

    public function __invoke($value) {
        $stdout = \hemio\edentata\Utils::sysExec(
                        '/usr/sbin/cracklib-check'
                        , $value . PHP_EOL
        );

        $split = explode(': ', $stdout);

        if (count($split) < 2)
            throw new \hemio\edentata\exception\Error(
            'Cracklib output not in expected format'
            );

        $msg = trim(array_pop($split));
        $this->message = sprintf(_('The chosen password is too weak: %s.'), $msg);

        return $msg === 'OK';
    }

}
