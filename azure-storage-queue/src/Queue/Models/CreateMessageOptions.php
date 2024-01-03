<?php

/**
 * LICENSE: The MIT License (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * https://github.com/azure/azure-storage-php/LICENSE
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Queue\Models;

use MicrosoftAzure\Storage\Common\Internal\Validate;

/**
 * Holds optional parameters for createMessage wrapper.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class CreateMessageOptions extends QueueServiceOptions
{
    private ?int $_visibilityTimeoutInSeconds = null;
    private ?int $_timeToLiveInSeconds = null;

    /**
     * Gets visibilityTimeoutInSeconds field.
     *
     * @return integer
     */
    public function getVisibilityTimeoutInSeconds(): ?int
    {
        return $this->_visibilityTimeoutInSeconds;
    }

    /**
     * Sets visibilityTimeoutInSeconds field.
     *
     * @param int|null $visibilityTimeoutInSeconds value to use.
     *
     * @return void
     */
    public function setVisibilityTimeoutInSeconds(?int $visibilityTimeoutInSeconds)
    {
        $this->_visibilityTimeoutInSeconds = $visibilityTimeoutInSeconds;
    }

    /**
     * Gets timeToLiveInSeconds field.
     *
     * @return integer
     */
    public function getTimeToLiveInSeconds()
    {
        return $this->_timeToLiveInSeconds;
    }

    /**
     * Sets timeToLiveInSeconds field.
     *
     * @param int|null $timeToLiveInSeconds value to use.
     *
     * @return void
     */
    public function setTimeToLiveInSeconds(?int $timeToLiveInSeconds)
    {
        $this->_timeToLiveInSeconds = $timeToLiveInSeconds;
    }
}
