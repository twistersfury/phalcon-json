<?php
    /*
     * This file is part of the Phalcon Json package.
     *
     * (c) Phoenix Osiris <phoenix@twistersfury.com>
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */

    namespace TwistersFury\Phalcon\Shared\Tests;

    use \SplFileInfo;

    class CodeCoverageTest extends \PHPUnit_Framework_TestCase {
        protected $sourcePath = null;
        protected $testsPath  = null;

        /**
         * @dataProvider _dpLoadFiles
         * @param SplFileInfo $fileInfo File To Test
         */
        public function testUnitTestExists(SplFileInfo $fileInfo) {
            $filePath = str_replace($this->buildSourcePath(), $this->buildTestsPath(), $fileInfo->getRealPath());
            $filePath = str_replace('.php', 'Test.php', $filePath);

            $this->assertFileExists($filePath);
        }

        protected function buildSourcePath() : string
        {
            if ($this->sourcePath !== null) {
                return $this->sourcePath;
            }

            $this->sourcePath = __DIR__;
            $this->sourcePath = realpath($this->sourcePath . '/../../src');

            return $this->sourcePath;
        }

        protected function buildTestsPath() : string
        {
            if ($this->testsPath !== null)
            {
                return $this->testsPath;
            }

            $this->testsPath = __DIR__;
            $this->testsPath = realpath($this->testsPath . '/../unit');

            return $this->testsPath;
        }

        public function _dpLoadFiles() : array
        {
            $filesList = [];

            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->buildSourcePath(), \RecursiveDirectoryIterator::SKIP_DOTS)) as $filePath) {
                //Ignore Config Items
                if (strstr($filePath->getRealPath(), '/etc') !== false || $filePath->getBasename() === 'phalcon_bootstrap.php' || strstr($filePath->getRealPath(), '/Interfaces/') !== false) {
                    continue;
                }

                $filesList[] = [$filePath];
            }

            return $filesList;
        }
    }