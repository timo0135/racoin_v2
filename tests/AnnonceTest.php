<?php

use PHPUnit\Framework\TestCase;
use model\Annonce;

class AnnonceTest extends TestCase
{
    public function testAnnonceCreation()
    {
        $annonce = new Annonce();
        $this->assertInstanceOf(Annonce::class, $annonce);
    }
}