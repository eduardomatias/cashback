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

namespace vendor\pagamento\pagseguro\Resources\Factory;

use vendor\pagamento\pagseguro\Enum\Properties\Current;

/**
 * Class Shipping
 * @package vendor\pagamento\pagseguro\Resources\Factory\Request
 */
class Item
{
    /**
     * @var array
     */
    private $item;

    /**
     * Item constructor.
     */
    public function __construct()
    {
        $this->item = [];
    }

    /**
     * @param \vendor\pagamento\pagseguro\Domains\Item $item
     * @return \vendor\pagamento\pagseguro\Domains\Item
     */
    public function instance(\vendor\pagamento\pagseguro\Domains\Item $item)
    {
        return $item;
    }

    /**
     * @param $array
     * @return array
     */
    public function withArray($array)
    {
        $properties = new Current;

        $item = new \vendor\pagamento\pagseguro\Domains\Item();
        $item->setId($array[$properties::ITEM_ID])
             ->setAmount($array[$properties::ITEM_AMOUNT])
             ->setDescription($array[$properties::ITEM_DESCRIPTION])
             ->setQuantity($array[$properties::ITEM_DESCRIPTION])
             ->setWeight($array[$properties::ITEM_WEIGHT])
             ->setShippingCost($array[$properties::ITEM_SHIPPING_COST]);

        array_push($this->item, $item);
        return $this->item;
    }

    /**
     * @param $id
     * @param $description
     * @param $quantity
     * @param $amount
     * @param null $weight
     * @param null $shippingCost
     * @return array
     */
    public function withParameters(
        $id,
        $description,
        $quantity,
        $amount,
        $weight = null,
        $shippingCost = null
    ) {
        $item = new \vendor\pagamento\pagseguro\Domains\Item();
        $item->setId($id)
            ->setAmount($amount)
            ->setDescription($description)
            ->setQuantity($quantity)
            ->setWeight($weight)
            ->setShippingCost($shippingCost);
        array_push($this->item, $item);
        return $this->item;
    }
}
