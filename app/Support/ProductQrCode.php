<?php

namespace App\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class ProductQrCode
{
    /**
     * Generate an SVG QR code for the given URL.
     */
    public static function svgForUrl(string $url): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(200, 0),
            new SvgImageBackEnd
        );

        $writer = new Writer($renderer);

        return $writer->writeString($url);
    }

    /**
     * Generate an SVG QR code for a product.
     */
    public static function svg($product): string
    {
        return self::svgForUrl(self::url($product));
    }

    /**
     * Generate the URL that the QR code points to: the public product detail page.
     */
    public static function url($product): string
    {
        return route('shop.show', $product);
    }
}
