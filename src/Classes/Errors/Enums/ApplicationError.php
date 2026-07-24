<?php

declare(strict_types=1);

namespace App\Classes\Errors\Enums;

enum ApplicationError: string
{
    case ROUTE_NOT_FOUND = 'route_not_found';
    case HOMEPAGE_CATEGORIES_UNAVAILABLE = 'homepage_categories_unavailable';
    case HOMEPAGE_POSTS_UNAVAILABLE = 'homepage_posts_unavailable';
    case DATABASE_UNAVAILABLE = 'database_unavailable';
    case CATEGORY_NOT_FOUND = 'category_not_found';
    case CATEGORY_UNAVAILABLE = 'category_unavailable';
    case ARTICLE_NOT_FOUND = 'article_not_found';
    case ARTICLE_UNAVAILABLE = 'article_unavailable';
    case RATE_LIMITED = 'rate_limited';

    public function statusCode(): int
    {
        return match ($this) {
            self::ROUTE_NOT_FOUND, self::CATEGORY_NOT_FOUND, self::ARTICLE_NOT_FOUND => 404,
            self::RATE_LIMITED => 429,
            self::DATABASE_UNAVAILABLE,
            self::HOMEPAGE_CATEGORIES_UNAVAILABLE,
            self::HOMEPAGE_POSTS_UNAVAILABLE,
            self::CATEGORY_UNAVAILABLE,
            self::ARTICLE_UNAVAILABLE => 500,
        };
    }

    public function title(): string
    {
        return match ($this) {
            self::ROUTE_NOT_FOUND => 'Page not found',
            self::DATABASE_UNAVAILABLE => 'Database unavailable',
            self::HOMEPAGE_CATEGORIES_UNAVAILABLE => 'Homepage categories unavailable',
            self::HOMEPAGE_POSTS_UNAVAILABLE => 'Homepage articles unavailable',
            self::CATEGORY_NOT_FOUND => 'Category not found',
            self::CATEGORY_UNAVAILABLE => 'Category unavailable',
            self::ARTICLE_NOT_FOUND => 'Article not found',
            self::ARTICLE_UNAVAILABLE => 'Article unavailable',
            self::RATE_LIMITED => 'Too many requests',
        };
    }

    public function message(): string
    {
        return match ($this) {
            self::ROUTE_NOT_FOUND => 'The page you requested does not exist or has been moved.',
            self::DATABASE_UNAVAILABLE => 'The application could not connect to the database. Please try again later.',
            self::HOMEPAGE_CATEGORIES_UNAVAILABLE => 'The homepage categories could not be loaded from the database. Please try again later.',
            self::HOMEPAGE_POSTS_UNAVAILABLE => 'The homepage article previews could not be loaded from the database. Please try again later.',
            self::CATEGORY_NOT_FOUND => 'The requested category does not exist or has been removed.',
            self::CATEGORY_UNAVAILABLE => 'The category page could not be loaded from the database. Please try again later.',
            self::ARTICLE_NOT_FOUND => 'The requested article does not exist or has been removed.',
            self::ARTICLE_UNAVAILABLE => 'The article could not be loaded from the database. Please try again later.',
            self::RATE_LIMITED => 'Too many requests were sent from your IP address. Please try again in a moment.',
        };
    }
}
