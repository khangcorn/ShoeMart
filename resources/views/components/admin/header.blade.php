<nav class=" text-black border-b border-gray-300 dark:border-gray-700">
    <div class="px-4 py-4 flex items-center justify-between">

        <div class="flex gap-2 items-center">
            <div class="">
                <button id="toggleSidebar"
                    class=" left-4 rounded-lg py-3 px-3 border border-gray-300 dark:border-gray-700 ">
                    <svg class="w-5 h-5 fill-gray-500 dark:fill-gray-300" xmlns="http://www.w3.org/2000/svg" x="0px"
                        y="0px" width="100" height="100" viewBox="0 0 50 50">
                        <path
                            d="M 3 9 A 1.0001 1.0001 0 1 0 3 11 L 47 11 A 1.0001 1.0001 0 1 0 47 9 L 3 9 z M 3 24 A 1.0001 1.0001 0 1 0 3 26 L 47 26 A 1.0001 1.0001 0 1 0 47 24 L 3 24 z M 3 39 A 1.0001 1.0001 0 1 0 3 41 L 47 41 A 1.0001 1.0001 0 1 0 47 39 L 3 39 z">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="relative">
                <button class="absolute left-6 top-1/2 -translate-y-1/2">
                    <svg class="fill-gray-500 dark:fill-gray-300" width="20" height="20" viewBox="0 0 20 20"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z"
                            fill=""></path>
                    </svg>
                </button>
                <input type="text" placeholder="Search something..."
                    class="dark:bg-dark-900  w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent py-2.5 pl-12 pr-14 
      text-black placeholder:text-gray-500 
      focus:border-brand-300 focus:outline-none focus:ring focus:ring-brand-500/10 
       dark:bg-gray-900 dark:bg-white/[0.03] 
      dark:text-black dark:placeholder:text-gray-500 
      dark:focus:border-brand-800 xl:w-[430px]">


            </div>
        </div>

        <div class="flex items-center gap-2">
            <div class="cursor-pointer border border-gray-300 bg-white rounded-full p-2" onclick="toggleDarkMode()">
               <svg id="darkModeToggleIcon" class="w-5 h-5 text-black dark:text-white" width="20"
                    height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                </svg>



            </div>
            <div class="relative cursor-pointer border border-gray-300 rounded-full p-2 bg-white">
                <div class="absolute h-2 w-2 animate-ping bg-red-600 rounded-full top-0 right-0"></div>
                <div class="absolute h-2 w-2 bg-red-600 rounded-full top-0 right-0"></div>
                <svg class=" text-black w-5 h-5 " width="20" height="20" viewBox="0 0 20 20" fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg">

                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z"
                        fill=""></path>
                </svg>
            </div>
            <div id="logout-wrapper" style="position: relative; display: inline-block;">
                <div id="logout-icon" class="cursor-pointer border border-gray-300 rounded-full p-2 bg-white">
                    <svg class="text-black w-5 h-5 " viewBox="0 0 24 24" id="Layer_1" data-name="Layer 1"
                        xmlns="http://www.w3.org/2000/svg" fill="#000000">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <defs>
                                <style>
                                    .cls-1 {
                                        fill: none;
                                        stroke: #020202;
                                        stroke-miterlimit: 10;
                                        stroke-width: 1.91px;
                                    }
                                </style>
                            </defs>
                            <circle class="cls-1" cx="12" cy="7.25" r="5.73"></circle>
                            <path class="cls-1"
                                d="M1.5,23.48l.37-2.05A10.3,10.3,0,0,1,12,13h0a10.3,10.3,0,0,1,10.13,8.45l.37,2.05"></path>
                        </g>
                    </svg>
                </div>
                 <button id="logout-btn" style="display: none; position: absolute; top: 100%; left: 50%; transform: translateX(-50%);
                    margin-top: 5px; padding: 5px 10px; border: none; background: #dc3545; color: white; border-radius: 4px; cursor: pointer;">
                    Đăng xuất
                </button>
            </div>
        </div>
    </div>

<style>
    #logout-wrapper {
    position: relative;
    display: inline-block;
}

#logout-btn {
    display: none;
    position: absolute;
    top: 100%; /* dưới icon */
    left: 50%;
    transform: translateX(-50%);
    margin-top: 5px;
    padding: 5px 10px;
    border: none;
    background-color: #dc3545;
    color: white;
    border-radius: 4px;
    cursor: pointer;
}

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
    $('#logout-wrapper').hover(
        function() {
            $('#logout-btn').fadeIn(200);
        },
        function() {
            $('#logout-btn').fadeOut(200);
        }
    );

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
