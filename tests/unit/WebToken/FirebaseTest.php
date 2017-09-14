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
use TwistersFury\Phalcon\Json\Tests\FirebaseTest as Firebase;

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
        require_once __DIR__ . '/../../_data/WebToken/AbstractToken.php';
        require_once __DIR__ . '/../../_data/WebToken/FirebaseTest.php';

        $filesRoot = vfsStream::setup('root');
        $filesRoot->addChild(
            vfsStream::newFile('someFile.key')
                ->setContent(base64_encode('some-key'))
        );

        $diInstance = new FactoryDefault();
        $diInstance->set('config', new Config(['json' => ['keyFile' => $filesRoot->url() . '/someFile.key']]));
        $diInstance->set('crypt', (new Crypt())->setKey('some-master-key'));

        Di::setDefault($diInstance);

        $this->testSubject = new Firebase();
    }

    protected function _after()
    {
        Di::reset();
    }

    public function testGenerate() {
        $this->testSubject->setAlgorithm('some-hash');
        $this->tester->assertEquals('some-string', $this->testSubject->generateToken(['some-data' => 'some-value']));
        $this->tester->assertArrayHasKey('iat', $this->testSubject->wasCalled);
        $this->tester->assertArrayHasKey('jti', $this->testSubject->wasCalled);
        $this->tester->assertArrayHasKey('iss', $this->testSubject->wasCalled);
        $this->tester->assertArrayHasKey('nbf', $this->testSubject->wasCalled);
        $this->tester->assertArrayHasKey('exp', $this->testSubject->wasCalled);
        $this->tester->assertArrayHasKey('data', $this->testSubject->wasCalled);
        $this->tester->assertEquals(['some-data' => 'some-value'], $this->testSubject->wasCalled['data']);
    }


}