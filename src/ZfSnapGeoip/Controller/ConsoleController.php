<?php

namespace ZfSnapGeoip\Controller;

use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Http\Response;
use ZfSnapGeoip\DatabaseConfig;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

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
            $events->trigger(__FUNCTION__ . '.exists', $this, array(
                'path' => $datFilePath,
            ));
            $this->writeline('Database already exist. Skipping...', Color::LIGHT_RED);
            return;
        }

        try {
            $response = $this->getDbResponse();
        } catch (\Zend\Http\Client\Exception\RuntimeException $e) {
            $this->writeLine(sprintf('%s', $e->getMessage()), Color::WHITE, Color::RED);
            return;
        }

        if (! $response instanceof Response || $response->getStatusCode() !== Response::STATUS_CODE_200) {
            $this->writeLine('Error during file download occured', Color::LIGHT_RED);
            return;
        }

        $events->trigger(__FUNCTION__ . '.pre', $this, array(
            'path' => $datFilePath,
            'response' => $response,
        ));

        $this->writeLine('Download completed', Color::LIGHT_GREEN);
        $this->writeLine('Unzip the downloading data...', Color::LIGHT_YELLOW);

        file_put_contents($datFilePath, gzdecode($response->getBody()));

        $events->trigger(__FUNCTION__ . '.post', $this, array(
            'path' => $datFilePath,
        ));

        $this->writeLine(sprintf('Unzip completed (%s)', $datFilePath), Color::LIGHT_GREEN);
    }

    /**
     * @return Response
     */
    public function getDbResponse()
    {
        $source = $this->config->getSource();

        $this->writeLine(sprintf('Downloading %s...', $source), Color::LIGHT_YELLOW);

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
