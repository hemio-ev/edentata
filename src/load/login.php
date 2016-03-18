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

use hemio\form;
use hemio\html;

$title    = new html\Str('Edentata Login');
$document = new form\Document($title);

$document->getHtml()->getHead()->setBaseUrl($config['base_url']);
$document->getHtml()->getHead()->addJsFile('static/js/login.js');
if ($authMethod === 'logout')
    $document->getHtml()->getHead()->addJsFile('static/js/logout.js');

$document->getHtml()->getHead()->addCssFile('static/design/style.css');

$pdo = new sql\Connection($config['database_dsn']);

$loadedModule = new LoadModule('login_http', $pdo, $i10n);
$i10n->setDomainModule($loadedModule);
$title->setValue(module\login_http\Login::loginTitle());

$document->getHtml()->getBody()->addChild(
    $loadedModule->getContent(
        new Request(
        $_GET
        , Utils::getPost(), $_SERVER['REQUEST_URI']
        , $config['base_url']
        )
        , $i10n
    )
);

$document->getHtml()->getBody()->addChild(new ContentFooter($config));

echo $document->__toString();
