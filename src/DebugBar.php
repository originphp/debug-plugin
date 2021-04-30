<?php
/**
 * OriginPHP Framework
 * Copyright 2018 - 2021 Jamiel Sharief.
 *
 * Licensed under The MIT License
 * The above copyright notice and this permission notice shall be included in all copies or substantial
 * portions of the Software.
 *
 * @copyright   Copyright (c) Jamiel Sharief
 * @link        https://www.originphp.com
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Simple debugger whilst developing the framework.
 */

namespace Debug;

use Origin\Core\Config;
use Origin\Utility\Number;
use Origin\Http\Dispatcher;
use Origin\Model\ConnectionManager;

class DebugBar
{
    /**
     * Renders the DebugBar.
     *
     * @return void
     */
    public function render(): void
    {
        /**
         * @deprecated debug
         */
        if (! Config::read('App.debug') && ! Config::read('debug')) {
            return;
        }

        /**
         * Don't Load in CLI (e.g. unit tests)
         */
        if (isConsole()) {
            return;
        }

        $controller = Dispatcher::instance()->controller();
        $request = $controller->request();

        $log = [];
        if (ConnectionManager::has('default')) {
            $connection = ConnectionManager::get('default');
            $log = $connection->log();
        }

        $debugVars = [
            'debug_sql' => $log,
            'debug_request' => [
                'params' => $request->params(),
                'query' => $request->query(),
                'data' => $request->data(),
                'cookie' => $request->cookies(),
            ],
            'debug_vars' => [
                'variables' => $controller->viewVars(),
                'memory' => Number::readableSize(memory_get_peak_usage()),
                'took' => Number::precision(microtime(true) - START_TIME, 2) . ' seconds',
            ],
            'debug_session' => $request->session()->toArray()
        ];

        extract($debugVars);
        include 'view.ctp';
    }
}
