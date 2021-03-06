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

    class Firebase extends AbstractToken
    {
        private $className = null;
        
        public function __construct()
        {
            $this->checkRequirements();
        }

        private function checkRequirements()
        {
            if ($this->className === null) {
                if (class_exists('\Firebase\JWT\JWT')) {
                    $this->className = '\Firebase\JWT\JWT';
                } elseif (class_exists('\JWT')) {
                    $this->className = '\JWT';
                }
            }

            if (!$this->className) {
                throw new \RuntimeException('Missing Firebase JWT Library');
            }
        }

        /**
         * @param array $tokenData
         * @param array $tokenBase
         *
         * @return string
         */
        public function generateToken(array $tokenData, array $tokenBase = []) : string
        {
            /** @var \Phalcon\Security\Random $randomGenerator */
            $randomGenerator = $this->getDI()->get('\Phalcon\Security\Random');

            $tokenBase = array_merge(
                [
                    'iat'  => time(),
                    'jti'  => $randomGenerator->hex( 32 ),
                    'iss'  => $this->getIssuer(),
                    'nbf'  => $this->getStartTime(),
                    'exp'  => $this->getStartTime() + $this->getExpirationLength(),
                ],
                $tokenBase
            );

            $tokenBase['data'] = $tokenData;

            return $this->encode($tokenBase);
        }

        /**
         * @param string $jsonToken
         *
         * @return \stdClass
         */
        public function parseToken(string $jsonToken) : \stdClass
        {
            return $this->decode($jsonToken);
        }

        /**
         * @param array $tokenData
         *
         * @return string
         * @codeCoverageIgnore
         */
        private function encode(array $tokenData) : string
        {
            $className = $this->className;
            return $className::encode(
                $tokenData,
                $this->getSecret(),
                $this->getAlgorithm()
            );
        }

        /**
         * @param string $jsonToken
         *
         * @return object
         * @codeCoverageIgnore
         */
        private function decode(string $jsonToken) : \stdClass
        {
            $className = $this->className;
            return $className::decode(
                $jsonToken,
                $this->getSecret(),
                [$this->getAlgorithm()]
            );
        }
    }
