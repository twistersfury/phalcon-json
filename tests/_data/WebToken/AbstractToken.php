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

    use TwistersFury\Phalcon\Json\WebToken\AbstractToken as sourceToken;

    class AbstractToken extends sourceToken
    {
        public function generateToken(array $tokenData, array $tokenBase = null)
        {
            throw new \Exception("Not Implemented");
        }

        public function parseToken(string $jsonToken): array
        {
            throw new \Exception("Not Implemented");
        }

        public function isAlgorithmValid(string $hashAlgorithm)
        {
            if ($hashAlgorithm === 'invalid') {
                return false;
            }

            return parent::isAlgorithmValid($hashAlgorithm);
        }
    }