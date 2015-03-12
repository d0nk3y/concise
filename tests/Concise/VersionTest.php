<?php

namespace Concise;

class VersionTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->version = new Version();
    }

    public function testAnEmptyStringWillBeReturnedIfThePackageCanNotBeFound()
    {
        $this->assert($this->version->getVersionForPackage('foo'), is_blank);
    }

    public function testVersionCanBeExtractedForAPackageName()
    {
        $this->assert(
            $this->version->getVersionForPackage('sebastian/version'),
            matches_regex,
            '/^\\d\.\\d+/'
        );
    }

    public function testWeCanEasilyGetTheConciseVersion()
    {
        $this->assert(
            $this->version->getConciseVersion(),
            equals,
            $this->version->getVersionForPackage('elliotchance/concise')
        );
    }

    public function testFindingVendorFolder()
    {
        $this->assert($this->version->findVendorFolder(), ends_with, '/vendor');
    }

    public function testReturnEmptyStringIfVendorFolderCannotBeFound()
    {
        $version = $this->niceMock('Concise\Version')
                        ->stub('findVendorFolder')
                        ->get();
        $this->assert($version->getConciseVersion(), is_blank);
    }

    public function testFindVendorFolderWillReturnNullIfTheVendorFolderCouldNotBeFound()
    {
        $version = $this->niceMock('Concise\Version')
                        ->expose('findVendorFolder')
                        ->get();
        $this->assert($version->findVendorFolder('/tmp'), is_null);
    }

    public function versionData()
    {
        return array(
            // Edge cases.
            'unknown version is blank' => array('', ''),
            'version can be prefixed with v' => array('v1.1', 'Dark Matter'),

            // Specific versions.
            array('1.0', 'Big Bang'),
            array('1.1', 'Dark Matter'),
            array('1.2', 'String Theory'),
            array('1.3', 'Supernova'),
            array('1.4', 'Quantum'),
            array('1.5', 'Antimatter'),
            array('1.6', 'Entropy'),
            array('1.7', 'Multiverse'),
        );
    }

    /**
     * @group #257
     * @dataProvider versionData
     */
    public function testVersionName($version, $expectedName)
    {
        $name = $this->version->getVersionNameForVersion($version);
        $this->assert($name, equals, $expectedName);
    }
}
