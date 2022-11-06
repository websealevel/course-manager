<?php

/**
 * Structure de Flag. Un nom complet, un nom raccourci et une fonction.
 */
class Flag
{
    public function __construct(
        readonly public string $name,
        readonly public string $short,
        readonly public Closure $action
    ) {
    }
}
