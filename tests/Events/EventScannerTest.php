<?php

use Collective\Annotations\Events\Annotations\AnnotationStrategy;
use Collective\Annotations\Events\Attributes\AttributeStrategy;
use Collective\Annotations\Events\Scanner;
use Collective\Annotations\Events\ScanStrategyInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EventScannerTest extends TestCase
{
    /**
     * @param ScanStrategyInterface $strategy
     */
    #[DataProvider("strategyProvider")]
    public function testProperEventDefinitionsAreGenerated(ScanStrategyInterface $strategy)
    {
        require_once __DIR__ . '/fixtures/handlers/BasicEventHandler.php';
        $scanner = new Scanner($strategy, ['App\Handlers\Events\BasicEventHandler']);

        $definition = str_replace(PHP_EOL, "\n", $scanner->getEventDefinitions());
        $this->assertEquals(trim(file_get_contents(__DIR__ . '/results/annotation-basic.php')), $definition);
    }

    /**
     * @param ScanStrategyInterface $strategy
     */
    #[DataProvider("strategyProvider")]
    public function testProperMultipleEventDefinitionsAreGenerated(ScanStrategyInterface $strategy)
    {
        require_once __DIR__ . '/fixtures/handlers/MultipleEventHandler.php';
        $scanner = new Scanner($strategy, ['App\Handlers\Events\MultipleEventHandler']);

        $definition = str_replace(PHP_EOL, "\n", $scanner->getEventDefinitions());
        $this->assertEquals(trim(file_get_contents(__DIR__ . '/results/annotation-multiple.php')), $definition);
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
