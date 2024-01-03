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
 * @package   MicrosoftAzure\Storage\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Common\Models;

use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Holds elements of queue properties logging field.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class Logging
{
    private ?string $_version = null;
    private bool $_delete = false;
    private bool $_read = false;
    private bool $_write = false;
    private ?RetentionPolicy $_retentionPolicy = null;

    /**
     * Creates object from $parsedResponse.
     *
     * @internal
     * @param array $parsedResponse XML response parsed into array.
     *
     * @return Logging
     */
    public static function create(array $parsedResponse): Logging
    {
        $result = new self();
        $result->setVersion($parsedResponse['Version']);
        $result->setDelete(Utilities::toBoolean($parsedResponse['Delete']));
        $result->setRead(Utilities::toBoolean($parsedResponse['Read']));
        $result->setWrite(Utilities::toBoolean($parsedResponse['Write']));
        $result->setRetentionPolicy(
            RetentionPolicy::create($parsedResponse['RetentionPolicy'])
        );

        return $result;
    }

    /**
     * Gets the retention policy
     *
     * @return null|RetentionPolicy
     *
     */
    public function getRetentionPolicy(): ?RetentionPolicy
    {
        return $this->_retentionPolicy;
    }

    /**
     * Sets retention policy
     *
     * @param RetentionPolicy $policy object to use
     *
     * @return void
     */
    public function setRetentionPolicy(RetentionPolicy $policy): void
    {
        $this->_retentionPolicy = $policy;
    }

    /**
     * Gets whether all write requests should be logged.
     *
     * @return bool .
     */
    public function getWrite(): bool
    {
        return $this->_write;
    }

    /**
     * Sets whether all write requests should be logged.
     *
     * @param bool $write new value.
     *
     * @return void
     */
    public function setWrite(bool $write): void
    {
        $this->_write = $write;
    }

    /**
     * Gets whether all read requests should be logged.
     *
     * @return bool
     */
    public function getRead(): bool
    {
        return $this->_read;
    }

    /**
     * Sets whether all read requests should be logged.
     *
     * @param bool $read new value.
     *
     * @return void
     */
    public function setRead(bool $read): void
    {
        $this->_read = $read;
    }

    /**
     * Gets whether all delete requests should be logged.
     *
     * @return bool
     */
    public function getDelete(): bool
    {
        return $this->_delete;
    }

    /**
     * Sets whether all delete requests should be logged.
     *
     * @param bool $delete new value.
     *
     * @return void
     */
    public function setDelete(bool $delete): void
    {
        $this->_delete = $delete;
    }

    /**
     * Gets the version of Storage Analytics to configure
     *
     * @return null|string
     */
    public function getVersion(): ?string
    {
        return $this->_version;
    }

    /**
     * Sets the version of Storage Analytics to configure
     *
     * @param string $version new value.
     *
     * @return void
     */
    public function setVersion(string $version): void
    {
        $this->_version = $version;
    }

    /**
     * Converts this object to array with XML tags
     *
     * @internal
     * @return array
     */
    public function toArray(): array
    {
        return array(
            'Version'         => $this->_version,
            'Delete'          => Utilities::booleanToString($this->_delete),
            'Read'            => Utilities::booleanToString($this->_read),
            'Write'           => Utilities::booleanToString($this->_write),
            'RetentionPolicy' => !empty($this->_retentionPolicy)
                ? $this->_retentionPolicy->toArray()
                : null
        );
    }
}
