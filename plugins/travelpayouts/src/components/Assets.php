<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;

use Exception;
use Travelpayouts;
use Travelpayouts\components\assets\AssetsLoader;
use Travelpayouts\components\assets\WebpackAssets;

/**
 * Class Assets
 * @package Travelpayouts\components
 * @property-read AssetsLoader $loader
 */
class Assets extends WebpackAssets
{
    /**
     * @var AssetsLoader
     */
    protected $_loader;

    protected $path;
    protected $chunkNameVariableName = 'assets_chunks';
    /**
     * @var mixed|null
     */
    protected $prefix = 'assets_prefix';

    /**
     * @throws Exception
     */
    public function init()
    {
        if (!$this->path) {
            throw new Exception(Travelpayouts::__('{attribute} cannot be blank.', ['attribute' => 'Assets->path']));
        }
    }

    /**
     * @return AssetsLoader
     */
    public function getLoader()
    {
        if (!$this->_loader) {
            $this->_loader = new AssetsLoader([
                'assets' => $this,
                'loadableChunkName' => $this->chunkNameVariableName,
            ]);
        }
        return $this->_loader;
    }

    protected function assetsPath()
    {
        return $this->path;
    }

    public function prefix()
    {
        return $this->prefix;
    }
}
