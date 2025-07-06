<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OCA\Mail\Send;

use Horde_Imap_Client_Exception;
use Horde_Imap_Client_Socket;
use OCA\Mail\Account;
use OCA\Mail\Db\LocalMessage;
use OCA\Mail\Db\MailboxMapper;
use OCA\Mail\IMAP\MessageMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use Psr\Log\LoggerInterface;

class CopySentMessageHandler extends AHandler {
<<<<<<< HEAD
	public function __construct(private IMAPClientFactory $imapClientFactory,
=======
	public function __construct(
>>>>>>> 5d13aacd343883b2c7ace01db7280a0664c0a6e4
		private MailboxMapper $mailboxMapper,
		private LoggerInterface $logger,
		private MessageMapper $messageMapper,
	) {
	}

	#[\Override]
	public function process(
		Account $account,
		LocalMessage $localMessage,
		Horde_Imap_Client_Socket $client,
	): LocalMessage {
		if ($localMessage->getStatus() === LocalMessage::STATUS_PROCESSED) {
			return $this->processNext($account, $localMessage, $client);
		}

		$rawMesage = $localMessage->getRaw();
		if ($rawMesage === null) {
			$localMessage->setStatus(LocalMessage::STATUS_IMAP_SENT_MAILBOX_FAIL);
			return $localMessage;
		}

		$sentMailboxId = $account->getMailAccount()->getSentMailboxId();
		if ($sentMailboxId === null) {
			// We can't write the "sent mailbox" status here bc that would trigger an additional send.
			// Thus, we leave the "imap copy to sent mailbox" status.
			$localMessage->setStatus(LocalMessage::STATUS_IMAP_SENT_MAILBOX_FAIL);
			$this->logger->warning("No sent mailbox exists, can't save sent message");
			return $localMessage;
		}

		// Save the message in the sent mailbox
		try {
			$sentMailbox = $this->mailboxMapper->findById(
				$sentMailboxId
			);
		} catch (DoesNotExistException $e) {
			// We can't write the "sent mailbox" status here bc that would trigger an additional send.
			// Thus, we leave the "imap copy to sent mailbox" status.
			$localMessage->setStatus(LocalMessage::STATUS_IMAP_SENT_MAILBOX_FAIL);
			$this->logger->error('Sent mailbox could not be found', [
				'exception' => $e,
			]);

			return $localMessage;
		}

		try {
			$this->messageMapper->save(
				$client,
				$sentMailbox,
				$rawMesage,
			);
			$localMessage->setStatus(LocalMessage::STATUS_PROCESSED);
		} catch (Horde_Imap_Client_Exception $e) {
			$localMessage->setStatus(LocalMessage::STATUS_IMAP_SENT_MAILBOX_FAIL);
			$this->logger->error('Could not copy message to sent mailbox', [
				'exception' => $e,
			]);
			return $localMessage;
		}

		return $this->processNext($account, $localMessage, $client);
	}
}
