<?php

/**
 * This file contains the L10nTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\L10n\Tests;

use Lunr\Halo\PropertyTraits\PsrLoggerTestTrait;

/**
 * This class contains test methods for the L10n class.
 *
 * @covers Lunr\L10n\L10n
 */
class L10nBaseTest extends L10nTest
{

    use PsrLoggerTestTrait;

    /**
     * Test that $languages is initially empty.
     */
    public function testLanguagesEmpty(): void
    {
        $properties = $this->reflection->getStaticProperties();
        $languages  = $properties['languages'];

        $this->assertEmpty($languages);
    }

    /**
     * Test that the FilesystemAccessObject class is passed correctly.
     */
    public function testFAOIsPassedCorrectly(): void
    {
        $this->assertPropertySame('fao', $this->fao);
    }

    /**
     * Test that the language is correctly stored in the object.
     */
    public function testDefaultLanguageSetCorrectly(): void
    {
        $this->assertPropertyEquals('default_language', 'en_US');
    }

    /**
     * Test that the language is correctly stored in the object.
     */
    public function testLocaleLocationSetCorrectly(): void
    {
        // /usr/bin/l10n by default
        $default_location = dirname($_SERVER['PHP_SELF']) . '/l10n';

        $this->assertPropertyEquals('locales_location', $default_location);
    }

    /**
     * Test initial call to get_supported_languages().
     *
     * @depends testLanguagesEmpty
     * @covers  Lunr\L10n\L10n::get_supported_languages
     */
    public function testInitialGetSupportedLanguages(): void
    {
        $this->fao->expects($this->once())
                  ->method('get_list_of_directories')
                  ->with(dirname($_SERVER['PHP_SELF']) . '/l10n')
                  ->willReturn([ 'de_DE', 'nl_NL' ]);

        $languages = $this->class->get_supported_languages();
        sort($languages);
        $this->assertEquals($this->languages, $languages);
    }

    /**
     * Test that get_supported_languages() populated $languages.
     *
     * @depends testInitialGetSupportedLanguages
     */
    public function testLanguagesPopulated(): void
    {
        $properties = $this->reflection->getStaticProperties();
        $languages  = $properties['languages'];
        sort($languages);
        $this->assertEquals($this->languages, $languages);
    }

    /**
     * Test get_supported_languages() when it was already executed before.
     *
     * @depends testLanguagesPopulated
     * @covers  Lunr\L10n\L10n::get_supported_languages
     */
    public function testCachedGetSupportedLanguages(): void
    {
        $this->fao->expects($this->never())
                  ->method('get_list_of_directories');

        $languages = $this->class->get_supported_languages();
        sort($languages);
        $this->assertEquals($this->languages, $languages);
    }

    /**
     * Test iso_to_posix() with supported languages.
     *
     * @param string $iso   ISO language definition
     * @param string $posix POSIX language definition
     *
     * @dataProvider supportedLanguagesProvider
     * @depends      testCachedGetSupportedLanguages
     * @requires     extension intl
     * @covers       Lunr\L10n\L10n::iso_to_posix
     */
    public function testIsoToPosixForSupportedLanguages($iso, $posix): void
    {
        $this->assertEquals($posix, $this->class->iso_to_posix($iso));
    }

    /**
     * Test iso_to_posix() with unsupported languages.
     *
     * @param string $iso ISO language definition
     *
     * @dataProvider unsupportedLanguagesProvider
     * @depends      testCachedGetSupportedLanguages
     * @requires     extension intl
     * @covers       Lunr\L10n\L10n::iso_to_posix
     */
    public function testIsoToPosixForUnsupportedLanguages($iso): void
    {
        $this->assertEquals('en_US', $this->class->iso_to_posix($iso));
    }

}

?>
