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

require_once 'tests/Helpers.php';

class FirstTest extends \Helpers
{

    public function test1()
    {
        $this->assertEquals('', '');
    }

    public function test2()
    {
        $doc = new html\Document(new html\String('Test'));
        $doc->getHtml()->getHead()->addCssFile('style.css');

        $form = new form\FormPost('test');
        $doc->getHtml()->getBody()->addChild($form);

        $window = new \hemio\edentata\gui\Window('Abc', 'Subtitle');
        $form->addChild($window);
        $window->addButtonRight(new form\FieldSubmit('submit', _('Submit')));

        $selectbox = new \hemio\edentata\gui\Selectbox();
        $window->addChild($selectbox);

        $options   = new form\Container();
        $options[] = new form\FieldSubmit('submit', _('Submit'));
        $selectbox->setOptions($options);

        for ($i = 1; $i < 20; $i++) {
            $selectbox->addItem('test'.$i, sprintf('Test Nr. %d', $i));
        }

        $listbox = new \hemio\edentata\gui\Listbox();
        $window->addChild($listbox);

        for ($i = 1; $i < 20; $i++) {
            $str = new html\String(sprintf(_('Test Nr. %d'), $i));
            $a   = new html\A();
            $a[] = $str;
            $listbox->addLine($a);
        }


        $this->_assertEqualsXmlFile($doc, 'selectbox.html');
    }
}
