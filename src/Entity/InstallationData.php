<?php

namespace App\Entity;

class InstallationData extends AbstractStruct
{
    protected string $databaseName;
    protected string $databaseUsername;
    protected string $databasePassword;
    protected string $databaseHost;
    protected string $databasePort;
    protected string $appSecret;

    /**
     * @return string
     */
    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    /**
     * @param string $databaseName
     */
    public function setDatabaseName(string $databaseName): void
    {
        $this->databaseName = $databaseName;
    }

    /**
     * @return string
     */
    public function getDatabaseUsername(): string
    {
        return $this->databaseUsername;
    }

    /**
     * @param string $databaseUsername
     */
    public function setDatabaseUsername(string $databaseUsername): void
    {
        $this->databaseUsername = $databaseUsername;
    }

    /**
     * @return string
     */
    public function getDatabasePassword(): string
    {
        return $this->databasePassword;
    }

    /**
     * @param string $databasePassword
     */
    public function setDatabasePassword(string $databasePassword): void
    {
        $this->databasePassword = $databasePassword;
    }

    /**
     * @return string
     */
    public function getDatabaseHost(): string
    {
        return $this->databaseHost;
    }

    /**
     * @param string $databaseHost
     */
    public function setDatabaseHost(string $databaseHost): void
    {
        $this->databaseHost = $databaseHost;
    }

    /**
     * @return string
     */
    public function getDatabasePort(): string
    {
        return $this->databasePort;
    }

    /**
     * @param string $databasePort
     */
    public function setDatabasePort(string $databasePort): void
    {
        $this->databasePort = $databasePort;
    }

    /**
     * @return string
     */
    public function getAppSecret(): string
    {
        return $this->appSecret;
    }

    /**
     * @param string $appSecret
     */
    public function setAppSecret(string $appSecret): void
    {
        $this->appSecret = $appSecret;
    }
}
