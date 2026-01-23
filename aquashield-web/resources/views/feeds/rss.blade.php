{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <channel>
        <title><![CDATA[{{ config('app.name') }} Blog]]></title>
        <link>{{ route('blog.index') }}</link>
        <description><![CDATA[Latest roofing news, tips, and industry insights from {{ config('app.name') }}]]></description>
        <language>en-us</language>
        <lastBuildDate>{{ $lastBuildDate->toRssString() }}</lastBuildDate>
        <pubDate>{{ now()->toRssString() }}</pubDate>
        <generator>{{ $generator ?? config('app.name') . ' RSS Generator' }}</generator>
        <managingEditor>{{ config('mail.from.address') }} ({{ config('app.name') }})</managingEditor>
        <webMaster>{{ config('mail.from.address') }} ({{ config('app.name') }})</webMaster>
        <atom:link href="{{ route('feeds.rss') }}" rel="self" type="application/rss+xml" />
        <image>
            <url>{{ asset('images/logo.png') }}</url>
            <title><![CDATA[{{ config('app.name') }}]]></title>
            <link>{{ route('blog.index') }}</link>
            <width>144</width>
            <height>144</height>
        </image>

        @forelse ($posts as $post)
            <item>
                <title><![CDATA[{{ $post->post_title }}]]></title>
                <link>{{ route('blog.show', $post->post_title_slug) }}</link>
                <guid isPermaLink="true">{{ route('blog.show', $post->post_title_slug) }}</guid>
                <description><![CDATA[{!! Str::limit(strip_tags($post->post_content), 300) !!}]]></description>
                <content:encoded><![CDATA[{!! $post->post_content !!}]]></content:encoded>
                <dc:creator><![CDATA[{{ $post->user->name ?? config('app.name') }}]]></dc:creator>
                <author>{{ $post->user->email ?? config('mail.from.address') }} ({{ $post->user->name ?? config('app.name') }})</author>
                <pubDate>{{ $post->created_at->toRssString() }}</pubDate>
                <category><![CDATA[{{ $post->category->blog_category_name ?? 'Blog' }}]]></category>
                @if ($post->post_image)
                    <enclosure url="{{ $post->post_image }}" length="0" type="{{ $post->post_image_mime ?? 'image/jpeg' }}" />
                @endif
                @if ($post->post_excerpt)
                    <excerpt><![CDATA[{{ $post->post_excerpt }}]]></excerpt>
                @endif
            </item>
        @empty
            <item>
                <title><![CDATA[No posts available]]></title>
                <link>{{ route('blog.index') }}</link>
                <guid>{{ route('blog.index') }}/no-posts</guid>
                <description><![CDATA[There are currently no published posts available.]]></description>
                <pubDate>{{ now()->toRssString() }}</pubDate>
            </item>
        @endforelse
    </channel>
</rss>
