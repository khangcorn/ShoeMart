<nav class=" text-black border-b border-gray-300 dark:border-gray-700">
    <div class="px-3 py-3 flex items-center justify-between">
        <div class="relative">
            <button class="absolute left-4 top-1/2 -translate-y-1/2">
                <svg class="fill-gray-300 dark:fill-gray-300" width="20" height="20" viewBox="0 0 20 20"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z"
                        fill=""></path>
                </svg>
            </button>
            <input type="text" placeholder="Search something..."
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent py-2.5 pl-12 pr-14 
  text-black placeholder:text-gray-500 
  focus:border-brand-300 focus:outline-none focus:ring focus:ring-brand-500/10 
   dark:bg-gray-900 dark:bg-white/[0.03] 
  dark:text-black dark:placeholder:text-gray-500 
  dark:focus:border-brand-800 xl:w-[430px]">


        </div>

        <div class="flex items-center gap-2">
            <div class="cursor-pointer border border-gray-400 bg-white rounded-full p-2" onclick="toggleDarkMode()">
                <svg id="darkModeToggleIcon" class="text-black w-5 h-5" class="dark:hidden" width="20"
                    height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <circle cx="12" cy="12" r="6" stroke="#000000" stroke-width="1.5"></circle>
                        <path d="M12 2V3" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
                        <path d="M12 21V22" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
                        <path d="M22 12L21 12" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
                        <path d="M3 12L2 12" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
                        <path d="M19.0708 4.92969L18.678 5.32252" stroke="#000000" stroke-width="1.5"
                            stroke-linecap="round"></path>
                        <path d="M5.32178 18.6777L4.92894 19.0706" stroke="#000000" stroke-width="1.5"
                            stroke-linecap="round"></path>
                        <path d="M19.0708 19.0703L18.678 18.6775" stroke="#000000" stroke-width="1.5"
                            stroke-linecap="round"></path>
                        <path d="M5.32178 5.32227L4.92894 4.92943" stroke="#000000" stroke-width="1.5"
                            stroke-linecap="round"></path>
                    </g>
                </svg>
                

            </div>
            <div class="relative cursor-pointer border border-gray-400 rounded-full p-2 bg-white">
                <div class="absolute h-2 w-2 bg-red-600 rounded-full top-0 right-0"></div>
                <svg class=" text-black w-5 h-5 " width="20" height="20" viewBox="0 0 20 20" fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg">

                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z"
                        fill=""></path>
                </svg>
            </div>
            <div class="cursor-pointer border border-gray-400 rounded-full p-2 bg-white">
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
        </div>
    </div>


</nav>

<script>
    function toggleMenu() {
        document.getElementById("navbarNav").classList.toggle("hidden");
    }
</script>
<script src="{{ asset('js/darkmode.js') }}"></script>
