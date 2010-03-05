<?php
/**
 * wsgen application
 *
 * This file is part of wsgen.
 *
 * wsgen is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * wsgen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with wsgen; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
namespace org\westhoffswelt\wsgen;

/**
 * WSGen Application runner
 * 
 * This class handles the interaction of all the different application
 * components to produce the expected behaviour. It provides a lot of different
 * configuration flags to adjust all kinds of aspects of the final result.
 */
class Application 
{
    /**
     * Logger instance to use for the application run 
     * 
     * @var Logger
     */
    protected $logger;

    /**
     * LayoutManager to use for creation.
     * 
     * @var LayoutManager
     */
    protected $layout;

    /**
     * Definition to use 
     * 
     * @var DefinitionReader
     */
    protected $reader;

    /**
     * Definition Writer to use 
     * 
     * @var DefinitionWriter
     */
    protected $writer;

    /**
     * Renderer to use. 
     * 
     * @var Renderer
     */
    protected $renderer;

    /**
     * Construct with all the needed components. 
     * 
     * @param Logger $logger 
     * @param DefinitionReader $reader 
     * @param LayoutManager $layout 
     * @param DefinitionWriter $writer 
     */
    public function __construct( Logger $logger, DefinitionReader $reader, LayoutManager $layout, DefinitionWriter $writer ) 
    {
        $this->logger = $logger;
        $this->reader = $reader;
        $this->layout = $layout;
        $this->writer = $writer;
    }

    /**
     * Perform a full application run 
     * 
     * @return void
     */
    public function run() 
    {
        try 
        {
            $imageIdentifierMap = $this->reader->getMappingTable();
            
            $this->layout->init( count( $imageIdentifierMap ) );
            foreach( $imageIdentifierMap as $image => $identifier ) 
            {
                $this->layout->layoutImage( $image );
            }
            $imageLayoutMap = $this->layout->finish();
            
            $this->writer->writeDefinition( $imageIdentifierMap, $imageLayoutMap );
        }
        catch( \RuntimeException $e ) 
        {
            $this->logger->log( E_ERROR, $e->getMessage() );
        }
    }
}
