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
use TwistersFury\Phalcon\Json\Tests\AbstractToken;

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
        require_once __DIR__ . '/../../_data/WebToken/AbstractToken.php';

        $filesRoot = vfsStream::setup('root');
        $filesRoot->addChild(
            vfsStream::newFile('someFile.key')
                ->setContent(base64_encode('some-key'))
        );

        $diInstance = new FactoryDefault();
        $diInstance->set('config', new Config(['json' => ['keyFile' => $filesRoot->url() . '/someFile.key']]));
        $diInstance->set('crypt', (new Crypt())->setKey('some-master-key'));

        Di::setDefault($diInstance);

        $this->testSubject = $diInstance->get(AbstractToken::class);
    }

    protected function _after()
    {
        Di::reset();
    }

    // tests
    public function testThrowsConfigException()
    {
        $this->tester->expectException(new \RuntimeException('JSON Configuration Key Missing ("json")'), function() {
            $diInstance = new FactoryDefault();
            $diInstance->set('config', new Config([]));

            Di::setDefault($diInstance);

            $diInstance->get(AbstractToken::class);
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

                $diInstance->get(AbstractToken::class);
            }
        );
    }

    public function testAlgorithmThrowsException() {
        $this->tester->expectException(new \InvalidArgumentException('Hash Algorithm Invalid: invalid'), function() {
            $this->testSubject->setAlgorithm('invalid');
        });
    }

    public function testGetSecret() {
        $reflectionMethod = new \ReflectionMethod(\TwistersFury\Phalcon\Json\WebToken\AbstractToken::class, 'getSecret');
        $reflectionMethod->setAccessible(true);

        $this->tester->assertEquals('some-master-keysome-key', $reflectionMethod->invoke($this->testSubject));
        $this->tester->assertEquals('some-master-keysome-key', $reflectionMethod->invoke($this->testSubject));
    }

    public function testDefaultConfig()
    {
        $this->assertEquals(300, $this->testSubject->getExpirationLength());
        $this->assertEquals('twistersfury/phalcon-json', $this->testSubject->getIssuer());
        $this->assertEquals('HS512', $this->testSubject->getAlgorithm());
    }
}