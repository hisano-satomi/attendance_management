<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * 基本的なアサーション（検証）を学ぶためのテスト
 * 
 * このテストではデータベースや外部サービスを使わず、
 * 純粋なPHPの計算や文字列操作をテストします。
 */
class BasicTest extends TestCase
{
    /**
     * テスト1: 基本的な計算のテスト
     * 
     * assertTrue(): 条件が真（true）であることを検証
     */
    public function test_基本的な計算が正しく動作する()
    {
        // 計算を実行
        $result = 1 + 1;
        
        // 結果が2であることを検証
        $this->assertTrue($result === 2);
        
        // より明確な検証方法
        $this->assertEquals(2, $result);
    }

    /**
     * テスト2: 等価性のテスト
     * 
     * assertEquals(): 2つの値が等しいことを検証
     */
    public function test_値が等しいことを検証する()
    {
        $expected = 'Laravel';  // 期待する値
        $actual = 'Laravel';    // 実際の値
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * テスト3: 不等価性のテスト
     * 
     * assertNotEquals(): 2つの値が異なることを検証
     */
    public function test_値が異なることを検証する()
    {
        $value1 = 'PHP';
        $value2 = 'JavaScript';
        
        $this->assertNotEquals($value1, $value2);
    }

    /**
     * テスト4: 配列の要素の検証
     * 
     * assertContains(): 配列に特定の値が含まれることを検証
     */
    public function test_配列に特定の値が含まれる()
    {
        $languages = ['PHP', 'JavaScript', 'Python'];
        
        $this->assertContains('PHP', $languages);
        $this->assertNotContains('Ruby', $languages);
    }

    /**
     * テスト5: 配列の要素数の検証
     * 
     * assertCount(): 配列の要素数を検証
     */
    public function test_配列の要素数を検証する()
    {
        $array = [1, 2, 3, 4, 5];
        
        $this->assertCount(5, $array);
    }

    /**
     * テスト6: 文字列の検証
     * 
     * assertStringContainsString(): 文字列に特定の文字列が含まれることを検証
     */
    public function test_文字列に特定の文字が含まれる()
    {
        $message = 'Hello, Laravel!';
        
        $this->assertStringContainsString('Laravel', $message);
        $this->assertStringNotContainsString('Ruby', $message);
    }

    /**
     * テスト7: 真偽値の検証
     */
    public function test_真偽値を検証する()
    {
        $isActive = true;
        $isDeleted = false;
        
        $this->assertTrue($isActive);
        $this->assertFalse($isDeleted);
    }

    /**
     * テスト8: nullの検証
     */
    public function test_null値を検証する()
    {
        $value = null;
        $notNull = 'something';
        
        $this->assertNull($value);
        $this->assertNotNull($notNull);
    }

    /**
     * テスト9: 空の検証
     */
    public function test_空であることを検証する()
    {
        $emptyString = '';
        $emptyArray = [];
        $notEmpty = 'value';
        
        $this->assertEmpty($emptyString);
        $this->assertEmpty($emptyArray);
        $this->assertNotEmpty($notEmpty);
    }

    /**
     * 【実践問題】テスト10: 消費税計算のテスト
     * 
     * 実際のアプリケーションで使いそうな計算をテストしてみましょう
     */
    public function test_消費税込み価格を計算する()
    {
        // 商品価格（税抜き）
        $price = 1000;
        
        // 消費税率10%
        $taxRate = 0.10;
        
        // 税込み価格を計算
        $priceWithTax = $price * (1 + $taxRate);
        
        // 1,100円になることを検証
        $this->assertEquals(1100, $priceWithTax);
    }
}

