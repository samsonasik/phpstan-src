<?php declare(strict_types = 1);

namespace PHPStan\Rules\Comparison;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Parser\LastConditionVisitor;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\VerbosityLevel;
use function sprintf;

/**
 * @implements Rule<Node\Expr\BinaryOp>
 */
class ConstantLooseComparisonRule implements Rule
{

	public function __construct(
		private bool $checkAlwaysTrueLooseComparison,
		private bool $reportAlwaysTrueInLastCondition,
	)
	{
	}

	public function getNodeType(): string
	{
		return Node\Expr\BinaryOp::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (!$node instanceof Node\Expr\BinaryOp\Equal && !$node instanceof Node\Expr\BinaryOp\NotEqual) {
			return [];
		}

		$nodeType = $scope->getType($node);
		if (!$nodeType instanceof ConstantBooleanType) {
			return [];
		}

		if (!$nodeType->getValue()) {
			return [
				RuleErrorBuilder::message(sprintf(
					'Loose comparison using %s between %s and %s will always evaluate to false.',
					$node instanceof Node\Expr\BinaryOp\Equal ? '==' : '!=',
					$scope->getType($node->left)->describe(VerbosityLevel::value()),
					$scope->getType($node->right)->describe(VerbosityLevel::value()),
				))->build(),
			];
		} elseif ($this->checkAlwaysTrueLooseComparison) {
			$isLast = $node->getAttribute(LastConditionVisitor::ATTRIBUTE_NAME);
			if ($isLast === true && !$this->reportAlwaysTrueInLastCondition) {
				return [];
			}

			$errorBuilder = RuleErrorBuilder::message(sprintf(
				'Loose comparison using %s between %s and %s will always evaluate to true.',
				$node instanceof Node\Expr\BinaryOp\Equal ? '==' : '!=',
				$scope->getType($node->left)->describe(VerbosityLevel::value()),
				$scope->getType($node->right)->describe(VerbosityLevel::value()),
			));
			if ($isLast === false && !$this->reportAlwaysTrueInLastCondition) {
				$errorBuilder->tip('Remove remaining cases below this one and this error will disappear too.');
			}

			return [$errorBuilder->build()];
		}

		return [];
	}

}