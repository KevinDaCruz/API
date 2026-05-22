<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    #[Test]
    public function uses_professional_email_returns_true_for_company_domain(): void
    {
        $user = new User();
        $user->email = 'john@entreprise.com';

        $this->assertTrue($user->usesProfessionalEmail());
    }

    #[Test]
    public function uses_professional_email_returns_false_for_gmail(): void
    {
        $user = new User();
        $user->email = 'john@gmail.com';

        $this->assertFalse($user->usesProfessionalEmail());
    }
}
