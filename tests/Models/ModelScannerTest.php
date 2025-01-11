<?php

use Collective\Annotations\Database\Eloquent\Annotations\AnnotationStrategy;
use Collective\Annotations\Database\Eloquent\Attributes\AttributeStrategy;
use Collective\Annotations\Database\InvalidBindingResolverException;
use Collective\Annotations\Database\Scanner;
use Collective\Annotations\Database\ScanStrategyInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ModelScannerTest extends TestCase
{

    /**
     * @param ScanStrategyInterface $strategy
     * @throws InvalidBindingResolverException
     */
    #[DataProvider("strategyProvider")]
    public function testProperModelDefinitionsAreGenerated(ScanStrategyInterface $strategy)
    {
        require_once __DIR__.'/fixtures/annotations/AnyModel.php';
        $scanner = new Scanner($strategy, ['App\User']);

        $definition = str_replace(PHP_EOL, "\n", $scanner->getModelDefinitions());
        $this->assertEquals(trim(file_get_contents(__DIR__.'/results/annotation.php')), $definition);
    }

    /**
     * @param ScanStrategyInterface $strategy
     * @throws InvalidBindingResolverException
     */
    #[DataProvider("strategyProvider")]
    public function testNonEloquentModelThrowsException(ScanStrategyInterface $strategy)
    {
        $this->expectException(InvalidBindingResolverException::class);
        $this->expectExceptionMessage('Class [App\NonEloquentModel] is not a subclass of [Illuminate\Database\Eloquent\Model]');

        require_once __DIR__.'/fixtures/annotations/NonEloquentModel.php';
        $scanner = new Scanner($strategy, ['App\NonEloquentModel']);
        $scanner->getModelDefinitions();
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
