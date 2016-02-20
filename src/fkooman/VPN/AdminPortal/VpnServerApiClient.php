<?php
/**
 * Copyright 2015 François Kooman <fkooman@tuxed.net>.
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
 */
namespace fkooman\VPN\AdminPortal;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Client;
use RuntimeException;

class VpnServerApiClient
{
    /** @var \GuzzleHttp\Client */
    private $client;

    /** @var string */
    private $vpnServerApiUri;

    public function __construct(Client $client, $vpnServerApiUri)
    {
        $this->client = $client;
        $this->vpnServerApiUri = $vpnServerApiUri;
    }

    public function getStatus()
    {
        $requestUri = sprintf('%s/status', $this->vpnServerApiUri);

        return $this->client->get($requestUri)->json();
    }

    public function getLoadStats()
    {
        $requestUri = sprintf('%s/load-stats', $this->vpnServerApiUri);

        return $this->client->get($requestUri)->json();
    }

    public function getVersion()
    {
        $requestUri = sprintf('%s/version', $this->vpnServerApiUri);

        return $this->client->get($requestUri)->json();
    }

    /**
     * Get the log for a particular date.
     *
     * @param string $showDate date in format YYYY-MM-DD
     */
    public function getLog($showDate)
    {
        $requestUri = sprintf('%s/log/history?showDate=%s', $this->vpnServerApiUri, $showDate);

        try {
            return $this->client->get($requestUri)->json();
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->json();
            throw new RuntimeException(
                $responseBody['error']
            );
        }
    }

    public function postCcdDisable($commonName)
    {
        $requestUri = sprintf('%s/ccd/disable', $this->vpnServerApiUri);

        return $this->client->post(
            $requestUri,
            array(
                'body' => array(
                    'common_name' => $commonName,
                ),
            )
        )->json();
    }

    public function deleteCcdDisable($commonName)
    {
        $requestUri = sprintf('%s/ccd/disable?common_name=%s', $this->vpnServerApiUri, $commonName);

        return $this->client->delete($requestUri)->json();
    }

    public function getCcdDisable($userId = null)
    {
        $requestUri = sprintf('%s/ccd/disable', $this->vpnServerApiUri);
        if (!is_null($userId)) {
            $requestUri = sprintf('%s/ccd/disable?user_id=%s', $this->vpnServerApiUri, $userId);
        }

        return $this->client->get($requestUri)->json();
    }

    public function getStaticAddresses($userId = null)
    {
        $requestUri = sprintf('%s/static/ip', $this->vpnServerApiUri);
        if (!is_null($userId)) {
            $requestUri = sprintf('%s/static/ip?user_id=%s', $this->vpnServerApiUri, $userId);
        }

        return $this->client->get($requestUri)->json();
    }

    public function getStaticAddress($commonName)
    {
        $requestUri = sprintf('%s/static/ip?common_name=%s', $this->vpnServerApiUri, $commonName);

        return $this->client->get($requestUri)->json();
    }

    public function setStaticAddresses($commonName, $v4, $v6)
    {
        $requestUri = sprintf('%s/static/ip', $this->vpnServerApiUri);

        $p = array(
            'common_name' => $commonName,
        );
        if (!is_null($v4) && !empty($v4)) {
            $p['v4'] = $v4;
        }

        return $this->client->post(
            $requestUri,
            array(
                'body' => $p,
            )
        )->json();
    }

    public function postKill($commonName)
    {
        $requestUri = sprintf('%s/kill', $this->vpnServerApiUri);

        return $this->client->post(
            $requestUri,
            array(
                'body' => array(
                    'common_name' => $commonName,
                ),
            )
        )->json();
    }

    public function postCrlFetch()
    {
        $requestUri = sprintf('%s/crl/fetch', $this->vpnServerApiUri);

        return $this->client->post($requestUri)->json();
    }
}
