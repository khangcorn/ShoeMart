{{-- 


@section('title', 'Homepage')

@section('content')
<div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8">
    <img src="https://static.nike.com/a/images/f_auto/dpr_0.9,cs_srgb/w_1396,c_limit/24c4cdf6-9cdf-4aa6-8c9a-89dbd8b866d0/nike-just-do-it.jpg"
        alt="Nike" class="h-auto w-full max-w-screen-xl object-cover" />
</div>

<div class="p-2 h-50 mt-8 justify-center items-center mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8 text-center">
    <div>
        <h1 class="text-4xl font-bold mr-4">LeBron XXII ‘The Limelight’</h1>
        <p class="text-sm text-black mr-4">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
        <a class="mt-4 group inline-block rounded-full bg-black font-medium px-6 py-2 text-white" href="#">
            Shop
        </a>
    </div>

    <div class="mt-12">
        <div class="text-left text-4xl font-normal mb-2">Feature</div>
        <div class="grid grid-cols-3 gap-4">
            @foreach (['img1.jpg', 'img2.jpg', 'img3.jpg'] as $img)
            <a href="#" class="block shadow-indigo-100">
                <img alt="Product" src="{{ asset('storage/'.$img) }}" class="h-full w-full object-cover" />
                <div class="mt-8">
                    <h1 class="text-left text-xl font-normal">Bra & Leggings</h1>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const slider = document.getElementById("slider");
    const prev = document.getElementById("prev");
    const next = document.getElementById("next");

    prev.addEventListener("click", () => {
        slider.scrollBy({ left: -200, behavior: "smooth" });
    });

    next.addEventListener("click", () => {
        slider.scrollBy({ left: 200, behavior: "smooth" });
    });
</script>
@endsection --}}
