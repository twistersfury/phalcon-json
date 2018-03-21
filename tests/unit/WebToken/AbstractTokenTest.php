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
use TwistersFury\Phalcon\Json\WebToken\AbstractToken;

class AbstractTokenTest extends Unit
{
    /**
     * @var \Tester
     */
    protected $tester;

    /** @var AbstractToken */
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

        /** @var AbstractToken testSubject */
        $this->testSubject = $this->getMockBuilder(AbstractToken::class)
            ->getMockForAbstractClass();

        $this->testSubject->setDI($diInstance);

        $this->testSubject->setStartTime(100);
    }

    protected function _after()
    {
        Di::reset();
    }

    public function testThrowsCryptException()
    {
        $this->tester->expectException(
            new \RuntimeException('Missing "crypt" service in DIC'),
            function () {
                $diInstance = new FactoryDefault();
                $diInstance->remove('crypt');

                Di::setDefault($diInstance);

                /** @var AbstractToken testSubject */
                $this->testSubject = $this->getMockBuilder(AbstractToken::class)
                                          ->getMockForAbstractClass();

                $this->testSubject->setDI($diInstance);
            }
        );
    }

    public function testThrowsConfigException()
    {
        $this->tester->expectException(
            new \RuntimeException('Missing "config" service in DIC'),
            function () {
                $diInstance = new FactoryDefault();
                $diInstance->remove('config');

                Di::setDefault($diInstance);

                /** @var AbstractToken testSubject */
                $this->testSubject = $this->getMockBuilder(AbstractToken::class)
                                          ->getMockForAbstractClass();

                $this->testSubject->setDI($diInstance);
            }
        );
    }

    // tests
    public function testThrowsConfigMissingException()
    {
        $this->tester->expectException(new \RuntimeException('JSON Configuration Key Missing ("json")'), function() {
            $diInstance = new FactoryDefault();
            $diInstance->set('config', new Config([]));

            Di::setDefault($diInstance);

            /** @var AbstractToken testSubject */
            $this->testSubject = $this->getMockBuilder(AbstractToken::class)
                                      ->getMockForAbstractClass();

            $this->testSubject->setDI($diInstance);
        });
    }

    public function testThrowsKeyException()
    {
        $this->tester->expectException(
            new \RuntimeException('Missing JSON Web Token Key File ("keyFile")'),
            function() {
                $filesRoot = vfsStream::setup('root');

                $diInstance = new FactoryDefault();
                $diInstance->set('config', new Config(['json' => ['keyFile' => $filesRoot->url() . '/someFile.key']]));

                Di::setDefault($diInstance);

                /** @var AbstractToken testSubject */
                $this->testSubject = $this->getMockBuilder(AbstractToken::class)
                                          ->getMockForAbstractClass();

                $this->testSubject->setDI($diInstance);
            }
        );
    }

    public function testAlgorithmThrowsException() {
        $this->tester->expectException(new \InvalidArgumentException('Hash Algorithm Invalid: '), function() {
            $this->testSubject->setAlgorithm('');
        });
    }

    public function testGetSecret() {
        $reflectionMethod = new \ReflectionMethod(\TwistersFury\Phalcon\Json\WebToken\AbstractToken::class, 'getSecret');
        $reflectionMethod->setAccessible(true);

        $this->tester->assertEquals('some-master-keysome-key', $reflectionMethod->invoke($this->testSubject));
        $this->tester->assertEquals('some-master-keysome-key', $reflectionMethod->invoke($this->testSubject));
    }

    public function testExpirationLength()
    {
        $this->assertEquals(300, $this->testSubject->getExpirationLength());
        $this->testSubject->setExpirationLength(500);
        $this->assertEquals(500, $this->testSubject->getExpirationLength());
    }

    public function testStartTime()
    {
        $this->assertLessThanOrEqual(time(), $this->testSubject->getStartTime());
        $this->testSubject->setStartTime(10);
        $this->assertEquals(10, $this->testSubject->getStartTime());
    }

    public function testAlgorithm()
    {
        $this->assertEquals('HS512', $this->testSubject->getAlgorithm());
        $this->testSubject->setAlgorithm('Something');
        $this->assertEquals('Something', $this->testSubject->getAlgorithm());
    }

    public function testIssuer()
    {
        $this->assertEquals('twistersfury/phalcon-json', $this->testSubject->getIssuer());
        $this->testSubject->setIssuer('some-one');
        $this->assertEquals('some-one', $this->testSubject->getIssuer());
    }
}