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

namespace vendor\pagseguro\Parsers\PreApproval\Search\Date;

use vendor\pagseguro\Parsers\Error;
use vendor\pagseguro\Parsers\Parser;
use vendor\pagseguro\Parsers\PreApproval\Search\Result;
use vendor\pagseguro\Resources\Http;

/**
 * Class Payment
 * @package vendor\pagseguro\Parsers\Checkout
 */
class Request extends Error implements Parser
{

    /**
     * @param \vendor\pagseguro\Resources\Http $http
     * @return Response
     */
    public static function success(Http $http)
    {
        $xml = simplexml_load_string($http->getResponse());
        $response = new Result();
        $response->setDate(current($xml->date))
            ->setPreApprovals(current($xml->preApprovals))
            ->setResultsInThisPage(current($xml->resultsInThisPage))
            ->setCurrentPage(current($xml->currentPage))
            ->setTotalPages(current($xml->totalPages));
        return $response;
    }

    /**
     * @param \vendor\pagseguro\Resources\Http $http
     * @return \vendor\pagseguro\Domains\Error
     */
    public static function error(Http $http)
    {
        $error = parent::error($http);
        return $error;
    }
}
