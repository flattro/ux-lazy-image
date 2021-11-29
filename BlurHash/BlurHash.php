<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\UX\LazyImage\BlurHash;

use Intervention\Image\ImageManager;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @final
 * @experimental
 */
class BlurHash implements BlurHashInterface
{
    private $imageManager;

    public function __construct(ImageManager $imageManager = null)
    {
        $this->imageManager = $imageManager;
    }

    public function createDataUriThumbnail(string $filename, int $width, int $height, int $encodingWidth = 75, int $encodingHeight = 75): string
    {
        // Resize and encode
        $encoded = $this->encode($filename, $encodingWidth, $encodingHeight);

        if (!$encoded) {
            // Base 64 encoded SVG icon of a broken image
            return 'data:image/svg+xml;base64,PHN2ZyBoZWlnaHQ9JzMwMHB4JyB3aWR0aD0nMzAwcHgnICBmaWxsPSIjMDAwMDAwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2ZXJzaW9uPSIxLjEiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgMTI4LjIgMTIwLjIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDEyOC4yIDEyMC4yOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PHBhdGggZD0iTTExMS44LDU5LjFjLTAuMy0wLjUtMC43LTAuOC0xLjItMC45bC05LjgtMi41Yy0xLjEtMC4zLTIuMiwwLjQtMi40LDEuNWwtMi4yLDguOUw4Ny43LDY0YzAsMCwwLDAsMCwwbC0wLjQtMC4xICAgYy0wLjUtMC4xLTEuMSwwLTEuNSwwLjJjLTAuNSwwLjMtMC44LDAuNy0wLjksMS4ybC0yLjIsOC45bC04LjktMi4yYy0xLjEtMC4zLTIuMiwwLjQtMi40LDEuNUw2OSw4Mi4zbC04LjktMi4yICAgYy0wLjUtMC4xLTEuMSwwLTEuNSwwLjJjLTAuNSwwLjMtMC44LDAuNy0wLjksMS4ybC0wLjIsMC44YzAsMCwwLDAsMCwwbC0yLjMsOS40Yy0wLjMsMS4xLDAuNCwyLjIsMS41LDIuNGwzOC44LDkuNyAgIGMwLjUsMC4xLDAuOSwwLjIsMS40LDAuMmMyLjUsMCw0LjgtMS43LDUuNC00LjJsOS44LTM5LjJDMTEyLjIsNjAuMSwxMTIuMSw1OS42LDExMS44LDU5LjF6IE03NC43LDc2LjNsOC43LDIuMiAgIGMwLjUsMC4yLDEuMiwwLjEsMS43LTAuMmMwLjUtMC4zLDAuOS0wLjksMC45LTEuNWwxLjctNi45bDUuNiw3LjRsLTIuOCwxMS4ybC0xNy44LTQuNUw3NC43LDc2LjN6IE05OC40LDk4LjggICBjLTAuMiwwLjktMS4xLDEuNC0xLjksMS4ybC0zNi45LTkuMmwxLjQtNS41bDMwLjYsNy43YzAuMSwwLDAuMywwLjEsMC40LDAuMWMwLDAsMCwwLDAsMGMwLDAsMC4xLDAsMC4xLDBjMC4xLDAsMC4yLDAsMC40LTAuMSAgIGMwLDAsMC4xLDAsMC4xLDBjMC4yLDAsMC4zLTAuMSwwLjQtMC4yYzAuNS0wLjMsMC44LTAuNywwLjktMS4ybDMuNS0xNC4xYzAuMS0wLjYsMC0xLjItMC4zLTEuN2wtNC45LTYuNGw0LjgsMS4yICAgYzEuMSwwLjMsMi4yLTAuNCwyLjQtMS41bDIuMi04LjlsNiwxLjVMOTguNCw5OC44eiI+PC9wYXRoPjxwYXRoIGQ9Ik01Ny41LDc1LjljMS4xLDAsMi0wLjksMi0ybDAtOS4yaDkuMWMxLjEsMCwyLTAuOSwyLTJ2LTkuMWg5LjFjMS4xLDAsMi0wLjksMi0ybDAtOS4yaDguMmMxLjEsMCwyLTAuOSwyLTJWMjIuNyAgIGMwLTMuMS0yLjUtNS42LTUuNi01LjZIMjIuNmMtMy4xLDAtNS42LDIuNS01LjYsNS42djU4LjJjMCwzLjEsMi41LDUuNiw1LjYsNS42aDIzLjdjMS4xLDAsMi0wLjksMi0ydi04LjVMNTcuNSw3NS45eiBNNjYuNiw2MC43ICAgaC05LjFjLTEuMSwwLTIsMC45LTIsMmwwLDkuMmwtMjQuNywwbDAtNi4xbDcuOC01LjlsNi44LDMuOWMwLjgsMC40LDEuNywwLjMsMi40LTAuM2wxNC45LTE0LjRsMy45LDMuMVY2MC43eiBNNDQuMyw4Mi40SDIyLjYgICBjLTAuOSwwLTEuNi0wLjctMS42LTEuNlYyMi43YzAtMC45LDAuNy0xLjYsMS42LTEuNmg2My43YzAuOSwwLDEuNiwwLjcsMS42LDEuNnYxNS44aC04LjFjLTEuMSwwLTIsMC45LTIsMmwwLDkuMmgtOGwtNS45LTQuNyAgIGMtMC44LTAuNi0xLjktMC42LTIuNiwwLjFMNDYuMSw1OS42bC02LjYtMy44Yy0wLjctMC40LTEuNi0wLjQtMi4yLDAuMWwtOS43LDcuM2MtMC41LDAuNC0wLjgsMS0wLjgsMS42bDAsOS4xYzAsMCwwLDAsMCwwbDAsMC4xICAgYzAsMC41LDAuMiwxLDAuNiwxLjRjMC40LDAuNCwwLjksMC42LDEuNCwwLjZoMTUuNVY4Mi40eiI+PC9wYXRoPjxwYXRoIGQ9Ik0zNy4zLDQ3LjRjNSwwLDktNC4xLDktOWMwLTUtNC4xLTktOS05Yy01LDAtOSw0LjEtOSw5QzI4LjIsNDMuMywzMi4zLDQ3LjQsMzcuMyw0Ny40eiBNMzcuMywzMy4zYzIuOCwwLDUsMi4zLDUsNSAgIGMwLDIuOC0yLjMsNS01LDVjLTIuOCwwLTUtMi4zLTUtNUMzMi4yLDM1LjUsMzQuNSwzMy4zLDM3LjMsMzMuM3oiPjwvcGF0aD48L2c+PC9zdmc+';
        }

        // Create a new blurred thumbnail from encoded BlurHash
        $pixels = \kornrunner\Blurhash\Blurhash::decode($encoded, $width, $height);

        $thumbnail = $this->imageManager->canvas($width, $height);
        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                $thumbnail->pixel($pixels[$y][$x], $x, $y);
            }
        }

        return 'data:image/jpeg;base64,'.base64_encode($thumbnail->encode('jpg', 80));
    }

    public function encode(string $filename, int $encodingWidth = 75, int $encodingHeight = 75): string
    {
        if (!$this->imageManager) {
            throw new \LogicException("Missing package, to use the \"blur_hash\" Twig function, run:\n\ncomposer require intervention/image");
        }

        try {
            // Resize image to increase encoding performance
            $image = $this->imageManager->make(file_get_contents($filename));
            $image->resize($encodingWidth, $encodingHeight, static function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Encode using BlurHash
            $width = $image->getWidth();
            $height = $image->getHeight();

            $pixels = [];
            for ($y = 0; $y < $height; ++$y) {
                $row = [];
                for ($x = 0; $x < $width; ++$x) {
                    $color = $image->pickColor($x, $y);
                    $row[] = [$color[0], $color[1], $color[2]];
                }

                $pixels[] = $row;
            }

            return \kornrunner\Blurhash\Blurhash::encode($pixels, 4, 3);
        } catch(\Exception $e) {
            return false;
        }
    }
}
