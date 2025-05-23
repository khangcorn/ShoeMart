<nav class=" text-black border-b border-gray-300 ">
    <div class="px-4 py-4 flex items-center justify-between">

        <div class="flex gap-2 items-center">
            <div class="">
                <button id="toggleSidebar"
                    class=" left-4 rounded-lg py-3 px-3 border border-gray-300  ">
                    <svg class="w-5 h-5 fill-gray-500 " xmlns="http://www.w3.org/2000/svg" x="0px"
                        y="0px" width="100" height="100" viewBox="0 0 50 50">
                        <path
                            d="M 3 9 A 1.0001 1.0001 0 1 0 3 11 L 47 11 A 1.0001 1.0001 0 1 0 47 9 L 3 9 z M 3 24 A 1.0001 1.0001 0 1 0 3 26 L 47 26 A 1.0001 1.0001 0 1 0 47 24 L 3 24 z M 3 39 A 1.0001 1.0001 0 1 0 3 41 L 47 41 A 1.0001 1.0001 0 1 0 47 39 L 3 39 z">
                        </path>
                    </svg>
                </button>
            </div>
           
        </div>

        <div class="flex items-center gap-2">
            
           
            <div id="logout-wrapper">
                
                 <button id="logout-btn" class="px-4 py-2 bg-red-500 text-white rounded-lg">
                    Đăng xuất
                </button>
            </div>
        </div>
    </div>

<style>



</style>
</nav>

<script>
    function toggleMenu() {
        document.getElementById("navbarNav").classList.toggle("hidden");
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
   

    $('#logout-btn').click(function() {
        if(confirm('Bạn có chắc muốn đăng xuất?')) {
            $.post("{{ route('logout') }}", {
                _token: "{{ csrf_token() }}"
            }).done(function() {
                window.location.href = "{{ url('/login') }}";
            }).fail(function() {
                alert('Đăng xuất thất bại, thử lại sau!');
            });
        }
    });
});
</script>

<script src="{{ asset('js/darkmode.js') }}"></script>
