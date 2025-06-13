<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Bridge;

/**
 * Concrete implementation of a media file that represents an uploaded image.
 *
 * Acts as the *Refined Abstraction* in the Bridge pattern, delegating storage
 * specifics to the implementation layer exposed by {@see MediaFile}.
 *
 * @package DesignPatterns\Structural\Bridge
 */
class ImageFile extends MediaFile
{
    /**
     * Produces the HTML required to embed the image on a web page.
     *
     * @return string HTML <img> tag with the correct `src` attribute pointing at
     *                the public media directory.
     */
    public function render(): string
    {
        return '<img src="/media/' . $this->path . '" alt="uploaded image">';
    }
}
