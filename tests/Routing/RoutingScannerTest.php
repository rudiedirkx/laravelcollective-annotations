<?php

use Collective\Annotations\Routing\Annotations\AnnotationStrategy;
use Collective\Annotations\Routing\Attributes\AttributeStrategy;
use Collective\Annotations\Routing\Scanner;
use Collective\Annotations\Routing\ScanStrategyInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RoutingScannerTest extends TestCase
{
    /**
     * @param ScanStrategyInterface $strategy
     */
    #[DataProvider("strategyProvider")]
    public function testProperRouteDefinitionsAreGenerated(ScanStrategyInterface $strategy)
    {
        require_once __DIR__ . '/fixtures/annotations/BasicController.php';
        $scanner = new Scanner($strategy, ['App\Http\Controllers\BasicController']);

        $definition = str_replace(PHP_EOL, "\n", $scanner->getRouteDefinitions());
        $this->assertEquals(trim(file_get_contents(__DIR__ . '/results/annotation-basic.php')), $definition);
    }

    /**
     * @param ScanStrategyInterface $strategy
     */
    #[DataProvider("strategyProvider")]
    public function testProperRouteDefinitionsDetailAreGenerated(ScanStrategyInterface $strategy)
    {
        require_once __DIR__ . '/fixtures/annotations/BasicController.php';
        $scanner = new Scanner($strategy, ['App\Http\Controllers\BasicController']);

        $routeDetail = $scanner->getRouteDefinitionsDetail();
        $this->assertEquals(include __DIR__ . '/results/route-detail-basic.php', $routeDetail);
    }

    /**
     * @param ScanStrategyInterface $strategy
     */
    #[DataProvider("strategyProvider")]
    public function testAnyAnnotation(ScanStrategyInterface $strategy)
    {
        require_once __DIR__ . '/fixtures/annotations/AnyController.php';
        $scanner = new Scanner($strategy, ['App\Http\Controllers\AnyController']);

        $definition = str_replace(PHP_EOL, "\n", $scanner->getRouteDefinitions());
        $this->assertEquals(trim(file_get_contents(__DIR__ . '/results/annotation-any.php')), $definition);
    }

    /**
     * @param ScanStrategyInterface $strategy
     */
    #[DataProvider("strategyProvider")]
    public function testAnyAnnotationDetail(ScanStrategyInterface $strategy)
    {
        require_once __DIR__ . '/fixtures/annotations/AnyController.php';
        $scanner = new Scanner($strategy, ['App\Http\Controllers\AnyController']);

        $routeDetail = $scanner->getRouteDefinitionsDetail();
        $this->assertEquals(include __DIR__ . '/results/route-detail-any.php', $routeDetail);
    }

    /**
     * @param ScanStrategyInterface $strategy
     */
    #[DataProvider("strategyProvider")]
    public function testWhereAnnotation(ScanStrategyInterface $strategy)
    {
        require_once __DIR__ . '/fixtures/annotations/WhereController.php';
        $scanner = new Scanner($strategy, ['App\Http\Controllers\WhereController']);

        $definition = $scanner->getRouteDefinitions();
        $this->assertEquals(trim(file_get_contents(__DIR__ . '/results/annotation-where.php')), $definition);
    }

    /**
     * @param ScanStrategyInterface $strategy
     */
    #[DataProvider("strategyProvider")]
    public function testWhereAnnotationDetail(ScanStrategyInterface $strategy)
    {
        require_once __DIR__ . '/fixtures/annotations/WhereController.php';
        $scanner = new Scanner($strategy, ['App\Http\Controllers\WhereController']);
        $routeDetail = $scanner->getRouteDefinitionsDetail();
        $this->assertEquals(include __DIR__ . '/results/route-detail-where.php', $routeDetail);
    }

    /**
     * @param ScanStrategyInterface $strategy
     */
    #[DataProvider("strategyProvider")]
    public function testPrefixAnnotation(ScanStrategyInterface $strategy)
    {
        require_once __DIR__ . '/fixtures/annotations/PrefixController.php';
        $scanner = new Scanner($strategy, ['App\Http\Controllers\PrefixController']);

        $definition = str_replace(PHP_EOL, "\n", $scanner->getRouteDefinitions());
        $this->assertEquals(trim(file_get_contents(__DIR__ . '/results/annotation-prefix.php')), $definition);
    }

    /**
     * @param ScanStrategyInterface $strategy
     */
    #[DataProvider("strategyProvider")]
    public function testInheritedControllerAnnotations(ScanStrategyInterface $strategy)
    {
        require_once __DIR__ . '/fixtures/annotations/AnyController.php';
        require_once __DIR__ . '/fixtures/annotations/ChildController.php';
        $scanner = new Scanner($strategy, [
            'App\Http\Controllers\AnyController',
            'App\Http\Controllers\ChildController'
        ]);

        $definition = str_replace(PHP_EOL, "\n", $scanner->getRouteDefinitions());
        $this->assertEquals(trim(file_get_contents(__DIR__ . '/results/annotation-child.php')), $definition);
    }

    static public function strategyProvider(): array
    {
        return ['attributeStrategy' => [self::attributeStrategy()]];
    }

    protected static function attributeStrategy(): ScanStrategyInterface
    {
        return new AttributeStrategy();
    }
}
