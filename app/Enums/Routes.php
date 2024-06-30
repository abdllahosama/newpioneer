<?php

namespace App\Enums;

enum Routes: string
{
   const ARTICLES = 'articles';
   const STATICPAGES = 'staticPages';
   const ARTICLES_CATEGORIES = 'articlesCategories';

    static function getConstantName($value)
    {
//        $class = $this::class;
        $class = self::class;
        $map = array_flip((new \ReflectionClass($class))->getConstants());
        return (array_key_exists($value, $map) ? $map[$value] : null);
    }

}
