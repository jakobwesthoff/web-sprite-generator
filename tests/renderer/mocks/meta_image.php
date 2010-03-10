<?php
namespace org\westhoffswelt\wsgen\tests\Renderer\mocks;
use org\westhoffswelt\wsgen;

class MetaImage extends wsgen\MetaImage
{
    public $resource; 
    public $resolution;

    public function __construct( $filename, $resolution ) 
    {
        parent::__construct( $filename );

        $this->resource = imagecreatefrompng( $filename );
        $this->resolution = $resolution;
    }

    public function getResolution() 
    {
        return $this->resolution;
    }

    public function getResource() 
    {
        return $this->resource;
    }
}
