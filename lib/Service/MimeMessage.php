<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2022 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Mail\Service;

use DOMDocument;
use DOMElement;
use DOMNode;
use Horde_Mime_Part;
use Horde_Text_Filter;
use OCA\Mail\Exception\InvalidDataUriException;
use OCA\Mail\Html\Parser;
use OCA\Mail\Service\DataUri\DataUriParser;

class MimeMessage {
	private DataUriParser $uriParser;

	public function __construct(DataUriParser $uriParser) {
		$this->uriParser = $uriParser;
	}

	/**
	 * generates mime message
	 *
	 * @param string $contentPlain
	 * @param string $contentHtml
	 * @param Horde_Mime_Part[] $attachments
	 *
	 * @return Horde_Mime_Part
	 */
<<<<<<< HEAD
	public function build(bool $isHtml, string $content, array $attachments): Horde_Mime_Part {
		if ($isHtml) {
			$imageParts = [];
			if (empty($content)) {
				$htmlContent = $textContent = $content;
			} else {
				$source = '<html><meta http-equiv="content-type" content="text/html; charset=UTF-8"><body>' . $content . '</body>';

				$doc = new DOMDocument();
				$doc->loadHTML($source, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);

				$images = $doc->getElementsByTagName('img');

				for ($i = 0; $i < $images->count(); $i++) {
					$image = $images->item($i);
					if (!($image instanceof DOMElement)) {
						continue;
					}

					$src = $image->getAttribute('src');
					if ($src === '') {
						continue;
					}

					try {
						$dataUri = $this->uriParser->parse($src);
					} catch (InvalidDataUriException $e) {
						continue;
					}

					$part = new Horde_Mime_Part();
					$part->setType($dataUri->getMediaType());
					$part->setCharset($dataUri->getParameters()['charset']);
					$part->setName('embedded_image_' . $i);
					$part->setDisposition('inline');
					if ($dataUri->isBase64()) {
						$part->setTransferEncoding('base64');
					}
					$part->setContents($dataUri->getData());

					$cid = $part->setContentId();
					$imageParts[] = $part;

					$image->setAttribute('src', 'cid:' . $cid);
				}

				$htmlContent = $doc->saveHTML();
				$textContent = Horde_Text_Filter::filter($htmlContent, 'Html2text', ['callback' => [$this, 'htmlToTextCallback']]);
			}

			$alternativePart = new Horde_Mime_Part();
			$alternativePart->setType('multipart/alternative');

			$htmlPart = new Horde_Mime_Part();
			$htmlPart->setType('text/html');
			$htmlPart->setCharset('UTF-8');
			$htmlPart->setContents($htmlContent);
			$htmlPart->setDescription('HTML Version of Message');

			$textPart = new Horde_Mime_Part();
			$textPart->setType('text/plain');
			$textPart->setCharset('UTF-8');
			$textPart->setContents($textContent);
			$textPart->setDescription('Plaintext Version of Message');
=======
	public function build(?string $contentPlain, ?string $contentHtml, array $attachments, bool $isPgpEncrypted = false): Horde_Mime_Part {
>>>>>>> 5d13aacd343883b2c7ace01db7280a0664c0a6e4

		if ($isPgpEncrypted === true && isset($contentPlain)) {
			$basePart = $this->buildPgpPart($contentPlain);
		} elseif (count($attachments) > 0) {
			/*
<<<<<<< HEAD
			 * RFC1341: In general, user agents that compose multipart/alternative entities should place the
			 * body parts in increasing order of preference, that is, with the preferred format last.
			 */
			$alternativePart[] = $textPart;
			$alternativePart[] = $htmlPart;

			/*
			 * Wrap the multipart/alternative parts in multipart/related when inline images are found.
			 */
			if (count($imageParts) > 0) {
				$bodyPart = new Horde_Mime_Part();
				$bodyPart->setType('multipart/related');
				$bodyPart[] = $alternativePart;
				foreach ($imageParts as $imagePart) {
					$bodyPart[] = $imagePart;
				}
			} else {
				$bodyPart = $alternativePart;
			}
		} else {
			$bodyPart = new Horde_Mime_Part();
			$bodyPart->setType('text/plain');
			$bodyPart->setCharset('UTF-8');
			$bodyPart->setContents($content);
		}

		/*
		 * For attachments wrap the body (multipart/related, multipart/alternative or text/plain) in
		 * a multipart/mixed part.
		 */
		if (count($attachments) > 0) {
=======
			* Messages with non embedded attachments need to be wrap in a multipart/mixed part
			*/
>>>>>>> 5d13aacd343883b2c7ace01db7280a0664c0a6e4
			$basePart = new Horde_Mime_Part();
			$basePart->setType('multipart/mixed');
			$basePart[] = $this->buildMessagePart($contentPlain, $contentHtml);
			foreach ($attachments as $attachment) {
				$basePart[] = $attachment;
			}
		} else {
			$basePart = $this->buildMessagePart($contentPlain, $contentHtml);
		}

		$basePart->isBasePart(true);

		return $basePart;
	}

	/**
	 * generates html/plain message part
	 *
	 * @return Horde_Mime_Part
	 */
	private function buildMessagePart(?string $contentPlain, ?string $contentHtml): Horde_Mime_Part {

		if (isset($contentHtml)) {

			// determine if content is wrapped properly in a html tag, otherwise we need to wrap it properly
			if (mb_strpos($contentHtml, '<html') === false) {
				$source = '<!DOCTYPE html><html><meta http-equiv="content-type" content="text/html; charset=UTF-8"><body>' . PHP_EOL . $contentHtml . PHP_EOL . '</body>';
			} else {
				$source = ' ' . $contentHtml;
			}

			// determine if content has any embedded images
			$embeddedParts = [];
			$doc = Parser::parseToDomDocument($source);
			foreach ($doc->getElementsByTagName('img') as $id => $image) {
				if (!($image instanceof DOMElement)) {
					continue;
				}

				$src = $image->getAttribute('src');
				if ($src === '') {
					continue;
				}
				try {
					$dataUri = $this->uriParser->parse($src);
				} catch (InvalidDataUriException $e) {
					continue;
				}

				$part = new Horde_Mime_Part();
				$part->setType($dataUri->getMediaType());
				$part->setCharset($dataUri->getParameters()['charset']);
				$part->setName('embedded_image_' . $id);
				$part->setDisposition('inline');
				if ($dataUri->isBase64()) {
					$part->setTransferEncoding('base64');
				}
				$part->setContents($dataUri->getData());

				$cid = $part->setContentId();
				$embeddedParts[] = $part;

				$image->setAttribute('src', 'cid:' . $cid);
			}
			$htmlContent = $doc->saveHTML();

			$htmlPart = new Horde_Mime_Part();
			$htmlPart->setType('text/html');
			$htmlPart->setCharset('UTF-8');
			$htmlPart->setContents($htmlContent);
		}

		if (isset($contentPlain)) {
			$plainPart = new Horde_Mime_Part();
			$plainPart->setType('text/plain');
			$plainPart->setCharset('UTF-8');
			$plainPart->setContents($contentPlain);
		} elseif (!isset($contentPlain) && isset($contentHtml)) {
			$plainPart = new Horde_Mime_Part();
			$plainPart->setType('text/plain');
			$plainPart->setCharset('UTF-8');
			$plainPart->setContents(
				Horde_Text_Filter::filter($contentHtml, 'Html2text', ['callback' => [$this, 'htmlToTextCallback']])
			);
		}

		if (isset($plainPart, $htmlPart)) {
			/*
			* RFC1341: Multipart/alternative entities should place the body parts in
			* increasing order of preference, that is, with the preferred format last.
			*/
			$messagePart = new Horde_Mime_Part();
			$messagePart->setType('multipart/alternative');
			$messagePart[] = $plainPart;
			$messagePart[] = $htmlPart;
		} elseif (isset($htmlPart)) {
			$messagePart = $htmlPart;
		} elseif (isset($plainPart)) {
			$messagePart = $plainPart;
		} else {
			$messagePart = new Horde_Mime_Part();
		}

		if (isset($embeddedParts) && count($embeddedParts) > 0) {
			/*
			 * Text parts with embedded content (e.g. inline images, etc) need be wrapped in multipart/related part
			 */
			$basePart = new Horde_Mime_Part();
			$basePart->setType('multipart/related');
			$basePart[] = $messagePart;
			foreach ($embeddedParts as $part) {
				$basePart[] = $part;
			}
		} else {
			$basePart = $messagePart;
		}

		return $basePart;
	}

	/**
	 * generates pgp encrypted message part
	 *
	 * @param string $content
	 *
	 * @return Horde_Mime_Part
	 */
	private function buildPgpPart(string $content): Horde_Mime_Part {

		$contentPart = new Horde_Mime_Part();
		$contentPart->setType('application/octet-stream');
		$contentPart->setContentTypeParameter('name', 'encrypted.asc');
		$contentPart->setTransferEncoding('7bit');
		$contentPart->setDisposition('inline');
		$contentPart->setDispositionParameter('filename', 'encrypted.asc');
		$contentPart->setDescription('OpenPGP encrypted message');
		$contentPart->setContents($content);

		$pgpIdentPart = new Horde_Mime_Part();
		$pgpIdentPart->setType('application/pgp-encrypted');
		$pgpIdentPart->setTransferEncoding('7bit');
		$pgpIdentPart->setDescription('PGP/MIME Versions Identification');
		$pgpIdentPart->setContents('Version: 1');

		$basePart = new Horde_Mime_Part();
		$basePart->setType('multipart/encrypted');
		$basePart->setContentTypeParameter('protocol', 'application/pgp-encrypted');
		$basePart[] = $pgpIdentPart;
		$basePart[] = $contentPart;

		return $basePart;

	}

	/**
	 * A callback for Horde_Text_Filter.
	 *
	 * The purpose of this callback is to overwrite the default behavior
	 * of html2text filter to convert <p>Hello</p> => Hello\n\n with
	 * <p>Hello</p> => Hello\n.
	 *
	 * @param DOMDocument $doc
	 * @param DOMNode $node
	 * @return string|null non-null, add this text to the output and skip further processing of the node.
	 */
	public function htmlToTextCallback(DOMDocument $doc, DOMNode $node) {
		if ($node instanceof DOMElement && strtolower($node->tagName) === 'p') {
			return $node->textContent . "\n";
		}

		return null;
	}
}
