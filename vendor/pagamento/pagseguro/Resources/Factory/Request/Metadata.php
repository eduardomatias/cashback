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

namespace vendor\pagamento\pagseguro\Resources\Factory\Request;

use vendor\pagamento\pagseguro\Enum\Properties\Current;

/**
 * Class Metadata
 * @package vendor\pagamento\pagseguro\Resources\Factory\Request
 */
class Metadata
{
    private $metadata;

    public function __construct()
    {
        $this->metadata = [];
    }

    public function instance(\vendor\pagamento\pagseguro\Domains\Metadata $metadata)
    {
        return $metadata;
    }

    public function withArray($array)
    {
        $properties = new Current;

        $metadata = new \vendor\pagamento\pagseguro\Domains\Metadata();
        $metadata->setKey($array[$properties::METADATA_ITEM_KEY])
             ->setValue($array[$properties::METADATA_ITEM_VALUE])
             ->setGroup($array[$properties::METADATA_ITEM_GROUP]);

        array_push($this->metadata, $metadata);
        return $this->metadata;
    }

    public function withParameters(
        $key,
        $value,
        $group = null
    ) {
        $metadata = new \vendor\pagamento\pagseguro\Domains\Metadata();
        $metadata->setKey($key)
            ->setValue($value)
            ->setGroup($group);
        array_push($this->metadata, $metadata);
        return $this->metadata;
    }
}
