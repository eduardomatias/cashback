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

namespace vendor\pagamento\pagseguro\Services\Transactions\Search;

use vendor\pagamento\pagseguro\Domains\Account\Credentials;
use vendor\pagamento\pagseguro\Parsers\Transaction\Search\Code\Request;
use vendor\pagamento\pagseguro\Resources\Connection;
use vendor\pagamento\pagseguro\Resources\Http;
use vendor\pagamento\pagseguro\Resources\Log\Logger;
use vendor\pagamento\pagseguro\Resources\Responsibility;

/**
 * Class Payment
 * @package vendor\pagamento\pagseguro\Services\Checkout
 */
class Code
{

    /**
     * @param \vendor\pagamento\pagseguro\Domains\Account\Credentials $credentials
     * @param $code
     * @return string
     * @throws \Exception
     */
    public static function search(Credentials $credentials, $code)
    {

        Logger::info("Begin", ['service' => 'Transactions.Search.Code']);

        try {
            $connection = new Connection\Data($credentials);
            $http = new Http();
            Logger::info(
                sprintf(
                    "GET: %s",
                    self::request($connection, $code)
                ),
                ['service' => 'Transactions.Search.Code']
            );
            $http->get(
                self::request($connection, $code)
            );

            $response = Responsibility::http(
                $http,
                new Request
            );

            Logger::info(
                sprintf(
                    "Date: %s, Code: %s",
                    $response->getDate(),
                    $response->getCode()
                ),
                ['service' => 'Transactions.Search.Code']
            );
            return $response;
        } catch (\Exception $exception) {
            Logger::error($exception->getMessage(), ['service' => 'Transactions.Search.Code']);
            throw $exception;
        }
    }

    /**
     * @param Connection\Data $connection
     * @return string
     */
    private static function request(Connection\Data $connection, $code)
    {
        return sprintf(
            "%s/%s/?%s",
            $connection->buildTransactionSearchRequestUrl(),
            $code,
            $connection->buildCredentialsQuery()
        );
    }
}
