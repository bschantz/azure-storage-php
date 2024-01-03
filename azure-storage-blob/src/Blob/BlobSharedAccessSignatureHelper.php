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
 * @package   MicrosoftAzure\Storage\Blob
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Blob;

use InvalidArgumentException;
use MicrosoftAzure\Storage\Blob\Internal\BlobResources as Resources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper;

/**
 * Provides methods to generate Azure Storage Shared Access Signature
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Blob
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class BlobSharedAccessSignatureHelper extends SharedAccessSignatureHelper
{
    /**
     * Constructor.
     *
     * @param string $accountName the name of the storage account.
     * @param string $accountKey the shared key of the storage account
     *
     */
    public function __construct($accountName, $accountKey)
    {
        parent::__construct($accountName, $accountKey);
    }

    /**
     * Generates Blob service shared access signature.
     *
     * This only supports version 2015-04-05 and later.
     *
     * @param  string            $signedResource      Resource name to generate the
     *                                                canonicalized resource.
     *                                                It can be Resources::RESOURCE_TYPE_BLOB
     *                                                or Resources::RESOURCE_TYPE_CONTAINER.
     * @param  string            $resourceName        The name of the resource, including
     *                                                the path of the resource. It should be
     *                                                - {container}/{blob}: for blobs,
     *                                                - {container}: for containers, e.g.:
     *                                                mymusic/music.mp3 or
     *                                                music.mp3
     * @param  string            $signedPermissions   Signed permissions.
     * @param  \Datetime|string  $signedExpiry        Signed expiry date.
     * @param  \Datetime|string  $signedStart         Signed start date.
     * @param  string            $signedIP            Signed IP address.
     * @param  string            $signedProtocol      Signed protocol.
     * @param  string            $signedIdentifier    Signed identifier.
     * @param  string            $cacheControl        Cache-Control header (rscc).
     * @param  string            $contentDisposition  Content-Disposition header (rscd).
     * @param  string            $contentEncoding     Content-Encoding header (rsce).
     * @param  string            $contentLanguage     Content-Language header (rscl).
     * @param  string            $contentType         Content-Type header (rsct).
     *
     * @see Constructing an service SAS at
     * https://docs.microsoft.com/en-us/rest/api/storageservices/constructing-a-service-sas
     * @return string
     */
    public function generateBlobServiceSharedAccessSignatureToken(
        $signedResource,
        $resourceName,
        $signedPermissions,
        $signedExpiry,
        $signedStart = "",
        $signedIP = "",
        $signedProtocol = "",
        $signedIdentifier = "",
        $signedSnapshotTime = "",
        $signedEncryptionScope = "",
        $cacheControl = "",
        $contentDisposition = "",
        $contentEncoding = "",
        $contentLanguage = "",
        $contentType = ""
    )
    {
        // check that the resource name is valid.
        Validate::canCastAsString($signedResource, 'signedResource');
        Validate::notNullOrEmpty($signedResource, 'signedResource');
        Validate::isTrue(
            $signedResource === Resources::RESOURCE_TYPE_BLOB ||
            $signedResource === Resources::RESOURCE_TYPE_CONTAINER ||
            $signedResource === Resources::RESOURCE_TYPE_DIRECTORY,
            \sprintf(
                Resources::INVALID_VALUE_MSG,
                '$signedResource',
                'Can only be \'b\', \'c\', or \'d\'.'
            )
        );

        // check that the resource name is valid.
        Validate::notNullOrEmpty($resourceName, 'resourceName');
        Validate::canCastAsString($resourceName, 'resourceName');

        // validate and sanitize signed permissions
        $signedPermissions = $this->validateAndSanitizeStringWithArray(
            strtolower($signedPermissions),
            Resources::ACCESS_PERMISSIONS[$signedResource]
        );

        // check that expiry is valid
        if ($signedExpiry instanceof \Datetime) {
            $signedExpiry = Utilities::isoDate($signedExpiry);
        }
        Validate::notNullOrEmpty($signedExpiry, 'signedExpiry');
        Validate::canCastAsString($signedExpiry, 'signedExpiry');
        Validate::isDateString($signedExpiry, 'signedExpiry');

        // check that signed start is valid
        if ($signedStart instanceof \Datetime) {
            $signedStart = Utilities::isoDate($signedStart);
        }
        Validate::canCastAsString($signedStart, 'signedStart');
        if (strlen($signedStart) > 0) {
            Validate::isDateString($signedStart, 'signedStart');
        }

        // check that signed IP is valid
        Validate::canCastAsString($signedIP, 'signedIP');

        // validate and sanitize signed protocol
        $signedProtocol = $this->validateAndSanitizeSignedProtocol($signedProtocol);

        // check that signed identifier is valid
        Validate::canCastAsString($signedIdentifier, 'signedIdentifier');
        Validate::isTrue(
            strlen($signedIdentifier) <= 64,
            sprintf(Resources::INVALID_STRING_LENGTH, 'signedIdentifier', 'maximum 64')
        );

        Validate::canCastAsString($cacheControl, 'cacheControl');
        Validate::canCastAsString($contentDisposition, 'contentDisposition');
        Validate::canCastAsString($contentEncoding, 'contentEncoding');
        Validate::canCastAsString($contentLanguage, 'contentLanguage');
        Validate::canCastAsString($contentType, 'contentType');

        // construct an array for the values to be in the token query string
        $sasParams = [];
        $sasParams['sp'] = $signedPermissions;
        $sasParams['sv'] = Resources::STORAGE_API_LATEST_VERSION;
        $sasParams['st'] = $signedStart;
        $sasParams['se'] = $signedExpiry;
        $sasParams['sr'] = $signedResource;
        if (!empty($signedIdentifier)) {
            $sasParams['si'] = $signedIdentifier;
        }
        if (!empty($signedIP)) {
            $sasParams['sip'] = $signedIP;
        }
        if (!empty($signedProtocol)) {
            $sasParams['spr'] = $signedProtocol;
        }
        if (!empty($signedSnapshotTime)) {
            $sasParams['sst'] = $signedSnapshotTime;
        }
        if (!empty($signedEncryptionScope)) {
            $sasParams['ses'] = $signedEncryptionScope;
        }
        if (!empty($cacheControl)) {
            $sasParams['rscc'] = $cacheControl;
        }
        if (!empty($contentDisposition)) {
            $sasParams['rscd'] = $contentDisposition;
        }
        if (!empty($contentEncoding)) {
            $sasParams['rsce'] = $contentEncoding;
        }
        if (!empty($contentLanguage)) {
            $sasParams['rdcl'] = $contentLanguage;
        }
        if (!empty($contentType)) {
            $sasParams['rdct'] = $contentType;
        }

        // construct an array with the parameters to generate the shared access signature at the account level
        $arrayToSign = [];
        $arrayToSign[] = $sasParams['sp'];
        $arrayToSign[] = $sasParams['st'];
        $arrayToSign[] = $sasParams['se'];
        $arrayToSign[] = static::generateCanonicalResource(
            $this->accountName,
            Resources::RESOURCE_TYPE_BLOB,
            $resourceName
        );
        $arrayToSign[] = $sasParams['si'];
        $arrayToSign[] = $sasParams['sip'];
        $arrayToSign[] = $sasParams['spr'];
        $arrayToSign[] = $sasParams['sv'];
        $arrayToSign[] = $sasParams['sr'];
        $arrayToSign[] = $sasParams['sst'];
        $arrayToSign[] = $sasParams['ses'];
        $arrayToSign[] = $sasParams['rscc'];
        $arrayToSign[] = $sasParams['rscd'];
        $arrayToSign[] = $sasParams['rsce'];
        $arrayToSign[] = $sasParams['rdcl'];
        $arrayToSign[] = $sasParams['rdct'];

        // implode the parameters into a string
        $stringToSign = implode("\n", $arrayToSign);

        // decode the account key from base64
        $decodedAccountKey = base64_decode($this->accountKey);
        // create the signature with hmac sha256
        $signature = hash_hmac("sha256", $stringToSign, $decodedAccountKey, true);
        // encode the signature as base64
        $sasParams['sig'] = base64_encode($signature);

        return http_build_query($sasParams);
    }
}
