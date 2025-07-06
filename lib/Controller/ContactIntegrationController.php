<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2021 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Mail\Controller;

use OCA\Mail\Http\TrapError;
use OCA\Mail\Service\ContactIntegration\ContactIntegrationService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\OpenAPI;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

#[OpenAPI(scope: OpenAPI::SCOPE_IGNORE)]
class ContactIntegrationController extends Controller {
	private ContactIntegrationService $service;

	public function __construct(string $appName,
		IRequest $request,
		ContactIntegrationService $service) {
		parent::__construct($appName, $request);

		$this->service = $service;
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param string $mail
	 * @return JSONResponse
	 */
	#[TrapError]
	public function match(string $mail): JSONResponse {
		return (new JSONResponse($this->service->findMatches($mail)))->cacheFor(60 * 60, false, true);
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param string $uid
	 * @param string $mail
	 * @return JSONResponse
	 */
	#[TrapError]
	public function addMail(?string $uid = null, ?string $mail = null): JSONResponse {
		$res = $this->service->addEMailToContact($uid, $mail);
		if ($res === null) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		}
		return new JSONResponse($res);
	}

	/**
	 * @NoAdminRequired
	 */
	#[TrapError]
	public function newContact(?string $contactName = null, ?string $mail = null): JSONResponse {
		$res = $this->service->newContact($contactName, $mail);
		if ($res === null) {
			return new JSONResponse([], Http::STATUS_NOT_ACCEPTABLE);
		}
		return new JSONResponse($res);
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param string $term
	 * @return JSONResponse
	 */
	#[TrapError]
	public function autoComplete(string $term): JSONResponse {
<<<<<<< HEAD
		$res = $this->service->autoComplete($term);
		return (new JSONResponse($res))->cacheFor(60 * 60, false, true);
=======
		$cached = $this->cache->get($this->uid . $term);
		if ($cached !== null) {
			$decoded = json_decode($cached, true);
			if ($decoded !== null) {
				return new JSONResponse($decoded);
			}
		}
		$res = $this->service->autoComplete($term);
		$this->cache->set($this->uid . $term, json_encode($res), 24 * 3600);
		return new JSONResponse($res);
>>>>>>> 5d13aacd343883b2c7ace01db7280a0664c0a6e4
	}
}
