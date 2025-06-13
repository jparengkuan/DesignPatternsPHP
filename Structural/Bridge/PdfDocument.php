<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Bridge;

/**
 * Concrete implementation of a media file that represents an uploaded PDF document.
 *
 * Functions as the *Refined Abstraction* in the Bridge pattern, off‑loading
 * storage details to the {@see MediaFile} implementation layer while providing
 * PDF‑specific rendering logic.
 *
 * @package DesignPatterns\Structural\Bridge
 */
class PdfDocument extends MediaFile
{
    /**
     * Produces the HTML anchor tag used to download or open the PDF in a new tab.
     *
     * @return string HTML <a> element with the proper `href` and `target` attributes.
     */
    public function render(): string
    {
        return '<a href="/media/' . $this->path . '" target="_blank">Download PDF</a>';
    }
}
