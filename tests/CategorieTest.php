<?php

use PHPUnit\Framework\TestCase;
use model\Categorie;

class CategorieTest extends TestCase
{
    public function testCategorieCreation()
    {
        $categorie = new Categorie();
        $this->assertInstanceOf(Categorie::class, $categorie);
    }
}