<?php
/**
 * Copyright © Gigadesign. All rights reserved.
 */
declare(strict_types=1);

namespace Gigadesign\GoogleClient\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class GoogleClientManager
 *
 * @author Mark van der Werf <info@gigadesign.nl>
 */
class GoogleClientManager
{

    /**
     * @var \Google_Client
     */
    private $client;

    /**
     * @var array
     */
    private $accessToken;

    /**
     * @var array
     */
    private $scopes;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * GoogleClientManager constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface $configWriter
     * @param StoreManagerInterface $storeManager
     * @param array $scopes
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        StoreManagerInterface $storeManager,
        array $scopes = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->storeManager = $storeManager;
        $this->scopes = array_unique($scopes);
    }

    /**
     * @return string|null
     */
    public function getAuthUrl(): ?string
    {
        if ($this->getClient())
        {
            return $this->getClient()->createAuthUrl();
        }

        return null;
    }

    public function setAuthCode(string $authCode): self
    {
        if ($this->getClient())
        {
            $accessToken = $this->getClient()->fetchAccessTokenWithAuthCode($authCode);
            $this->getClient()->setAccessToken($accessToken);

            $this->setAccessToken($accessToken);
        }

        return $this;
    }

    public function setDeveloperKey(string $developerKey): self
    {
        if ($this->getClient())
        {
            $this->getClient()->setDeveloperKey($developerKey);
        }

        return $this;
    }

    public function getService(string $serviceName): ?\Google_Service
    {
        if ($this->getClient())
        {
            $class = '\Google_Service_' . $serviceName;

            try
            {
                return new $class($this->getClient());
            }
            catch (\Exception $e)
            {
                return null;
            }
        }

        return null;
    }

    /**
     * @return \Google_Client|null
     */
    private function getClient(): ?\Google_Client
    {
        if (!$this->client)
        {
            $this->client = new \Google_Client();
            $this->client->setApplicationName('Google Calendar for Magento 2');
            $this->client->setScopes($this->scopes);
            $this->client->setClientId($this->scopeConfig->getValue('google_client/api/client_id'));
            $this->client->setClientSecret($this->scopeConfig->getValue('google_client/api/client_secret'));
            $this->client->setRedirectUri($this->storeManager->getDefaultStoreView()->getBaseUrl() . 'googleclient/connect/index');
            $this->client->setAccessType('offline');
            $this->client->setPrompt('select_account consent');

            if ($this->getAccessToken())
            {
                $this->client->setAccessToken($this->getAccessToken());
            }

            if ($this->client->isAccessTokenExpired() && $this->client->getRefreshToken())
            {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            }
        }

        return $this->client;
    }

    /**
     * @param array $accessToken
     * @return bool
     */
    private function setAccessToken(array $accessToken): bool
    {
        $this->accessToken = $accessToken;

        $this->configWriter->save('google_client/api/access_token', json_encode($accessToken));

        $this->scopeConfig->clean();

        return true;
    }

    /**
     * @return array|null
     */
    private function getAccessToken(): ?array
    {
        if (!$this->accessToken)
        {
            $accessToken = $this->scopeConfig->getValue('google_client/api/access_token');

            if ($accessToken)
            {
                $accessTokenArray = json_decode($accessToken, true);

                if (is_array($accessTokenArray))
                {
                    $this->accessToken = $accessTokenArray;
                }
            }
        }

        return $this->accessToken;
    }
}
