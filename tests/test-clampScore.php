<?php
/**
 * Tests for e20rMyCredPmproIntegration::clampScore()
 *
 * Guards against turning a renewal award into a deduction (issue #2).
 */
class e20rMyCredClampScoreTests extends WP_UnitTestCase {

	public function test_uncapped_level_returns_configured_score() {
		$this->assertSame( 100, e20rMyCredPmproIntegration::clampScore( 100, 5000, 0, 999999 ) );
	}

	public function test_zero_or_negative_score_is_never_awarded() {
		$this->assertSame( 0, e20rMyCredPmproIntegration::clampScore( 0, 10, 0, 999999 ) );
		$this->assertSame( 0, e20rMyCredPmproIntegration::clampScore( -50, 10, 0, 999999 ) );
		$this->assertSame( 0, e20rMyCredPmproIntegration::clampScore( 'abc', 10, 0, 999999 ) );
	}

	public function test_capped_level_only_grants_what_fits_under_the_cap() {
		$this->assertSame( 100, e20rMyCredPmproIntegration::clampScore( 100, 800, 1000, 999999 ) );
		$this->assertSame( 50, e20rMyCredPmproIntegration::clampScore( 100, 950, 1000, 999999 ) );
		$this->assertSame( 0, e20rMyCredPmproIntegration::clampScore( 100, 1000, 1000, 999999 ) );
	}

	public function test_balance_above_cap_never_produces_a_deduction() {
		// Old code: 100 + (1000 - 1300) = -200
		$this->assertSame( 0, e20rMyCredPmproIntegration::clampScore( 100, 1200, 1000, 999999 ) );
	}

	public function test_unlimited_sentinel_tops_up_to_max_balance() {
		$this->assertSame( 999899, e20rMyCredPmproIntegration::clampScore( 100, 100, 999999, 999999 ) );
		$this->assertSame( 0, e20rMyCredPmproIntegration::clampScore( 100, 999999, 999999, 999999 ) );
		$this->assertSame( 0, e20rMyCredPmproIntegration::clampScore( 100, 1500000, 999999, 999999 ) );
	}

	public function test_string_settings_are_treated_as_integers() {
		$this->assertSame( 50, e20rMyCredPmproIntegration::clampScore( '100', '950', '1000', '999999' ) );
	}
}
