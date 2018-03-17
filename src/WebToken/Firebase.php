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

    use Firebase\JWT\JWT;

    class Firebase extends AbstractToken
    {
        public function __construct() {
            parent::__construct();

            if (!class_exists('\Firebase\JWT\JWT')) {
                throw new \RuntimeException('Missing Firebase JWT Library'); //@cofffdeCoverageIgnore
            }
        }

        /**
         * @param array $tokenData
         * @param array $tokenBase
         *
         * @return string
         */
        public function generateToken(array $tokenData, array $tokenBase = [])
        {
            /** @var \Phalcon\Security\Random $randomGenerator */
            $randomGenerator = $this->getDI()->get('\Phalcon\Security\Random');

            $tokenBase = array_merge(
                [
                    'iat'  => time(),
                    'jti'  => $randomGenerator->hex( 32 ),
                    'iss'  => 'twistersfury/phalcon-json',
                    'nbf'  => time(),
                    'exp'  => time() + $this->getExpirationLength(),
                ],
                $tokenBase
            );

            $tokenBase['data'] = $tokenData;

            return $this->encode($tokenBase);
        }

        /**
         * @param string $jsonToken
         *
         * @return array
         */
        public function parseToken(string $jsonToken) : array
        {
            $tokenData = $this->decode($jsonToken);

            return json_decode(json_encode($tokenData->data), true);
        }

        /**
         * @param array $tokenData
         *
         * @return string
         * @codeCoverageIgnore
         */
        public function encode(array $tokenData) {
            return JWT::encode(
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
        public function decode(string $jsonToken)
        {
            return JWT::decode(
                $jsonToken,
                $this->getSecret(),
                [$this->getAlgorithm()]
            );
        }
    }
