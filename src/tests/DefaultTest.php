<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class Test extends TestCase
{
	public function testTrue()
	{
		return $this->assertIsBool(true, "ok");
	}
}
