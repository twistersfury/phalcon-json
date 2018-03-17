<?php
    /**
     * @author    Phoenix <phoenix@twistersfury.com>
     * @license   proprietary
     * @copyright 2016 Twister's Fury
     */

    namespace TwistersFury\Phalcon\Json;

    use Phalcon\Loader;

    (new Loader())
        ->registerNamespaces(
            [
                __NAMESPACE__ => __DIR__
            ]
        )->register(true);
