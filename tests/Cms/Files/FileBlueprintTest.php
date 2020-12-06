<?php

namespace Kirby\Cms;

class FileBlueprintTest extends TestCase
{
    public function testOptions()
    {
        $blueprint = new FileBlueprint([
            'model' => new File(['filename' => 'test.jpg'])
        ]);

        $expected = [
            'changeName' => null,
            'create'     => null,
            'delete'     => null,
            'read'       => null,
            'replace'    => null,
            'update'     => null,
        ];

        $this->assertEquals($expected, $blueprint->options());
    }

    public function testTemplateFromContent()
    {
        $file = new File([
            'filename' => 'test.jpg',
            'content' => [
                'template' => 'gallery'
            ]
        ]);

        $this->assertEquals('gallery', $file->template());
    }

    public function testCustomTemplate()
    {
        $file = new File([
            'filename' => 'test.jpg',
            'template' => 'gallery'
        ]);

        $this->assertEquals('gallery', $file->template());
    }

    public function testDefaultBlueprint()
    {
        $file = new File([
            'filename' => 'test.jpg',
            'template' => 'does-not-exist',
        ]);

        $blueprint = $file->blueprint();

        $this->assertInstanceOf(FileBlueprint::class, $blueprint);
    }

    public function testCustomBlueprint()
    {
        new App([
            'blueprints' => [
                'files/gallery' => [
                    'name'  => 'gallery',
                    'title' => 'Gallery',
                ]
            ]
        ]);

        $file = new File([
            'filename' => 'test.jpg',
            'template' => 'gallery',
        ]);

        $blueprint = $file->blueprint();

        $this->assertInstanceOf(FileBlueprint::class, $blueprint);
        $this->assertEquals('Gallery', $blueprint->title());
    }

    public function testAccept()
    {
        $file = new File([
            'filename' => 'test.jpg'
        ]);

        // string = MIME types
        $blueprint = new FileBlueprint([
            'accept' => 'image/jpeg, text/*',
            'model'  => $file
        ]);
        $this->assertSame([
            'extension'   => null,
            'mime'        => ['image/jpeg', 'text/*'],
            'maxheight'   => null,
            'maxsize'     => null,
            'maxwidth'    => null,
            'minheight'   => null,
            'minsize'     => null,
            'minwidth'    => null,
            'orientation' => null,
            'type'        => null
        ], $blueprint->accept());

        // empty value = no restrictions
        $expected = [
            'extension'   => null,
            'mime'        => null,
            'maxheight'   => null,
            'maxsize'     => null,
            'maxwidth'    => null,
            'minheight'   => null,
            'minsize'     => null,
            'minwidth'    => null,
            'orientation' => null,
            'type'        => null
        ];

        $blueprint = new FileBlueprint([
            'model' => $file
        ]);
        $this->assertSame($expected, $blueprint->accept());

        $blueprint = new FileBlueprint([
            'accept' => null,
            'model'  => $file
        ]);
        $this->assertSame($expected, $blueprint->accept());

        $blueprint = new FileBlueprint([
            'accept' => [],
            'model'  => $file
        ]);
        $this->assertSame($expected, $blueprint->accept());

        // array with mixed case
        $blueprint = new FileBlueprint([
            'accept' => [
                'extensION' => ['txt'],
                'MiMe'      => ['image/jpeg', 'text/*'],
                'MAXsize'   => 100,
                'typE'      => ['document']
            ],
            'model' => $file
        ]);
        $this->assertSame([
            'extension'   => ['txt'],
            'mime'        => ['image/jpeg', 'text/*'],
            'maxheight'   => null,
            'maxsize'     => 100,
            'maxwidth'    => null,
            'minheight'   => null,
            'minsize'     => null,
            'minwidth'    => null,
            'orientation' => null,
            'type'        => ['document']
        ], $blueprint->accept());

        // MIME, extension and type normalization
        $blueprint = new FileBlueprint([
            'accept' => [
                'mime'      => 'image/jpeg,  image/png;q=0.7',
                'extension' => 'txt,json  ,  jpg',
                'type'      => 'document;audio  ,  video'
            ],
            'model' => $file
        ]);
        $this->assertSame([
            'extension'   => ['txt', 'json', 'jpg'],
            'mime'        => ['image/jpeg', 'image/png'],
            'maxheight'   => null,
            'maxsize'     => null,
            'maxwidth'    => null,
            'minheight'   => null,
            'minsize'     => null,
            'minwidth'    => null,
            'orientation' => null,
            'type'        => ['document;audio', 'video']
        ], $blueprint->accept());
    }

    public function testAcceptMime()
    {
        $file = new File([
            'filename' => 'test.jpg'
        ]);

        // no restrictions
        $blueprint = new FileBlueprint([
            'model' => $file
        ]);
        $this->assertSame('*', $blueprint->acceptMime());

        // just MIME restrictions
        $blueprint = new FileBlueprint([
            'accept' => 'image/jpeg,  image/png;q=0.7',
            'model'  => $file
        ]);
        $this->assertSame('image/jpeg, image/png', $blueprint->acceptMime());

        // just extension restrictions
        $blueprint = new FileBlueprint([
            'accept' => [
                'extension' => 'jpg, mp4'
            ],
            'model' => $file
        ]);
        $this->assertSame('image/jpeg, video/mp4', $blueprint->acceptMime());

        // just type restrictions
        $blueprint = new FileBlueprint([
            'accept' => [
                'type' => 'archive, audio'
            ],
            'model' => $file
        ]);
        $this->assertSame(
            'application/x-gzip, application/x-tar, application/x-zip, ' .
            'audio/x-aiff, audio/mp4, audio/midi, audio/mpeg, audio/x-wav',
            $blueprint->acceptMime()
        );

        // combined extension and type restrictions
        $blueprint = new FileBlueprint([
            'accept' => [
                'extension' => 'jpg, txt, png',
                'type'      => 'image, audio'
            ],
            'model' => $file
        ]);
        $this->assertSame('image/jpeg, image/png', $blueprint->acceptMime());

        // don't override explicit MIME types with other restrictions
        $blueprint = new FileBlueprint([
            'accept' => [
                'mime'      => 'image/jpeg,  application/pdf;q=0.7',
                'extension' => 'jpg, txt, png',
                'type'      => 'document, image'
            ],
            'model' => $file
        ]);
        $this->assertSame('image/jpeg, application/pdf', $blueprint->acceptMime());
    }

    public function testExtendAccept()
    {
        new App([
            'roots' => [
                'index' => '/dev/null'
            ],
            'blueprints' => [
                'files/base' => [
                    'name'  => 'base',
                    'title' => 'Base',
                    'accept' => [
                        'mime' => 'image/jpeg'
                    ]
                ],
                'files/image' => [
                    'name'    => 'image',
                    'title'   => 'Image',
                    'extends' => 'files/base'
                ]
            ]
        ]);

        $file = new File([
            'filename' => 'test.jpg',
            'template' => 'image',
        ]);

        $blueprint = $file->blueprint();
        $this->assertEquals(['image/jpeg'], $blueprint->accept()['mime']);
    }
}
