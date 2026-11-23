<?php

namespace tests\codeception\unit;

use Codeception\Test\Unit;
use humhub\modules\mail\widgets\NewMessageButton;

class NewMessageButtonTest extends Unit
{
    public function testIconOnlyButtonIsNamedByItsTooltip()
    {
        $html = NewMessageButton::widget(['id' => 'create-message-button', 'icon' => 'plus', 'label' => '']);

        $this->assertStringContainsString('aria-label="New message"', $html);
        $this->assertStringContainsString('data-bs-title="New message"', $html);
        $this->assertStringContainsString('btn-icon-only', $html);
    }

    public function testLabelledButtonShowsItsLabel()
    {
        $html = NewMessageButton::widget(['icon' => 'plus']);

        $this->assertStringContainsString('Message</button>', $html);
        $this->assertStringNotContainsString('aria-label', $html);
    }
}
