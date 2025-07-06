<?php

declare(strict_types=1);

namespace OCA\Mail\Settings;

use OCP\Settings\ISettings;
use OCP\Template;

class SupabaseSettings implements ISettings {

    public function getForm(): Template {
        // You can use a template for your settings form
        return new Template('mail', 'admin-supabase');
    }

    public function getSection(): string {
        return 'additional-settings';
    }

    public function getPriority(): int {
        return 10;
    }
}
