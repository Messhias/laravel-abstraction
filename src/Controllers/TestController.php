<?php
/*
 * Copyright (c) 2022.
 *
 * Fabio William Conceição <messhias@gmail.com>
 */

namespace Messhias\LaravelAbstraction\Controllers;

final class TestController extends ResourceAPIController
{
	public function __construct()
	{
	}
	
	protected function getKeyIdentifier(): string
	{
		return "test_controller";
	}
}
