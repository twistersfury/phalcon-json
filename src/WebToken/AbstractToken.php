<?php
    /*
     * This file is part of the Phalcon Json package.
     *
     * (c) Phoenix Osiris <phoenix@twistersfury.com>
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */


    namespace TwistersFury\Phalcon\Json\WebToken;

    use TwistersFury\Phalcon\Shared\Traits\Injectable;

    abstract class AbstractToken
    {
        use Injectable;

        private $apiKey = null;
        private $hashAlgorithm = 'HS512';

        public function __construct() {
            if (!$this->getDI()->get('config')->get('json')) {
                throw new \RuntimeException('JSON Configuration Missing');
            } else if (!file_exists($this->getDI()->get('config')->json->get('keyFile'))) {
                throw new \RuntimeException('Missing JSON Web Token Key File');
            }
        }

        protected function getSecret() : string {
            if ($this->apiKey !== null) {
                return $this->apiKey;
            }

            $this->apiKey = $this->getDi()->get('crypt')->getKey() . base64_decode(
                file_get_contents($this->getDI()->get('config')->json->keyFile)
            );

            return $this->apiKey;
        }

        public function getAlgorithm()
        {
            return $this->hashAlgorithm;
        }

        public function setAlgorithm(string $hashAlgorithm)
        {
            if (!$this->isAlgorithmValid($hashAlgorithm)) {
                throw new \InvalidArgumentException('Hash Algorithm Invalid: ' . $hashAlgorithm);
            }

            $this->hashAlgorithm = $hashAlgorithm;

            return $this;
        }

        public function isAlgorithmValid(string $hashAlgorithm)
        {
            return $hashAlgorithm !== '';
        }

        abstract function generateToken(array $tokenData, array $tokenBase = []);
        abstract function parseToken(string $jsonToken);
    }