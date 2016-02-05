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

namespace hemio\edentata\tests;

use hemio\html;
use hemio\form;
use hemio\edentata;
use hemio\edentata\gui;

require_once 'tests/Helpers.php';

class GuiAllTest extends \Helpers {

    public function test_ensemble() {
        $num = 3;

        $doc = new html\Document(new html\Str('Test'));
        $doc->getHtml()->getHead()->addCssFile('design/style.css');

        $form = new form\FormPost('test');
        $doc->getHtml()->getBody()->addChild($form);

        $window = new \hemio\edentata\gui\Window('Abc', 'Subtitle');
        $form->addChild($window);
        $window->addButtonRight(new form\FieldSubmit('submit', _('Submit')));
        $window->setCssProperty('max-width', '40em');

        $window[] = new gui\FieldSwitch('switch', _('Switch'));

        $emailWithSelect = new gui\FieldEmailWithSelect();
        for ($i = 1; $i <= $num; $i++)
            $emailWithSelect->getDomain()
                    ->addOption('test' . $i, sprintf('Test Nr. %d', $i));
        $window[] = $emailWithSelect;

        $radioList = new gui\FieldRadioList('radio_list', _('Radio List'));
        for ($i = 1; $i <= $num; $i++)
            $radioList->addOption('test' . $i, sprintf('Test Nr. %d', $i));
        $window[] = $radioList;

        $selectbox = new \hemio\edentata\gui\Selectbox();
        $window->addChild($selectbox);

        $options = new form\Container();
        $options[] = new form\FieldSubmit('submit', _('Submit'));
        $selectbox->setOptions($options);


        for ($i = 1; $i <= $num; $i++)
            $selectbox->addItem('test' . $i, sprintf('Test Nr. %d', $i));

        $listbox = new \hemio\edentata\gui\Listbox();
        $window->addChild($listbox);

        for ($i = 1; $i <= $num; $i++) {
            $str = new html\Str(sprintf(_('Test Nr. %d'), $i));
            $listbox->addLinkEntry(new edentata\Request(), $str);
        }


        $this->_assertEqualsXmlFile($doc, 'ensemble.html');

        $doc->getHtml()->getHead()->addCssFile('design/style_dark.css');
        $this->_assertEqualsXmlFile($doc, 'ensemble-black.html');
    }

}
