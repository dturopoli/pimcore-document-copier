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
use Pimcore\Model\Document\Editable;

/**
 * Class Date
 * @package Divante\DocumentCopierBundle\ElementSerializer
 */
class Date extends GenericType
{
    /**
     * @param Editable $element
     * @return mixed
     * @throws InvalidElementTypeException
     */
    public static function getData(Editable $element)
    {
        if ($element instanceof Editable\Date) {
            return $element->getDate() ? $element->getDate->getTimestamp() : null;
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
        $document->setRawEditable($elementName, $elementDto['type'], $elementDto['data']);
    }
}
