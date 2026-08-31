<?php

namespace App\Support;

use Illuminate\Support\Str;
use Overtrue\Pinyin\Pinyin;

class Slug
{
    /**
     * 產生 URL 友善的 slug；中文標題會先轉拼音再 slugify，避免 Str::slug() 對中文直接回傳空字串。
     */
    public static function make(string $text): string
    {
        if (preg_match('/\p{Han}/u', $text)) {
            $text = Pinyin::sentence($text)->join('-');
        }

        return Str::slug($text);
    }
}
