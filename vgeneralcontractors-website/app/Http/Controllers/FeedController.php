<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

class FeedController extends Controller
{
    /**
     * Generate RSS feed with caching and modern Laravel 12 practices
     */
    public function rss(Request $request): Response
    {
        // Cache the RSS feed for 1 hour to improve performance
        $cacheKey = 'rss_feed_' . md5($request->fullUrl());
        
        $content = Cache::remember($cacheKey, 3600, function () {
            try {
                $posts = $this->getPublishedPosts();
                
                // Additional data for RSS feed
                $lastBuildDate = $posts->isNotEmpty() ? $posts->first()->updated_at : now();
                $generator = config('app.name') . ' RSS Generator v2.0 (Laravel ' . app()->version() . ')';
                
                return view('feeds.rss', [
                    'posts' => $posts,
                    'lastBuildDate' => $lastBuildDate,
                    'generator' => $generator
                ])->render();
            } catch (QueryException $e) {
                // If there's an issue with post_status column, get all posts
                $posts = Post::with(['user', 'category'])
                    ->latest('created_at')
                    ->take(20)
                    ->get();
                
                $lastBuildDate = $posts->isNotEmpty() ? $posts->first()->updated_at : now();
                $generator = config('app.name') . ' RSS Generator v2.0 (Laravel ' . app()->version() . ')';
                
                return view('feeds.rss', [
                    'posts' => $posts,
                    'lastBuildDate' => $lastBuildDate,
                    'generator' => $generator
                ])->render();
            }
        });
        
        return response($content)
            ->withHeaders([
                'Content-Type' => 'application/rss+xml; charset=UTF-8',
                'Cache-Control' => 'public, max-age=3600',
                'ETag' => md5($content)
            ]);
    }
    
    /**
     * Get published posts using modern Laravel 12 query patterns
     */
    private function getPublishedPosts()
    {
        $query = Post::query()
            ->with(['user', 'category']) // Eager load relationships
            ->latest('created_at')
            ->take(20);
            
        // Check if post_status column exists using Schema facade
        if (Schema::hasColumn('posts', 'post_status')) {
            $query->where('post_status', 'published');
        }
        
        return $query->get();
    }
    
    /**
     * Generate Atom feed (alternative format)
     */
    public function atom(Request $request): Response
    {
        $cacheKey = 'atom_feed_' . md5($request->fullUrl());
        
        $content = Cache::remember($cacheKey, 3600, function () {
            $posts = $this->getPublishedPosts();
            
            return view('feeds.atom', [
                'posts' => $posts,
                'updated' => now(),
                'id' => route('feeds.atom')
            ])->render();
        });
        
        return response($content)
            ->withHeaders([
                'Content-Type' => 'application/atom+xml; charset=UTF-8',
                'Cache-Control' => 'public, max-age=3600',
                'ETag' => md5($content)
            ]);
    }
}