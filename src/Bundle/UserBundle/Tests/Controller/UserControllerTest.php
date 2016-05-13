<?php

namespace Bundle\UserBundle\Tests\Controller;

use Bundle\UserBundle\Controller\UserController;

class UserControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $calc = new UserController();
        $result = $calc->add(30, 12);

        // assert that your calculator added the numbers correctly!
        $this->assertEquals(42, $result);
    }
}

