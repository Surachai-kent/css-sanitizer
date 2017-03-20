<?php
/**
 * @file
 * @license https://opensource.org/licenses/Apache-2.0 Apache-2.0
 */

namespace Wikimedia\CSS\Objects;

use InvalidArgumentException;
use Wikimedia\CSS\Util;

/**
 * @covers \Wikimedia\CSS\Objects\QualifiedRule
 * @covers \Wikimedia\CSS\Objects\Rule
 */
class QualifiedRuleTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Qualified rule block must be delimited by {}
	 */
	public function testBadBlock() {
		$rule = new QualifiedRule( new Token( Token::T_IDENT, 'value' ) );
		$rule->setBlock( SimpleBlock::newFromDelimiter( Token::T_LEFT_BRACKET ) );
	}

	public function testClone() {
		$identToken = new Token( Token::T_IDENT, [ 'value' => 'foobar', 'position' => [ 123, 42 ] ] );
		$ws = new Token( Token::T_WHITESPACE );
		$rule = new QualifiedRule( $identToken );

		$rule2 = clone( $rule );
		$this->assertNotSame( $rule, $rule2 );
		$this->assertNotSame( $rule->getPrelude(), $rule2->getPrelude() );
		$this->assertNotSame( $rule->getBlock(), $rule2->getBlock() );
		$this->assertEquals( $rule, $rule2 );
	}

	public function testBasics() {
		$identToken = new Token( Token::T_IDENT, [ 'value' => 'foobar', 'position' => [ 123, 42 ] ] );
		$ws = new Token( Token::T_WHITESPACE );
		$Iws = new Token( Token::T_WHITESPACE, [ 'significant' => false ] );
		$leftBraceToken = new Token( Token::T_LEFT_BRACE );
		$rightBraceToken = new Token( Token::T_RIGHT_BRACE );

		$rule = new QualifiedRule( $identToken );

		$this->assertSame( [ 123, 42 ], $rule->getPosition() );
		$this->assertInstanceOf( ComponentValueList::class, $rule->getPrelude() );
		$this->assertCount( 0, $rule->getPrelude() );
		$this->assertInstanceOf( SimpleBlock::class, $rule->getBlock() );
		$this->assertSame( Token::T_LEFT_BRACE, $rule->getBlock()->getStartTokenType() );

		$block = new SimpleBlock( $leftBraceToken );
		$block->getValue()->add( $ws );
		$rule->setBlock( $block );
		$this->assertSame( $block, $rule->getBlock() );

		$rule->getPrelude()->add( $identToken );
		$rule->getPrelude()->add( $ws );

		$this->assertEquals(
			[ $identToken, $ws, $leftBraceToken, $ws, $rightBraceToken ],
			$rule->toTokenArray()
		);
		$this->assertSame( Util::stringify( $rule ), (string)$rule );

		$rule = new QualifiedRule();
		$this->assertSame( [ -1, -1 ], $rule->getPosition() );
		$this->assertInstanceOf( ComponentValueList::class, $rule->getPrelude() );
		$this->assertCount( 0, $rule->getPrelude() );
	}
}
