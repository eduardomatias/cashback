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

namespace vendor\pagamento\pagseguro\Parsers\Response;

use vendor\pagamento\pagseguro\Domains\Document;
use vendor\pagamento\pagseguro\Domains\Phone;

/**
 * Class Sender
 * @package vendor\pagamento\pagseguro\Parsers\Response
 */
trait Sender
{
    /**
     * @var
     */
    private $sender;
    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param $sender
     * @return $this
     */
    public function setSender($sender)
    {
        $phone = new Phone();
        $phone->setAreaCode(current($sender->phone->areaCode))
              ->setNumber(current($sender->phone->number));

        $senderClass = new \vendor\pagamento\pagseguro\Domains\Sender();
        $this->sender = $senderClass->setName(current($sender->name))
            ->setEmail(current($sender->email))
            ->setPhone($phone)
            ->setDocuments(new Document());

        return $this;
    }
}
