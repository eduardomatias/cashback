<?php
/**
 * 2007-2016 [PagSeguro Internet Ltda.]
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    PagSeguro Internet Ltda.
 * @copyright 2007-2016 PagSeguro Internet Ltda.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 *
 */

namespace vendor\pagamento\pagseguro\Resources;

use vendor\pagamento\pagseguro\Resources\Responsibility\Http\Methods\Generic;
use vendor\pagamento\pagseguro\Resources\Responsibility\Http\Methods\Request;
use vendor\pagamento\pagseguro\Resources\Responsibility\Http\Methods\Success;
use vendor\pagamento\pagseguro\Resources\Responsibility\Http\Methods\Unauthorized;
use vendor\pagamento\pagseguro\Resources\Responsibility\Configuration\Environment;
use vendor\pagamento\pagseguro\Resources\Responsibility\Configuration\Extensible;
use vendor\pagamento\pagseguro\Resources\Responsibility\Configuration\File;
use vendor\pagamento\pagseguro\Resources\Responsibility\Configuration\Wrapper;
use vendor\pagamento\pagseguro\Resources\Responsibility\Notifications\Application;
use vendor\pagamento\pagseguro\Resources\Responsibility\Notifications\PreApproval;
use vendor\pagamento\pagseguro\Resources\Responsibility\Notifications\Transaction;

/**
 * class Handler
 * @package vendor\pagamento\pagseguro\Services\Connection\Responsibility
 */
class Responsibility
{
    public static function http($http, $class)
    {
        $success = new Success();
        $request = new Request();
        $unauthorized = new Unauthorized();
        $generic = new Generic();

        $success->successor(
            $request->successor(
                $unauthorized->successor(
                    $generic
                )
            )
        );
        return $success->handler($http, $class);
    }

    public static function configuration()
    {
        $environment = new Environment;
        $extensible = new Extensible;
        $file = new File;
        $wrapper = new Wrapper;

        $wrapper->successor(
            $environment->successor(
                $file->successor(
                    $extensible
                )
            )
        );
        return $wrapper->handler(null, null);
    }

    public static function notifications()
    {
        $transaction = new Transaction();
        $preApproval = new PreApproval();
        $application = new Application();

        $transaction->successor(
            $preApproval->successor(
                $application
            )
        );

        return $transaction->handler();
    }
}
