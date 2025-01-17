<?php
/**
 * @category    pimcore-document-copier
 * @date        10/02/2020 09:51
 * @author      Pascal Dunaj <pdunaj@divante.pl>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

declare(strict_types=1);

namespace Divante\DocumentCopierBundle\ElementSerializer;

use Pimcore\Model\Document;
use Pimcore\Model\Document\PageSnippet;
use Pimcore\Model\Document\Tag;

/**
 * Class Wysiwyg
 * @package Divante\DocumentCopierBundle\ElementSerializer
 */
class Wysiwyg
{
    /**
     * @param Tag $element
     * @return mixed
     */
    public static function getData(Tag $element)
    {
        // Default that works for simple fields
        // To be subclassed
        return $element->getData();
    }

    /**
     * @param string $elementName
     * @param array $elementDto
     * @param PageSnippet $document
     */
    public static function setData(string $elementName, array $elementDto, PageSnippet $document): void
    {
        // Default that works for simple fields
        // To be subclassed
        $element = Document\Tag::factory($elementDto['type'], $elementName, $document->getId());

        if ($elementDto['data']) {
            $pregImgMatches = [];
            preg_match_all('/(<img[^>]+>)/i', $elementDto['data'], $pregImgMatches);

            foreach ($pregImgMatches as $imgMatches) {
                if (!$imgMatches) {
                    continue;
                }

                foreach ($imgMatches as $match) {
                    $replaceIds = [];

                    preg_match('/pimcore_id="(.*?)"/s', $match, $assetIdMatch);
                    preg_match('/src="(.*?)"/s', $match, $assetPathMatch);

                    $explodedAssetPathMatch = explode('/', $assetPathMatch[1]);

                    foreach ($explodedAssetPathMatch as $assetPathMatchPartKey => $assetPathMatchPart) {
                        if (strpos($assetPathMatchPart, 'image-thumb__') !== false) {
                            unset($explodedAssetPathMatch[$assetPathMatchPartKey]);
                        }
                    }

                    $assetPath = implode('/', $explodedAssetPathMatch);

                    $asset = \Pimcore\Model\Asset\Image::getByPath($assetPath);

                    if ($asset) {
                        $replaceIds[] = [
                            'old' => $assetIdMatch[0],
                            'new' => 'pimcore_id="' . $asset->getId() . '"',
                        ];
                    }

                    if ($replaceIds) {
                        foreach ($replaceIds as $replaceId) {
                            $elementDto['data'] = str_replace(
                                $replaceId['old'],
                                $replaceId['new'],
                                $elementDto['data']
                            );
                        }
                    }
                }
            }
        }

        $element->setDataFromResource($elementDto['data']);

        $document->setElement($elementName, $element);
    }
}
