<?php
/**
 * @category    pimcore-document-copier
 * @date        10/02/2020 09:51
 * @author      Pascal Dunaj <pdunaj@divante.pl>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

declare(strict_types=1);

namespace Divante\DocumentCopierBundle\ElementSerializer;

use Divante\DocumentCopierBundle\Exception\InvalidElementTypeException;
use Pimcore\Model\Document;
use Pimcore\Model\Document\PageSnippet;
use Pimcore\Model\Document\Snippet as DocumentSnippet;
use Pimcore\Model\Document\Editable;

/**
 * Class Snippet
 * @package Divante\DocumentCopierBundle\ElementSerializer
 */
class Snippet extends GenericType
{
    /**
     * @param Editable $element
     * @return mixed
     * @throws InvalidElementTypeException
     */
    public static function getData(Editable $element)
    {
        if ($element instanceof Tag\Snippet) {
            $snippet = DocumentSnippet::getById($element->getData());

            if (!$snippet) {
                // Snippet does not exist
                return null;
            }

            return $snippet->getRealFullPath();
        } else {
            throw new InvalidElementTypeException();
        }
    }

    /**
     * @param string $elementName
     * @param array $elementDto
     * @param PageSnippet $document
     */
    public static function setData(string $elementName, array $elementDto, PageSnippet $document): void
    {
        $snippet = Document::getByPath(strval($elementDto['data']));

        if (!$snippet) {
            return;
        }

        $document->setRawEditable($elementName, $elementDto['type'], $snippet->getId());
    }
}
