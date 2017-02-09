<?php

namespace ZfSnapGeoip\Controller;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use Zend\Console\Request as ConsoleRequest;
use Zend\Http\Client;
use Zend\Http\Client\Exception\RuntimeException;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use ZfSnapGeoip\DatabaseConfig;

/**
 * Console Controller
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class ConsoleController extends AbstractActionController
{
    /**
     * @var Console
     */
    protected $console;

    /**
     * Is quiet mode enabled?
     *
     * @var bool
     */
    protected $isQuiet;

    /**
     * @var DatabaseConfig
     */
    protected $config;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @param Console $console
     * @param DatabaseConfig $config
     * @param Client $httpClient
     */
    public function __construct(Console $console, DatabaseConfig $config, Client $httpClient)
    {
        $this->console = $console;
        $this->config = $config;
        $this->setHttpClient($httpClient);
    }

    /**
     * @param Client $httpClient
     */
    public function setHttpClient(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        if (!($request instanceof ConsoleRequest)) {
            throw new \RuntimeException('You can use this controller only from a console!');
        }
        return parent::dispatch($request, $response);
    }

    /**
     * Download GeoIP data via console
     */
    public function downloadAction()
    {
        $datFilePath = $this->config->getDatabasePath();
        $events = $this->getEventManager();

        if ($this->getRequest()->getParam('no-clobber') && is_file($datFilePath)) {
            $events->trigger(__FUNCTION__ . '.exists', $this, [
                'path' => $datFilePath,
            ]);
            $this->writeLine('Database already exist. Skipping...');
            return;
        }

        try {
            $response = $this->getDbResponse();
        } catch (RuntimeException $e) {
            $this->writeLineError(sprintf('%s', $e->getMessage()));
            return;
        }

        if (!$response instanceof Response || $response->getStatusCode() !== Response::STATUS_CODE_200) {
            $this->writeLineError('Error during file download occured');
            return;
        }

        $events->trigger(__FUNCTION__ . '.pre', $this, [
            'path' => $datFilePath,
            'response' => $response,
        ]);

        $this->writeLineSuccess('Download completed');
        $this->writeLine('Unzip the downloading data...');

        file_put_contents($datFilePath, gzdecode($response->getBody()));

        $events->trigger(__FUNCTION__ . '.post', $this, [
            'path' => $datFilePath,
        ]);

        $this->writeLineSuccess(sprintf('Unzip completed (%s)', $datFilePath));
    }

    /**
     * @return Response
     */
    public function getDbResponse()
    {
        $source = $this->config->getSource();

        $this->writeLine(sprintf('Downloading %s...', $source));

        $this->httpClient->setUri($source);
        $this->httpClient->setMethod(Request::METHOD_GET);

        return $this->httpClient->send();
    }

    /**
     * @param string $text
     * @param int $color
     * @param int $bgColor
     */
    public function writeLine($text, $color = null, $bgColor = null)
    {
        if (!$this->isQuietMode()) {
            $this->getConsole()->writeLine($text, $color, $bgColor);
        }
    }

    /**
     * @param string $text
     */
    public function writeLineError($text)
    {
        $this->writeLine($text, Color::WHITE, Color::RED);
    }

    /**
     * @param string $text
     */
    public function writeLineSuccess($text)
    {
        $this->writeLine($text, Color::LIGHT_GREEN);
    }

    /**
     * @return Console
     */
    public function getConsole()
    {
        return $this->console;
    }

    /**
     * @return bool
     */
    public function isQuietMode()
    {
        if ($this->isQuiet === null) {
            $this->isQuiet = (bool) $this->getRequest()->getParam('q', false);
        }
        return $this->isQuiet;
    }
}
