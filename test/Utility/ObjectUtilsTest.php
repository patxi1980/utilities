<?php

namespace JimmyOak\Test\Utility;

use JimmyOak\Test\Value\DummyClass;
use JimmyOak\Utility\ObjectUtils;

class ObjectUtilsTest extends UtilsBaseTest
{
    /** @var ObjectUtils */
    protected $utils;

    private $expectedParsedXml;
    private $expectedParsedXmlString;
    private $objectToParse;
    private $objectParsedIntoArray = [
        'details' => [
            'media' => [
                'image' => [
                    'anImage.png',
                    'anotherImage.png',
                ],
                'video' => 'aVideo.mp4',
                'audio' => [],
            ]
        ]
    ];

    protected function setUp()
    {
        $this->expectedParsedXmlString = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<details>' .
            '<media>' .
            '<image>anImage.png</image>' .
            '<image>anotherImage.png</image>' .
            '<video>aVideo.mp4</video>' .
            '<audio/>' .
            '</media>' .
            '</details>';

        $this->prepareObjectToParse();

        $this->expectedParsedXml = simplexml_load_string($this->expectedParsedXmlString);

        $this->utils = ObjectUtils::instance();
    }

    /** @test */
    public function transformsObjectIntoArray()
    {
        $result = $this->utils->toArray($this->objectToParse);

        $this->assertSame($this->objectParsedIntoArray, $result);
    }

    /** @test */
    public function transformsObjectToXmlString()
    {
        $result = $this->utils->toXmlString($this->objectToParse);

        $this->assertSame($this->expectedParsedXmlString, $result);
    }

    /** @test */
    public function transformsObjectToXml()
    {
        $result = $this->utils->toXml($this->objectToParse);

        $this->assertEquals($this->expectedParsedXml, $result);
    }

    /** @test */
    public function transformsObjectToArrayDeeply()
    {
        $object = new DummyClass();
        $expected = [
            'aPrivateProperty' => 'A STRING',
            'aProtectedProperty' => 1234,
            'aPublicProperty' => 'ANOTHER STRING',
            'anObject' => [
                'aValue' => 'Jimmy',
                'anotherValue' => 'Kane',
                'oneMoreValue' => 'Oak',
            ],
            'anArrayOfObjects' => [
                [
                    'aValue' => 'Jimmy',
                    'anotherValue' => 'Kane',
                    'oneMoreValue' => 'Oak',
                ],
                [
                    'aValue' => 'Jimmy',
                    'anotherValue' => 'Kane',
                    'oneMoreValue' => 'Oak',
                ]
            ],
            'aParentProperty' => 5,
        ];

        $objectAsArray = $this->utils->toArray($object, true);

        $this->assertSame($expected, $objectAsArray);
    }

    private function prepareObjectToParse()
    {
        $details = new \stdClass();

        $media = new \stdClass();
        $media->image = ['anImage.png', 'anotherImage.png'];
        $media->video = 'aVideo.mp4';
        $media->audio = [];

        $details->media = $media;

        $this->objectToParse = new \stdClass();
        $this->objectToParse->details = $details;
    }
}
