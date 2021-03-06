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

namespace vendor\pagamento\pagseguro\Services\Application;

use vendor\pagamento\pagseguro\Domains\Account\Credentials;
use vendor\pagamento\pagseguro\Resources\Connection;
use vendor\pagamento\pagseguro\Resources\Http;
use vendor\pagamento\pagseguro\Resources\Log\Logger;
use vendor\pagamento\pagseguro\Resources\Responsibility;

/**
 * Class Notifications
 * @package vendor\pagamento\pagseguro\Services\Transactions
 */
class Notification
{

    /**
     * @param Credentials $credentials
     * @return mixed
     * @throws \Exception
     */
    public static function check(Credentials $credentials)
    {
        Logger::info("Begin", ['service' => 'Application.Notification']);
        try {
            $connection = new Connection\Data($credentials);
            $http = new Http();
            Logger::info(sprintf("GET: %s", self::request($connection)), ['service' => 'Application.Notification']);
            $http->get(
                self::request($connection)
            );

            $response = Responsibility::http(
                $http,
                new \vendor\pagamento\pagseguro\Parsers\Authorization\Notification\Request
            );
            Logger::info(
                sprintf(
                    "Date: %s, Code: %s",
                    $response->getDate(),
                    $response->getCode()
                ),
                ['service' => 'Application.Notification']
            );
            return $response;
        } catch (\Exception $exception) {
            Logger::error($exception->getMessage(), ['service' => 'Application.Notification']);
            throw $exception;
        }
    }

    /**
     * @param Connection\Data $connection
     * @return string
     */
    private static function request(Connection\Data $connection)
    {
        return $connection->buildNotificationAuthorizationRequestUrl()."/".
               Responsibility::notifications()."?".$connection->buildCredentialsQuery();
    }
}
