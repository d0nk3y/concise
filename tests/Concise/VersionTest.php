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
        $this->assert($this->version->getVersionForPackage('sebastian/version'), matches_regex, '/^\\d\.\\d+/');
    }
}
