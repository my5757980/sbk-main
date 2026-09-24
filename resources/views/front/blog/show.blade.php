@php $assetBase = asset('assets/front'); @endphp

@extends('front.layouts.app')

@section('title', $blog->meta_title ?: $blog->title . ' | SBK Global Auto Trading')
@section('meta_description', $blog->meta_description ?: Str::limit(strip_tags($blog->description ?: $blog->content), 155))

@section('content')
<style>
.sbk-article{--red:#d71920;--ink:#111216;--muted:#666d78;--line:#e6e8ec;--soft:#f6f7f9;background:#fff;color:var(--ink)}.sbk-article *{box-sizing:border-box}.sbk-article .container{width:min(1120px,calc(100% - 36px));max-width:none!important;margin:auto}.sbk-article-hero{background:#090a0d;color:#fff}.sbk-article-hero__inner{display:grid;grid-template-columns:minmax(0,1fr) 420px;gap:38px;align-items:center;padding:64px 0}.sbk-eyebrow{display:inline-flex;align-items:center;gap:12px;margin:0 0 16px;color:#ff4c54;font-size:12px;font-weight:900;letter-spacing:1.8px;text-transform:uppercase}.sbk-eyebrow:before{content:"";width:40px;height:3px;background:var(--red)}.sbk-article h1{margin:0;color:#fff;font-size:clamp(36px,5vw,58px);line-height:1.04}.sbk-meta{display:flex;gap:12px;flex-wrap:wrap;margin:18px 0 0;color:rgba(255,255,255,.72);font-size:12px;font-weight:900;text-transform:uppercase}.sbk-hero-img{height:300px;border-radius:8px;overflow:hidden;background:#111;box-shadow:0 24px 58px rgba(0,0,0,.35)}.sbk-hero-img img{width:100%;height:100%;object-fit:cover}.sbk-article-body{padding:56px 0 84px}.sbk-article-grid{display:grid;grid-template-columns:minmax(0,760px) 300px;gap:42px;align-items:start}.sbk-content{font-size:16px;line-height:1.85;color:#2d3137}.sbk-content>p:first-child{margin-top:0;font-size:18px;color:#444a54}.sbk-content h2,.sbk-content h3{margin:34px 0 12px;color:#111;line-height:1.2}.sbk-content img{max-width:100%;border-radius:8px}.sbk-content a{color:var(--red);font-weight:800}.sbk-side{position:sticky;top:90px;display:grid;gap:18px}.sbk-box{border:1px solid var(--line);border-radius:8px;background:#fff;overflow:hidden;box-shadow:0 10px 28px rgba(17,18,22,.06)}.sbk-box h3{margin:0;padding:17px 18px;border-bottom:1px solid var(--line);font-size:18px}.sbk-recent{display:grid;padding:6px 18px}.sbk-recent a{padding:14px 0;border-bottom:1px solid #eff0f2;color:#17191d;text-decoration:none;font-size:14px;font-weight:900;line-height:1.4}.sbk-recent a:last-child{border-bottom:0}.sbk-recent span{display:block;margin-top:6px;color:#7c828c;font-size:11px;text-transform:uppercase}.sbk-back{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:0 16px;border-radius:5px;background:var(--red);color:#fff!important;font-size:12px;font-weight:900;text-transform:uppercase;text-decoration:none}.sbk-note{padding:20px;background:#111;color:#fff}.sbk-note h3{padding:0;border:0;color:#fff}.sbk-note p{margin:12px 0 18px;color:rgba(255,255,255,.78);line-height:1.65}@media(max-width:900px){.sbk-article-hero__inner,.sbk-article-grid{grid-template-columns:1fr}.sbk-side{position:static}.sbk-hero-img{height:260px}}@media(max-width:620px){.sbk-article .container{width:calc(100% - 28px)}.sbk-article-hero__inner{padding:48px 0}.sbk-article-body{padding:42px 0 68px}.sbk-content{font-size:15px}.sbk-hero-img{height:220px}}
</style>

<article class="sbk-article">
    <header class="sbk-article-hero">
        <div class="container sbk-article-hero__inner">
            <div>
                <span class="sbk-eyebrow">SBK Blog</span>
                <h1>{{ $blog->title }}</h1>
                <div class="sbk-meta">
                    <span>{{ optional($blog->created_at)->format('M d, Y') }}</span>
                    <span>{{ max(1, ceil(str_word_count(strip_tags($blog->content ?: $blog->description)) / 220)) }} min read</span>
                </div>
            </div>
            <div class="sbk-hero-img">
                <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}">
            </div>
        </div>
    </header>

    <div class="container sbk-article-body">
        <div class="sbk-article-grid">
            <div class="sbk-content">
                @if($blog->description)
                    <p>{{ $blog->description }}</p>
                @endif

                {!! $blog->content ?: '<p>More details will be added soon.</p>' !!}
            </div>

            <aside class="sbk-side">
                <div class="sbk-box">
                    <h3>Recent Posts</h3>
                    <div class="sbk-recent">
                        @forelse($recentBlogs as $post)
                            <a href="{{ route('blog.show', $post) }}">
                                {{ $post->title }}
                                <span>{{ optional($post->created_at)->format('M d, Y') }}</span>
                            </a>
                        @empty
                            <a href="{{ route('blog') }}">Back to blog<span>SBK Blog</span></a>
                        @endforelse
                    </div>
                </div>

                <div class="sbk-box sbk-note">
                    <h3>Explore more guides</h3>
                    <p>Return to the blog for more buying, inspection, and export notes from SBK.</p>
                    <a class="sbk-back" href="{{ route('blog') }}">All Blogs</a>
                </div>
            </aside>
        </div>
    </div>
</article>
@endsection
