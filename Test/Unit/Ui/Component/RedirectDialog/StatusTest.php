<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStoreSwitcher\Test\Unit\Ui\Component\RedirectDialog;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Opengento\CountryStoreSwitcher\Ui\Component\RedirectDialog\Status;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Opengento\CountryStoreSwitcher\Ui\Component\RedirectDialog\Status
 */
class StatusTest extends TestCase
{
    /**
     * @var MockObject|ScopeConfigInterface
     */
    private $scopeConfig;

    private Status $status;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->getMockForAbstractClass(ScopeConfigInterface::class);

        $this->status = new Status($this->scopeConfig);
    }

    /**
     * @dataProvider helperData
     */
    public function testIsDisabled(bool $redirectStatus, bool $dialogStatus, bool $expected): void
    {
        $this->scopeConfig->expects($this->exactly(!$redirectStatus ? 2 : 1))
            ->method('isSetFlag')
            ->willReturnMap([
                ['country/redirect/enabled', 'store', null, $redirectStatus],
                ['country/redirect/dialog', 'store', null, $dialogStatus],
            ]);

        $this->assertSame($expected, $this->status->isDisabled());
    }

    public function helperData(): array
    {
        return [
            [true, false, true],
            [true, true, true],
            [false, true, false],
            [false, false, true],
        ];
    }
}
