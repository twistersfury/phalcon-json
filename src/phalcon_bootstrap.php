<?php
    /**
     * @author    Phoenix <phoenix@twistersfury.com>
     * @license   proprietary
     * @copyright 2016 Twister's Fury
     */

    namespace TwistersFury\Phalcon\Json;

    use Phalcon\Loader;

    //JIC - Usually Means Composer Install Is Running And Phalcon Isn't Installed (IE: CI)
    if (!class_exists('\Phalcon\Loader')) {
        return;
    }

    (new Loader())
        ->registerNamespaces(
            [
                __NAMESPACE__ => __DIR__
            ]
        )->register(true);
