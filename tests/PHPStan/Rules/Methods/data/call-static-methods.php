<?php

namespace CallStaticMethods;

class Foo
{

	public static function test()
	{
		Bar::protectedMethodFromChild();
	}

	protected static function baz()
	{

	}

	public function loremIpsum()
	{

	}

	private static function dolor()
	{

	}

}

class Bar extends Foo
{

	public static function test()
	{
		Foo::test();
		Foo::baz();
		parent::test();
		parent::baz();
		Foo::bar(); // nonexistent
		self::bar(); // nonexistent
		parent::bar(); // nonexistent
		Foo::loremIpsum(); // instance
		Foo::dolor();
	}

	public function loremIpsum()
	{
		parent::loremIpsum();
	}

	protected static function protectedMethodFromChild()
	{

	}

}

class Ipsum
{

	public static function ipsumTest()
	{
		parent::lorem(); // does not have a parent
		Foo::test();
		Foo::test(1);
		Foo::baz(); // protected and not from a parent
		UnknownStaticMethodClass::loremIpsum();
	}

}

class ClassWithConstructor
{

	private function __construct($foo)
	{

	}

}

class CheckConstructor extends ClassWithConstructor
{

	public function __construct()
	{
		parent::__construct();
	}

}

function () {
	self::someStaticMethod();
	static::someStaticMethod();
	parent::someStaticMethod();
	Foo::test();
	Foo::baz();
	Foo::bar();
	Foo::loremIpsum();
	Foo::dolor();

	\Locale::getDisplayLanguage('cs_CZ'); // OK
	\Locale::getDisplayLanguage('cs_CZ', 'en'); // OK
	\Locale::getDisplayLanguage('cs_CZ', 'en', 'foo'); // should report 3 parameters given, 1-2 required
};

interface SomeInterface
{

}

function (Foo $foo) {
	if ($foo instanceof SomeInterface) {
		$foo::test();
		$foo::test(1, 2, 3);
	}

	/** @var string|int $stringOrInt */
	$stringOrInt = doFoo();
	$stringOrInt::foo();
};

function (FOO $foo)
{
	$foo::test(); // do not report case mismatch

	FOO::unknownMethod();
	FOO::loremIpsum();
	FOO::dolor();
	FOO::test(1, 2, 3);
	FOO::TEST();
	FOO::test();
};

function (string $className) {
	$className::foo();
};

class CallingNonexistentParentConstructor extends Foo
{

	public function __construct()
	{
		parent::__construct();
	}

}

class Baz extends Foo
{

	public function doFoo()
	{
		parent::nonexistent();
	}

	public static function doBaz()
	{
		parent::nonexistent();
		parent::loremIpsum();
	}

}

class ClassOrString
{

	public function doFoo()
	{
		/** @var self|string $class */
		$class = doFoo();
		$class::calledMethod();
		$class::calledMethod(1);
		Self::calledMethod();
	}

	private static function calledMethod()
	{

	}

	public function doBar()
	{
		if (rand(0, 1)) {
			$class = 'Blabla';
		} else {
			$class = 'Bleble';
		}
		$class::calledMethod();
	}

}

interface InterfaceWithStaticMethod
{

	public static function doFoo();

	public function doInstanceFoo();

}

class CallStaticMethodOnAnInterface
{

	public function doFoo(InterfaceWithStaticMethod $foo)
	{
		InterfaceWithStaticMethod::doFoo();
		InterfaceWithStaticMethod::doBar();
		$foo::doFoo(); // fine - it's an object

		InterfaceWithStaticMethod::doInstanceFoo();
		$foo::doInstanceFoo();
	}

}

class CallStaticMethodAfterAssignmentInBooleanAnd
{

	public static function generateDeliverSmDlrForMessage()
	{
		if (
			($messageState = self::getMessageStateByStatusId())
			&& self::isMessageStateRequested($messageState)
		) {
		}
	}

	/**
	 * @return false|string
	 */
	public static function getMessageStateByStatusId()
	{
	}

	public static function isMessageStateRequested (string $messageState): bool
	{
	}

}

class PreserveArrayKeys
{
	private $p = [];

	/**
	 * @param array<string, bool> $map
	 */
	private function test(array $map)
	{
		foreach ($this->p['foo'] as $key => $_) {
			if (!isset($map[$key])) {
				throw self::e(array_keys($map));
			}
		}
	}

	/**
	 * @param array<string, bool> $map
	 */
	private function test2(array $map)
	{
		foreach ($this->p['foo'] as $key => $_) {
			if (!array_key_exists($key, $map)) {
				throw self::e(array_keys($map));
			}
		}
	}

	/**
	 * @param string[] $list
	 */
	private static function e($list): \Exception
	{
		return new \Exception();
	}
}

class ClassStringChecks
{

	/**
	 * @template T of Foo
	 * @param class-string $classString
	 * @param class-string<T> $anotherClassString
	 * @param class-string<Foo> $yetAnotherClassString
	 */
	public function doFoo(
		string $classString,
		string $anotherClassString,
		string $yetAnotherClassString
	)
	{
		$classString::nonexistentMethod();

		$anotherClassString::test();
		$anotherClassString::test(1, 2, 3);
		$anotherClassString::nonexistentMethod();

		$yetAnotherClassString::test();
		$yetAnotherClassString::test(1, 2, 3);
		$yetAnotherClassString::nonexistentMethod();
	}

}

trait TraitWithStaticMethod
{

	public static function doFoo(): void
	{

	}

}

class MethodCallingTraitWithStaticMethod
{

	public function doFoo(): void
	{
		TraitWithStaticMethod::doFoo();
	}

	public function doBar(TraitWithStaticMethod $a): void
	{
		$a::doFoo();
	}

}

class CallWithStatic
{

	private function doFoo()
	{

	}

	public function doBar()
	{
		static::doFoo(); // reported by different rule
		static::nonexistent();
	}

}
