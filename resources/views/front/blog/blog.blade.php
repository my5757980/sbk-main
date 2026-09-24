@extends('front.layouts.app')

@section('title', 'Blog | SBK Global Auto Trading')
@section('meta_description', 'Read SBK Global Auto Trading guides on Japanese vehicle imports, inspection, shipping, ownership, and buying with confidence.')

@php
    $assetBase = asset('assets/front');
    $heroImage = optional($featuredBlog)->image_url ?: $assetBase . '/img/blog-american-japanese.webp';
@endphp

@section('content')
<style>
.sbk-blog{--red:#d71920;--ink:#111216;--muted:#666d78;--line:#e6e8ec;--soft:#f6f7f9;background:#fff;color:var(--ink)}.sbk-blog *{box-sizing:border-box}.sbk-blog .container{width:min(1140px,calc(100% - 36px));max-width:none!important;margin:auto}.sbk-blog-hero{position:relative;isolation:isolate;background:#090a0d;color:#fff}.sbk-blog-hero:before{content:"";position:absolute;inset:0;z-index:-2;background:linear-gradient(90deg,rgba(0,0,0,.9),rgba(0,0,0,.66),rgba(0,0,0,.28)),url("{{ $heroImage }}") center/cover no-repeat}.sbk-blog-hero__inner{min-height:360px;display:flex;flex-direction:column;justify-content:center;padding:62px 0}.sbk-eyebrow{display:inline-flex;align-items:center;gap:12px;margin:0 0 16px;color:#ff4c54;font-size:12px;font-weight:900;letter-spacing:1.8px;text-transform:uppercase}.sbk-eyebrow:before{content:"";width:40px;height:3px;background:var(--red)}.sbk-blog-hero h1{max-width:760px;margin:0;color:#fff;font-size:clamp(38px,5vw,64px);line-height:1;font-weight:900}.sbk-blog-hero p{max-width:610px;margin:18px 0 0;color:rgba(255,255,255,.8);font-size:16px;line-height:1.7}.sbk-blog-main{padding:54px 0 86px}.sbk-blog-top{display:flex;justify-content:space-between;gap:22px;align-items:end;margin-bottom:26px}.sbk-blog-top h2{margin:0;font-size:32px;line-height:1.1}.sbk-blog .sbk-search{display:flex;gap:8px;min-width:min(360px,100%)}.sbk-blog .sbk-search input{height:44px;min-width:0;flex:1;border:1px solid #d9dce2;border-radius:5px;padding:0 13px;outline:none}.sbk-blog .sbk-search input:focus{border-color:var(--red);box-shadow:0 0 0 3px rgba(215,25,32,.09)}.sbk-blog .sbk-search button{height:44px;border:0;border-radius:5px;background:var(--red);color:#fff;padding:0 17px;font-weight:900;cursor:pointer}.sbk-blog-grid{display:grid;grid-template-columns:minmax(0,1fr) 310px;gap:30px;align-items:start}.sbk-posts{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}.sbk-post{display:flex;flex-direction:column;border:1px solid var(--line);border-radius:8px;overflow:hidden;background:#fff;box-shadow:0 10px 30px rgba(17,18,22,.07);transition:transform .2s ease,box-shadow .2s ease}.sbk-post:hover{transform:translateY(-4px);box-shadow:0 18px 42px rgba(17,18,22,.12)}.sbk-post__img{display:block;height:218px;background:#111;overflow:hidden}.sbk-post__img img{width:100%;height:100%;object-fit:cover;transition:transform .35s ease}.sbk-post:hover .sbk-post__img img{transform:scale(1.04)}.sbk-post__body{display:flex;flex:1;flex-direction:column;padding:22px}.sbk-meta{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px;color:#7a808a;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.5px}.sbk-post h3{margin:0;font-size:22px;line-height:1.24}.sbk-post h3 a{color:var(--ink);text-decoration:none}.sbk-post h3 a:hover{color:var(--red)}.sbk-post p{margin:13px 0 20px;color:var(--muted);font-size:14px;line-height:1.7}.sbk-link{display:inline-flex;width:max-content;margin-top:auto;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border-radius:5px;background:#111;color:#fff!important;font-size:12px;font-weight:900;text-transform:uppercase;text-decoration:none}.sbk-link:hover{background:var(--red)}.sbk-side{position:sticky;top:90px;display:grid;gap:18px}.sbk-box{border:1px solid var(--line);border-radius:8px;background:#fff;overflow:hidden;box-shadow:0 10px 28px rgba(17,18,22,.06)}.sbk-box h3{margin:0;padding:17px 18px;border-bottom:1px solid var(--line);font-size:18px}.sbk-recent{display:grid;padding:6px 18px}.sbk-recent a{padding:14px 0;border-bottom:1px solid #eff0f2;color:#17191d;text-decoration:none;font-size:14px;font-weight:900;line-height:1.4}.sbk-recent a:last-child{border-bottom:0}.sbk-recent span{display:block;margin-top:6px;color:#7c828c;font-size:11px;text-transform:uppercase}.sbk-note{padding:20px;background:#111;color:#fff}.sbk-note h3{padding:0;border:0;color:#fff}.sbk-note p{margin:12px 0 18px;color:rgba(255,255,255,.78);line-height:1.65}.sbk-note .sbk-link{background:var(--red)}.sbk-empty{grid-column:1/-1;padding:34px;border:1px dashed #d9dce2;border-radius:8px;background:var(--soft);color:var(--muted);text-align:center}.sbk-pages{grid-column:1/-1;margin-top:10px}.sbk-pages nav{display:flex;justify-content:center}.sbk-pages .pagination{display:flex;gap:6px;list-style:none;padding:0;margin:0}.sbk-pages .page-link{display:flex;min-width:38px;height:38px;align-items:center;justify-content:center;border:1px solid var(--line);border-radius:5px;color:#111;text-decoration:none}.sbk-pages .active .page-link,.sbk-pages .page-link:hover{background:var(--red);border-color:var(--red);color:#fff}@media(max-width:920px){.sbk-blog-grid{grid-template-columns:1fr}.sbk-side{position:static;grid-template-columns:1fr 1fr}.sbk-note{grid-column:1/-1}}@media(max-width:680px){.sbk-blog .container{width:calc(100% - 28px)}.sbk-blog-top{display:block}.sbk-blog .sbk-search{margin-top:16px}.sbk-posts,.sbk-side{grid-template-columns:1fr}.sbk-blog-hero__inner{min-height:320px}.sbk-post__img{height:205px}}
</style>

<section class="sbk-blog">
    <div class="sbk-blog-hero">
        <div class="container sbk-blog-hero__inner">
            <span class="sbk-eyebrow">SBK Blog</span>
            <h1>Simple guides for smarter vehicle imports.</h1>
            <p>Practical notes on choosing, checking, shipping, and protecting vehicles before you commit.</p>
        </div>
    </div>

    <div class="container sbk-blog-main">
        <div class="sbk-blog-top">
            <div>
                <span class="sbk-eyebrow" style="color:var(--red)">Latest Articles</span>
                <h2>{{ $search ? 'Search results' : 'Read the latest from SBK' }}</h2>
            </div>
            <form class="sbk-search" action="{{ route('blog') }}" method="get">
                <input type="search" name="search" value="{{ $search }}" placeholder="Search blog">
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="sbk-blog-grid">
            <main class="sbk-posts">
                @forelse($blogs as $post)
                    <article class="sbk-post">
                        <a class="sbk-post__img" href="{{ route('blog.show', $post) }}">
                            <img src="{{ $post->image_url }}" alt="{{ $post->title }}">
                        </a>
                        <div class="sbk-post__body">
                            <div class="sbk-meta">
                                <span>{{ optional($post->created_at)->format('M d, Y') }}</span>
                                <span>{{ max(1, ceil(str_word_count(strip_tags($post->content ?: $post->description)) / 220)) }} min read</span>
                            </div>
                            <h3><a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a></h3>
                            <p>{{ Str::limit(strip_tags($post->description ?: $post->content), 150) }}</p>
                            <a class="sbk-link" href="{{ route('blog.show', $post) }}">Read More</a>
                        </div>
                    </article>
                @empty
                    <div class="sbk-empty">No blog posts found. Add a published blog from the admin panel.</div>
                @endforelse

                <div class="sbk-pages">{{ $blogs->links() }}</div>
            </main>

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
                            <a href="{{ route('blog') }}">No posts yet<span>Admin blog</span></a>
                        @endforelse
                    </div>
                </div>

                <div class="sbk-box sbk-note">
                    <h3>Need help importing?</h3>
                    <p>SBK can help you choose, verify, and ship the right vehicle with clear documents before you commit.</p>
                    <a class="sbk-link" href="{{ route('contact-us') }}">Contact SBK</a>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
