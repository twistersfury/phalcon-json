<?php
    /*
     * This file is part of the Phalcon Json package.
     *
     * (c) Phoenix Osiris <phoenix@twistersfury.com>
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */

    namespace TwistersFury\Phalcon\Json\Tests;

    use TwistersFury\Phalcon\Json\WebToken\Firebase;

    class FirebaseTest extends Firebase
    {
        public $wasCalled = false;

        public function encode(array $tokenData) : string
        {
            $this->wasCalled = $tokenData;

            return 'some-string';
        }

        public function decode(string $jsonToken) : \stdClass
        {
            $this->wasCalled = $jsonToken;

            return (object) ['some' => 'value'];
        }
    }