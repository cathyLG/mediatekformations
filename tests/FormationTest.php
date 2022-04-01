<?php

namespace App\Tests;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

/**
 * Description of FormationTest
 *
 * @author Xiaoxiao
 */
class FormationTest extends TestCase {

    public function testGetPublishedAtString() {
        $formation = new Formation();
        $formation->setPublishedAt(new \DateTime("2022-01-15"));
        $this->assertEquals("15/01/2022", $formation->getPublishedAtString());
    }

}
