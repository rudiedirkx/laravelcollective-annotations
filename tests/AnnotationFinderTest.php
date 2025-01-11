<?php

use Collective\Annotations\AnnotationFinder;
use Mockery as m;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AnnotationFinderTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var m\MockInterface|Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    public function setUp(): void
    {
        $this->app = m::mock('Illuminate\Contracts\Foundation\Application');
    }

    /**
     * @covers \Collective\Annotations\AnnotationFinder::routesAreScanned
     * @covers \Collective\Annotations\AnnotationFinder::getScannedRoutesPath
     * @covers \Collective\Annotations\AnnotationFinder::eventsAreScanned
     * @covers \Collective\Annotations\AnnotationFinder::getScannedEventsPath
     * @covers \Collective\Annotations\AnnotationFinder::modelsAreScanned
     * @covers \Collective\Annotations\AnnotationFinder::getScannedModelsPath
     */
    #[DataProvider("scannedChecksDataProvider")]
    public function testRoutesAreScannedReturnsTrue($filepath, $found, $method, $expected)
    {
        $files = m::mock('Illuminate\Filesystem\Filesystem');
        $files->shouldReceive('exists')
            ->with($filepath)->once()
            ->andReturn($found);

        $this->app->shouldReceive('make')
            ->with('files')->once()
            ->andReturn($files);

        $this->app->shouldReceive('make')
            ->with('path.storage')->once()
            ->andReturn('storage');

        $this->assertEquals($expected, (new AnnotationFinder($this->app))->$method());
    }

    static public function scannedChecksDataProvider()
    {
        return [
            'routesScanned' => [
                'storage/framework/routes.scanned.php',
                true,
                'routesAreScanned',
                true,
            ],
            'routesNotScanned' => [
                'storage/framework/routes.scanned.php',
                false,
                'routesAreScanned',
                false,
            ],
            'eventsScanned' => [
                'storage/framework/events.scanned.php',
                true,
                'eventsAreScanned',
                true,
            ],
            'eventsNotScanned' => [
                'storage/framework/events.scanned.php',
                false,
                'eventsAreScanned',
                false,
            ],
            'modelsScanned' => [
                'storage/framework/models.scanned.php',
                true,
                'modelsAreScanned',
                true,
            ],
            'modelsNotScanned' => [
                'storage/framework/models.scanned.php',
                false,
                'modelsAreScanned',
                false,
            ],
        ];
    }
}
