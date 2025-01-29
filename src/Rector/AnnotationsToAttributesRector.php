<?php

namespace Collective\Annotations\Rector;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Rector\Rector\AbstractRector;

class AnnotationsToAttributesRector extends AbstractRector {

	private const ROUTE_NAMESPACE = 'Collective\Annotations\Routing\Attributes\Attributes';
	private const MODEL_NAMESPACE = 'Collective\Annotations\Database\Eloquent\Attributes\Attributes';
	private const MODEL_ANNOTATIONS = ['Bind'];

	public function getNodeTypes(): array
	{
		return [Class_::class, ClassMethod::class];
	}

	/**
	 * @param Class_|ClassMethod $node
	 * @return null|Class_|ClassMethod
	 */
	public function refactor(Node $node)
	{
// echo $node->name->name, "\n";
		// Ignore Model methods
		if ($node instanceof ClassMethod) {
			$scope = $node->getAttribute('scope');
			if ($scope instanceof Scope) {
				$parent = $scope->getClassReflection();
				if ($parent && $parent->isSubclassOf(Model::class)) {
					return null;
				}
			}
		}

		$doc = $node->getDocComment();
		if (!$doc) {
			return null;
		}
		$phpdoc = $doc->getText();

		$lines = explode("\n", $phpdoc);
		$newComment = [];
		foreach ($lines as $line) {
			if (preg_match('#@(?:Any|Controller|Delete|Get|Middleware|Options|Patch|Post|Put|Resource|Route|Where|Bind)\(.+#', $line, $match)) {
				if ($this->addAnnotation($node, $match[0])) {
					continue;
				}
			}

			$newComment[] = $line;
		}

		// Nothing changed, so really change nothing
		if (count($newComment) == count($lines)) {
			return null;
		}

		// Only /** and */ are left, so remove the whole thing
		if (count($newComment) == 2) {
			// array_splice($newComment, 1, 0, str_replace(' */', ' *', $newComment[1]));
			// $newComment = [];
			$comments = array_values(array_filter($node->getComments(), function($comment) {
				return !($comment instanceof Doc);
			}));
			$node->setAttribute('comments', $comments);
		}
		else {
			$node->setDocComment(new Doc(implode("\n", $newComment)));
		}

		// preg_match_all('#@(?:Middleware|Get|Post|Any)\(.+#', $phpdoc, $matches);
		// foreach ($matches[0] as $annotation) {
		// 	$this->addAnnotation($node, $annotation);
		// }

		return $node;
	}

    /**
     * @param Class_|ClassMethod $node
     */
	protected function addAnnotation(Node $node, string $annotation) : bool {
		$annotation = trim($annotation, '@');
		if (preg_match('#=\s*\{#', $annotation) || str_contains($annotation, '({')) {
			echo "  WARNING: " . $node->name->name . " contains dangerous {} properties\n";
			// return false;
		}

		$attrName = explode('(', $annotation)[0];
		$namespace = in_array($attrName, self::MODEL_ANNOTATIONS) ? self::MODEL_NAMESPACE : self::ROUTE_NAMESPACE;
		$annotation = preg_replace('#, (\w+)=#', ', $1: ', $annotation);
		$annotation = preg_replace('#\((\w+)=#', '($1: ', $annotation);
		$attrClass = sprintf('%s\%s', $namespace, $annotation);
		$node->attrGroups[] = new AttributeGroup([new Attribute(new FullyQualified($attrClass))]);
		return true;
	}

}
