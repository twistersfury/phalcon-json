<?php
/*
 * This file is part of the Phalcon Json package.
 *
 * (c) Phoenix Osiris <phoenix@twistersfury.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwistersFury\Phalcon\Json\Tests\WebToken;

use Codeception\Test\Unit;
use org\bovigo\vfs\vfsStream;
use Phalcon\Config;
use Phalcon\Crypt;
use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use TwistersFury\Phalcon\Json\WebToken\Firebase;

class FirebaseTest extends Unit
{
    /**
     * @var \Tester
     */
    protected $tester;

    /** @var Firebase */
    protected $testSubject = null;

    protected function _before()
    {
        $filesRoot = vfsStream::setup('root');
        $filesRoot->addChild(
            vfsStream::newFile('someFile.key')
                ->setContent(base64_encode('some-key'))
        );

        $diInstance = new FactoryDefault();
        $diInstance->set('config', new Config(['json' => ['keyFile' => $filesRoot->url() . '/someFile.key']]));
        $diInstance->set('crypt', (new Crypt())->setKey('some-master-key'));

        Di::setDefault($diInstance);

        $this->testSubject = $diInstance->get(Firebase::class);

        $this->testSubject->setStartTime(time());
    }

    protected function _after()
    {
        Di::reset();
    }

    public function testSetupException()
    {
        $this->tester->expectException(
            new \RuntimeException('Missing Firebase JWT Library'),
            function () {
                $reflectionProperty = new \ReflectionProperty(Firebase::class, 'className');
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($this->testSubject, false);

                $reflectionMethod = new \ReflectionMethod(Firebase::class, 'checkRequirements');
                $reflectionMethod->setAccessible(true);
                $reflectionMethod->invoke($this->testSubject);
            }
        );
    }

    public function testGenerate() {
        $jsonToken = $this->testSubject->generateToken(['some-data' => 'some-value']);
        $tokenData = $this->testSubject->parseToken($jsonToken);
        $this->assertEquals(
            [
                'some-data' => 'some-value'
            ],
            (array) $tokenData->data
        );

        $this->assertEquals(64, strlen($tokenData->jti));
    }
}