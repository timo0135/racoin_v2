<?php

use PHPUnit\Framework\TestCase;
use model\SousCategorie;

class SousCategorieTest extends TestCase
{
    public function testSousCategorieCreation()
    {
        $sousCategorie = new SousCategorie();
        $this->assertInstanceOf(SousCategorie::class, $sousCategorie);
    }
}