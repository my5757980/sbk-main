<style>
.sbk-steering{padding:20px 0 48px}.sbk-steering__heading{text-align:center;margin-bottom:24px}.sbk-steering__heading h2{margin:0 0 10px;font-size:30px}.sbk-steering__heading p{margin:0;color:#70757d;line-height:1.6}.sbk-steering__grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:24px}.sbk-steering__card{display:block;overflow:hidden;border:1px solid #e8e8e8;border-radius:14px;background:#fff;transition:box-shadow .2s,transform .2s}.sbk-steering__card:hover{transform:translateY(-4px);box-shadow:0 14px 32px rgba(17,18,22,.12)}.sbk-steering__card:focus-visible{outline:3px solid #d50000;outline-offset:5px}.sbk-steering__card img{display:block;width:100%;height:auto;aspect-ratio:600/320;object-fit:cover}.sbk-steering__caption{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:22px}.sbk-steering__caption h3{margin:0 0 7px;font-size:21px}.sbk-steering__caption p{margin:0;color:#70757d;font-size:13px}.sbk-steering__action{color:#d50000;font-size:13px;font-weight:700;white-space:nowrap}@media(max-width:620px){.sbk-steering__grid{grid-template-columns:1fr;gap:18px}.sbk-steering__heading h2{font-size:25px}.sbk-steering__caption{padding:18px}.sbk-steering__caption h3{font-size:18px}.sbk-steering__action{font-size:12px}}
</style>
<section class="sbk-steering" aria-labelledby="sbk-steering-title">
    <div class="container">
        <div class="sbk-steering__heading">
            <h2 id="sbk-steering-title">Shop by Steering Position</h2>
            <p>Choose the drive specification that suits you.</p>
        </div>
        <div class="sbk-steering__grid">
            @foreach(['LHD' => 'Left Hand Drive', 'RHD' => 'Right Hand Drive'] as $code => $label)
                <a class="sbk-steering__card" href="{{ route('shop', ['steering' => $code]) }}">
                    <img src="{{ asset('assets/front/img/steering-' . strtolower($code) . '.jpg') }}" alt="Car interior with the steering wheel on the {{ $code === 'LHD' ? 'left' : 'right' }}" width="600" height="320" loading="lazy">
                    <div class="sbk-steering__caption">
                        <div><h3>{{ $label }} ({{ $code }})</h3><p>Browse {{ $code }} vehicles in stock</p></div>
                        <span class="sbk-steering__action">Shop {{ $code }} &rarr;</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
