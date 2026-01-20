<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions;

use BytesCommerce\ZabbixApi\Enums\ZabbixAction;

final class User extends AbstractAction
{
    public static function getActionPrefix(): string
    {
        return 'user';
    }

    public function login(string $username, string $password): string
    {
        $result = $this->client->call(
            ZabbixAction::USER_LOGIN,
            ['username' => $username, 'password' => $password],
        );

        if (!is_string($result)) {
            throw new \RuntimeException('Invalid login response: expected string token');
        }

        return $result;
    }
}
