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

namespace vendor\pagseguro\Domains\Requests\PreApproval\Charge;

use vendor\pagseguro\Domains\Requests\Currency;
use vendor\pagseguro\Domains\Requests\Item;
use vendor\pagseguro\Domains\Requests\Metadata;
use vendor\pagseguro\Domains\Requests\Notification;
use vendor\pagseguro\Domains\Requests\Parameter;
use vendor\pagseguro\Domains\Requests\PaymentMethod;
use vendor\pagseguro\Domains\Requests\Requests;
use vendor\pagseguro\Domains\Requests\Shipping;
use vendor\pagseguro\Domains\Requests\Reference;
use vendor\pagseguro\Domains\Requests\Redirect;

/**
 * Class Request
 * @package vendor\pagseguro\Domains\Requests
 */
class Request implements Requests
{
    use Reference;
    use Item;
}
