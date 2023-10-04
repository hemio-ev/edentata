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

use hemio\html;
use hemio\html\Interface_\HtmlCode;
use hemio\form\Abstract_\FormElement;

/**
 * Description of ConditionalVisibility
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class ConditionalVisibility extends \hemio\form\Container
{
    /**
     *
     * @var HtmlCode
     */
    protected $hiding;

    /**
     *
     * @var FormElement
     */
    protected $condition;

    /**
     *
     * @var bool
     */
    protected $reverse;

    public function __construct(HtmlCode $hiding, FormElement $condition,
                                $reverse = false)
    {
        $this->hiding    = $hiding;
        $this->condition = $condition;
        $this->reverse   = $reverse;

        $this['div']   = new html\Div;
        $this['div'][] = $hiding;
        $this['style'] = new html\Style();
        $this['style']->setAttribute('scoped', true);
    }

    public function __toString()
    {
        if (!$this['div']->getAttribute('id'))
            $this['div']->setAttribute('id',
                                       $this->condition->getHtmlId().'_hiding');

        $cssSelector = $this->reverse ? 'not(:checked)' : 'checked';

        $this['style'][] = new html\Str(
            '#'.$this->condition->getHtmlId().':'.$cssSelector.
            ' ~ #'.$this['div']->getAttribute('id').' { display: none; }');

        return parent::__toString();
    }
}
