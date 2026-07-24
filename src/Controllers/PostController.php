<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class PostController extends Controller
{
    public function show(string $slug): string
    {
        $post = $this->demoPost($slug);

        return $this->render('post/show.tpl', [
            'pageTitle' => $post['title'],
            'post' => $post,
            'relatedPosts' => $this->relatedPosts($slug),
        ]);
    }

    /**
     * @return array{
     *   title: string,
     *   slug: string,
     *   category: array{name: string, slug: string},
     *   image: string,
     *   description: string,
     *   author: string,
     *   authorRole: string,
     *   publishedAt: string,
     *   views: string,
     *   readTime: string,
     *   content: array<int, array{heading: string, paragraphs: array<int, string>}>
     * }
     */
    private function demoPost(string $slug): array
    {
        return [
            'title' => $this->titleFromSlug($slug),
            'slug' => $slug,
            'category' => [
                'name' => 'Design Systems',
                'slug' => 'design-systems',
            ],
            'image' => $this->imageFromSlug($slug),
            'description' => 'A full article layout with metadata, lead text, structured content blocks, and a related articles section.',
            'author' => 'James Carter',
            'authorRole' => 'Editorial Lead',
            'publishedAt' => 'July 21, 2026',
            'views' => '2,430',
            'readTime' => '6 min read',
            'content' => [
                [
                    'heading' => 'Why the article page matters',
                    'paragraphs' => [
                        'The article page is where the visual language of the blog either holds together or falls apart. It needs enough structure to support long-form reading without turning into a wall of text.',
                        'For this layout the goal is straightforward: keep the article readable, preserve a strong headline hierarchy, and make adjacent content discoverable without distracting from the main body.',
                    ],
                ],
                [
                    'heading' => 'A readable content rhythm',
                    'paragraphs' => [
                        'The content column should stay restrained in width, with enough whitespace around paragraphs and headings to maintain pace. Supporting metadata belongs near the title, not scattered across the page.',
                        'Images, descriptions, and article sections should feel related rather than stacked mechanically. That means consistent spacing, clear breaks, and enough contrast between utility information and narrative content.',
                    ],
                ],
                [
                    'heading' => 'Related content should stay secondary',
                    'paragraphs' => [
                        'The related articles block exists to extend the reading journey, not to compete with the article itself. Three concise cards are enough for a clean handoff after the main story ends.',
                        'This keeps the page useful as both a reading experience and a discovery surface while avoiding the clutter of a full archive feed under every post.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array{
     *   href: string,
     *   image: string,
     *   title: string,
     *   meta: string,
     *   description: string
     * }>
     */
    private function relatedPosts(string $currentSlug): array
    {
        $slugs = [
            'editorial-ux-patterns',
            'meaningful-card-layouts',
            'archive-pagination',
        ];

        $posts = [];

        foreach ($slugs as $slug) {
            if ($slug === $currentSlug) {
                continue;
            }

            $posts[] = [
                'href' => "/post/{$slug}",
                'image' => $this->imageFromSlug($slug),
                'title' => $this->titleFromSlug($slug),
                'meta' => 'Related article',
                'description' => 'A short follow-up article suggestion placed below the main story for further reading.',
            ];
        }

        return array_slice($posts, 0, 3);
    }

    private function titleFromSlug(string $slug): string
    {
        return match ($slug) {
            'building-a-category-page' => 'Building a category page that scales with editorial content',
            'editorial-ux-patterns' => 'Editorial UX patterns for article archives',
            'meaningful-card-layouts' => 'Meaningful card layouts for content-heavy pages',
            'improving-category-navigation' => 'Improving category navigation with simple hierarchy',
            'archive-pagination' => 'Archive pagination patterns that stay readable',
            'sort-controls' => 'Sort controls that do not dominate the page',
            default => ucwords(str_replace('-', ' ', $slug)),
        };
    }

    private function imageFromSlug(string $slug): string
    {
        return match ($slug) {
            'editorial-ux-patterns', 'improving-category-navigation' => '/images/blogs.jpg',
            'meaningful-card-layouts', 'sort-controls' => '/images/images.jpeg',
            default => '/images/blog.jpg',
        };
    }
}
