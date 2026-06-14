<?php

declare(strict_types=1);

namespace Tests\Support\Helper;

class MediaBuyersFixture extends \Codeception\Module
{
    public $mbId;
    public $initials;
    public $name;
    public $email;
    public $slackUserId;
    public $active;

    public function __construct($mbId, $initials, $name, $email, $slackUserId, $active)
    {
        $this->mbId = $mbId;
        $this->initials = $initials;
        $this->name = $name;
        $this->email = $email;
        $this->slackUserId = $slackUserId;
        $this->active = $active;
    }
}
