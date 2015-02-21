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

use hemio\html;

chdir(__DIR__ . '/../../');

require_once 'vendor/autoload.php';

# external data
$request = new Request($_GET, $_POST);
$modulesNavi = [
    'email',
    'email_list',
    'jabber',
    'dns',
    'server_access'
];

$i10 = new I10n();

$activeModuleName = $request->module;

try {
    # doc
    $doc = new html\Document(new html\String('Edentata Dev.'));
    $doc->getHtml()->getHead()->addCssFile('static/design/style.css');
    $doc->getHtml()->setAttribute('lang', 'en');

    $body = $doc->getHtml()->getBody();

    $header = new html\Header();
    $body['header'] = $header;

    $body['main'] = new html\Div();
    $body['main']->addCssClass('main');

    $mainNav = new html\Div();
    $body['main']['main_nav'] = $mainNav;
    $mainNav->setId('main_nav');

    $mainContent = new html\Div();
    $body['main']['main_content'] = $mainContent;
    $mainContent->setId('main_content');
    $mainContent['messages'] = new \hemio\form\Container;

    # db connect
    $pdo = new sql\Connection('pgsql:dbname=test1', 'postgres');
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

    # db auth
    if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])) {
        header('WWW-Authenticate: Basic realm="Edentata"');
        header('HTTP/1.1 401 Unauthorized');
        throw new exception\Error(_('Login failed'));
    }

    $usrData = [
        'p_name' => $_SERVER['PHP_AUTH_USER'],
        'p_password' => $_SERVER['PHP_AUTH_PW']
    ];

    try {
        $qryAuth = new sql\QuerySelectFunction($pdo, 'user.ins_login', $usrData);
        $qryAuth->execute();
    } catch (\Exception $e) {
        header('WWW-Authenticate: Basic realm="Edentata"');
        header('HTTP/1.1 401 Unauthorized');
        throw new exception\Error(_('Login failed'));
    }

    $header->addChild(new html\String('Edentata – User: ' . $_SERVER['PHP_AUTH_USER']));
    $aSettings = new html\A;
    $aSettings[] = new html\String(_('Settings'));
    $settings = new Request(['module' => 'user']);
    $aSettings->setAttribute('href', $settings->getUrl());
    $header[] = $aSettings;

    # navi
    $nav = (new ContentNav($modulesNavi, $i10))->getNav();
    while ($nav->unhandledEvents()) {
        try {
            $nav->handleEvent();
        } catch (exception\Printable $event) {
            $mainContent['messages'][] = new gui\Message($event);
        }
    }
    $mainNav[] = $nav->getContent();

    # module
    $loadedModule = new LoadModule($activeModuleName, $pdo);
    $i10->setDomainModule($loadedModule);

    # generate content
    $content = $loadedModule->getContent($request);
    $mainContent->addChild($content);
} catch (\PDOException $e) {
    $mainContent['messages']
            ->addChild(new gui\Message(new exception\Error('*DB operation failed* ' . $e->getMessage())));
} catch (exception\Printable $e) {
    $mainContent['messages']->addChild(new gui\Message($e));
}

echo $doc->__toString();
