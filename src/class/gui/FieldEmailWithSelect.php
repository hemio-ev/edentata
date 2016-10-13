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

namespace hemio\edentata\gui;

use hemio\html;
use hemio\form;

/**
 * Description of FieldEmailWithSelect
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class FieldEmailWithSelect extends \hemio\form\Container {

    /**
     *
     * @return form\FieldSelect
     */
    public function getDomain() {
        return $this['p']['select']['select'];
    }

    /**
     *
     * @return form\FieldText
     */
    public function getLocalPart() {
        return $this['p']['text'];
    }

    public function __construct($name1 = 'localpart', $name2 = 'domain', $title1 = null, $title2 = null) {
        if ($title1 === null)
            $title1 = _('Local Part');

        if ($title2 === null)
            $title2 = _('Domain');

        $p = new html\P();
        $p->addCssClass('multiple');
        $this['p'] = $p;

        $p['text'] = new form\FieldText($name1, $title1);
        $p['text']->setRequired(true);
        $p['text']->addInheritableAppendage(
                form\FormPost::FORM_FIELD_TEMPLATE, new form\template\FormPlainControl
        );
        $p['text']->setPattern(
                '[a-zA-Z0-9.-]+'
                , _('Local part: May only contain letters "a-z", numbers "0-9", dashes "-" and dots "."')
        );
        $p['text']->setValueTransformation('localpart_tolower', 'strtolower');

        $p['at'] = new html\Span();
        $p['at']->addCssClass('between');
        $p['at']->addChild(new html\Str('@'));

        $p['select'] = new html\Span();
        $p['select']->addCssClass('select');
        $p['select']['select'] = new form\FieldSelect($name2, $title2);
        $p['select']['select']->addInheritableAppendage(
                form\FormPost::FORM_FIELD_TEMPLATE . '_SELECT', new form\template\FormPlainControl
        );
    }

}
