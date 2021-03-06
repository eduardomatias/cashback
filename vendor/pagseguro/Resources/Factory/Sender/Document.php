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

namespace vendor\pagseguro\Resources\Factory\Sender;

use vendor\pagseguro\Enum\Properties\Current;

/**
 * Class Document
 * @package vendor\pagseguro\Resources\Factory
 */
class Document
{

    /**
     * @var \vendor\pagseguro\Domains\Document
     */
    private $sender;

    /**
     * Document constructor.
     */
    public function __construct($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @param \vendor\pagseguro\Domains\Document $document
     * @return \vendor\pagseguro\Domains\Document
     */
    public function instance(\vendor\pagseguro\Domains\Document $document)
    {
        $this->sender->setDocuments($document);
        return $this->sender;
    }

    /**
     * @param $array
     * @return \vendor\pagseguro\Domains\Document|Document
     */
    public function withArray($array)
    {
        $properties = new Current;
        $document = new \vendor\pagseguro\Domains\Document();
        $document->setType($array[$properties::DOCUMENT_TYPE])
                 ->setIdentifier($array[$properties::DOCUMENT_VALUE]);
        $this->sender->setDocuments($document);
        return $this->sender;
    }

    /**
     * @param $type
     * @param $identifier
     * @return \vendor\pagseguro\Domains\Document
     */
    public function withParameters($type, $identifier)
    {
        $document = new \vendor\pagseguro\Domains\Document();
        $document->setType($type)
                 ->setIdentifier($identifier);
        $this->sender->setDocuments($document);
        return $this->sender;
    }
}
