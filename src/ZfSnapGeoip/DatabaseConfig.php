<?php

/**
 * Config
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip;

class DatabaseConfig
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getSource()
    {
        return $this->data['source'];
    }

    public function getSourceBasename()
    {
        return basename($this->getSource());
    }

    public function getDestination()
    {
        return $this->data['destination'];
    }

    public function getFilename()
    {
        return $this->data['filename'];
    }

    public function getFlag()
    {
        return $this->data['flag'];
    }

    public function getRegionVarsPath()
    {
        return $this->data['regionvars'];
    }

    public function getDatabasePath()
    {
        return $this->getDestination() . $this->getFilename();
    }
}