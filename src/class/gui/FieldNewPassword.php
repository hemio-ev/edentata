<?php

/*
 * Copyright (C) 2015 Sophie Herold <sophie@hemio.de>
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

namespace hemio\edentata\gui;

use hemio\form;

/**
 * Description of FieldNewPassword
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class FieldNewPassword extends form\Container {

    /**
     * 
     * @param type $name
     * @param type $title
     * @param type $titleRepeat
     */
    public function __construct($name, $title = null, $titleRepeat = null) {
        if ($title === null)
            $title = _('New Password');
        if ($titleRepeat === null)
            $titleRepeat = _('Repeat Password');

        $password = new form\FieldPassword($name, $title);

        $repeat = new form\FieldPassword(
                $name . '__repeat'
                , $titleRepeat
        );

        $eqCheck = new form\CheckCustom(
                'eq'
                , function ($pw) use ($password) {
            return $pw === $password->getValueUser();
        }
                , _('The repeated password does not match the original one.')
        );

        $password->setRequired();
        $repeat->setRequired();
        $repeat->addValidityCheck($eqCheck);
        $password->addValidityCheck(new form\CheckMinLength(8));
        $password->addValidityCheck(new \hemio\edentata\checks\Password());

        $this['password'] = $password;
        $this['password_repeat'] = $repeat;
    }

    /**
     * 
     * @return form\FieldPassword
     */
    public function getPassword() {
        return $this['password'];
    }

    /**
     * 
     * @return form\FieldPassword
     */
    public function getPasswordRepeat() {
        return $this['password_repeat'];
    }

    public function dataValid() {
        return $this->getPassword()->dataValid() && $this->getPasswordRepeat()->dataValid();
    }

}
