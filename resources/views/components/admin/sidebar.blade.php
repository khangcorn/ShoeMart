<nav class="px-4 py-3 ">
    <div>
        <h2 class="text-xl font-semibold  ">Trang quản trị Shoesmart</h2>
    </div>
    <div class="py-3  text-gray-400 font-semibold uppercase text-xs">
        <p>Danh mục</p>
    </div>
    <ul class="space-y-2">
        <li>
            <a href="{{ url('/admin') }}"
               class="flex items-center p-2.5 rounded
                  {{ request()->is('admin') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100 hover:text-black' }}">
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M7,21h10c2.2,0,4-1.8,4-4v-6.5c0-1.3-0.6-2.4-1.6-3.2l-5-3.8C13,2.5,11,2.5,9.6,3.6l-5,3.7C3.6,8.1,3,9.2,3,10.5V17 
                         C3,19.2,4.8,21,7,21z M5,10.5c0-0.6,0.3-1.2,0.8-1.6l5-3.8c0.4-0.3,0.8-0.4,1.2-0.4s0.8,0.1,1.2,0.4l5,3.8c0.5,0.4,0.8,1,0.8,1.6 
                         V17c0,1.1-0.9,2-2,2H7c-1.1,0-2-0.9-2-2V10.5z"/>
              </svg>
              <span class="ml-2">Trang chủ</span>
            </a>
          </li>
          
        

       <li>
    @if(auth()->user()->hasPermission('view_categories'))
        <a href="{{ route('categories.index') }}"
           class="flex items-center p-2.5 rounded
    {{ request()->is('admin/categories*') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100' }}">

    <svg class="w-5  h-5" fill="#000000" viewBox="0 0 35 35" data-name="Layer 2" id="e73e2821-510d-456e-b3bd-9363037e93e3" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M11.933,15.055H3.479A3.232,3.232,0,0,1,.25,11.827V3.478A3.232,3.232,0,0,1,3.479.25h8.454a3.232,3.232,0,0,1,3.228,3.228v8.349A3.232,3.232,0,0,1,11.933,15.055ZM3.479,2.75a.73.73,0,0,0-.729.728v8.349a.73.73,0,0,0,.729.728h8.454a.729.729,0,0,0,.728-.728V3.478a.729.729,0,0,0-.728-.728Z"></path><path d="M11.974,34.75H3.52A3.233,3.233,0,0,1,.291,31.521V23.173A3.232,3.232,0,0,1,3.52,19.945h8.454A3.232,3.232,0,0,1,15.2,23.173v8.348A3.232,3.232,0,0,1,11.974,34.75ZM3.52,22.445a.73.73,0,0,0-.729.728v8.348a.73.73,0,0,0,.729.729h8.454a.73.73,0,0,0,.728-.729V23.173a.729.729,0,0,0-.728-.728Z"></path><path d="M31.522,34.75H23.068a3.233,3.233,0,0,1-3.229-3.229V23.173a3.232,3.232,0,0,1,3.229-3.228h8.454a3.232,3.232,0,0,1,3.228,3.228v8.348A3.232,3.232,0,0,1,31.522,34.75Zm-8.454-12.3a.73.73,0,0,0-.729.728v8.348a.73.73,0,0,0,.729.729h8.454a.73.73,0,0,0,.728-.729V23.173a.729.729,0,0,0-.728-.728Z"></path><path d="M27.3,15.055a7.4,7.4,0,1,1,7.455-7.4A7.437,7.437,0,0,1,27.3,15.055Zm0-12.3a4.9,4.9,0,1,0,4.955,4.9A4.935,4.935,0,0,0,27.3,2.75Z"></path></g></svg>
                     <span class="ml-2">Danh mục</span>
        </a>
    @endif
</li>

        <li>
    @if(auth()->user()->hasPermission('view_sizes'))
        <a href="{{ route('sizes.index') }}"
           class="flex items-center p-2.5 rounded
    {{ request()->is('admin/sizes*') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100' }}">
    <svg class="w-5  h-5" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M11.5 3.04999C11.7485 3.04999 11.95 3.25146 11.95 3.49999V7.49999C11.95 7.74852 11.7485 7.94999 11.5 7.94999C11.2515 7.94999 11.05 7.74852 11.05 7.49999V4.58639L4.58638 11.05H7.49999C7.74852 11.05 7.94999 11.2515 7.94999 11.5C7.94999 11.7485 7.74852 11.95 7.49999 11.95L3.49999 11.95C3.38064 11.95 3.26618 11.9026 3.18179 11.8182C3.0974 11.7338 3.04999 11.6193 3.04999 11.5L3.04999 7.49999C3.04999 7.25146 3.25146 7.04999 3.49999 7.04999C3.74852 7.04999 3.94999 7.25146 3.94999 7.49999L3.94999 10.4136L10.4136 3.94999L7.49999 3.94999C7.25146 3.94999 7.04999 3.74852 7.04999 3.49999C7.04999 3.25146 7.25146 3.04999 7.49999 3.04999L11.5 3.04999Z" fill="#000000"></path> </g></svg>
                    <span class="ml-2">Kích thước</span>
                </a>
            @endif
        </li>
         <li>
    @if(auth()->user()->hasPermission('view_colors'))
        <a href="{{ route('colors.index') }}"
           class="flex items-center p-2.5 rounded
    {{ request()->is('admin/colors*') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100' }}">
               <svg class="w-5  h-5" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>color_filter_line</title> <g id="页面-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="Design" transform="translate(-48.000000, 0.000000)"> <g id="color_filter_line" transform="translate(48.000000, 0.000000)"> <path d="M24,0 L24,24 L0,24 L0,0 L24,0 Z M12.5934901,23.257841 L12.5819402,23.2595131 L12.5108777,23.2950439 L12.4918791,23.2987469 L12.4918791,23.2987469 L12.4767152,23.2950439 L12.4056548,23.2595131 C12.3958229,23.2563662 12.3870493,23.2590235 12.3821421,23.2649074 L12.3780323,23.275831 L12.360941,23.7031097 L12.3658947,23.7234994 L12.3769048,23.7357139 L12.4804777,23.8096931 L12.4953491,23.8136134 L12.4953491,23.8136134 L12.5071152,23.8096931 L12.6106902,23.7357139 L12.6232938,23.7196733 L12.6232938,23.7196733 L12.6266527,23.7031097 L12.609561,23.275831 C12.6075724,23.2657013 12.6010112,23.2592993 12.5934901,23.257841 L12.5934901,23.257841 Z M12.8583906,23.1452862 L12.8445485,23.1473072 L12.6598443,23.2396597 L12.6498822,23.2499052 L12.6498822,23.2499052 L12.6471943,23.2611114 L12.6650943,23.6906389 L12.6699349,23.7034178 L12.6699349,23.7034178 L12.678386,23.7104931 L12.8793402,23.8032389 C12.8914285,23.8068999 12.9022333,23.8029875 12.9078286,23.7952264 L12.9118235,23.7811639 L12.8776777,23.1665331 C12.8752882,23.1545897 12.8674102,23.1470016 12.8583906,23.1452862 L12.8583906,23.1452862 Z M12.1430473,23.1473072 C12.1332178,23.1423925 12.1221763,23.1452606 12.1156365,23.1525954 L12.1099173,23.1665331 L12.0757714,23.7811639 C12.0751323,23.7926639 12.0828099,23.8018602 12.0926481,23.8045676 L12.108256,23.8032389 L12.3092106,23.7104931 L12.3186497,23.7024347 L12.3186497,23.7024347 L12.3225043,23.6906389 L12.340401,23.2611114 L12.337245,23.2485176 L12.337245,23.2485176 L12.3277531,23.2396597 L12.1430473,23.1473072 Z" id="MingCute" fill-rule="nonzero"> </path> <path d="M12,2 C15.3137,2 18,4.68629 18,8 C18,8.77983 17.8508,9.52578 17.5795,10.2101 C20.1269,10.9035 21.9999,13.233 21.9999,16 C21.9999,19.3137 19.3136,22 15.9999,22 C14.4629,22 13.0608,21.4214 12,20.4722 C10.9385,21.4223 9.53671,22 8,22 C4.68629,22 2,19.3137 2,16 C2,13.2331 3.87293,10.9036 6.42018,10.2102 C6.14903,9.52618 6,8.7805 6,8 C6,4.68629 8.68629,2 12,2 Z M15.9999,12 C14.9354,12 13.9681,12.4151 13.2506,13.0945 C13.728,13.9557 14,14.9469 14,16 C14,17.0541 13.7282,18.0448 13.2507,18.9057 C13.9683,19.585 14.9355,20 15.9999,20 C18.209,20 19.9999,18.2091 19.9999,16 C19.9999,13.7909 18.209,12 15.9999,12 Z M7.55029,12.025 C5.55285,12.2485 4,13.9429 4,16 C4,18.2091 5.79086,20 8,20 C10.2091,20 12,18.2091 12,16 C12,15.26 11.7999,14.5688 11.4506,13.9752 C9.90741,13.8351 8.53296,13.1107 7.55029,12.025 Z M12,4 C9.79086,4 8,5.79086 8,8 C8,10.0571 9.55282,11.7515 11.5502,11.975 C12.5319,10.8905 13.9068,10.1651 15.4506,10.0248 C15.7999,9.43123 16,8.73996 16,8 C16,5.79086 14.2091,4 12,4 Z" id="形状" fill="#09244B"> </path> </g> </g> </g> </g></svg>
                <span class="ml-2">Màu sắc</span>
            </a>
             @endif
        </li>

         <li>
    @if(auth()->user()->hasPermission('view_products'))
        <a href="{{ route('products.index') }}"
           class="flex items-center p-2.5 rounded
    {{ request()->is('admin/products*') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100' }}">
                 <svg class="w-5 h-5  " viewBox="0 -0.5 25 25" fill="none"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="" stroke-width="5" fill-rule="evenodd" clip-rule="evenodd"
                        d="M11.665 3.75618C11.8762 3.65061 12.1247 3.65061 12.3358 3.75618L18.7807 6.97853L12.3358 10.2009C12.1247 10.3064 11.8762 10.3064 11.665 10.2009L5.22014 6.97853L11.665 3.75618ZM4.29297 8.19199V16.0946C4.29297 16.3787 4.45347 16.6384 4.70757 16.7654L11.25 20.0365V11.6512C11.1631 11.6205 11.0777 11.5843 10.9942 11.5425L4.29297 8.19199ZM12.75 20.037L19.2933 16.7654C19.5474 16.6384 19.7079 16.3787 19.7079 16.0946V8.19199L13.0066 11.5425C12.9229 11.5844 12.8372 11.6207 12.75 11.6515V20.037ZM13.0066 2.41453C12.3732 2.09783 11.6277 2.09783 10.9942 2.41453L4.03676 5.89316C3.27449 6.27429 2.79297 7.05339 2.79297 7.90563V16.0946C2.79297 16.9468 3.27448 17.7259 4.03676 18.1071L10.9942 21.5857L11.3296 20.9149L10.9942 21.5857C11.6277 21.9024 12.3732 21.9024 13.0066 21.5857L19.9641 18.1071C20.7264 17.7259 21.2079 16.9468 21.2079 16.0946V7.90563C21.2079 7.05339 20.7264 6.27429 19.9641 5.89316L13.0066 2.41453Z"
                        fill="currentColor"></path>
                </svg>
                <span class="ml-2">Sản phẩm</span>
            </a>
             @endif
        </li>

      <li>
    @if(auth()->user()->hasPermission('view_users'))
        <a href="{{ route('users.index') }}"
           class="flex items-center p-2.5 rounded
    {{ request()->is('admin/users*') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100' }}">
    <svg  class="w-5 h-5  " viewBox="0 0 24 24" id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><defs><style>.cls-1{fill:none;stroke:#020202;stroke-miterlimit:10;stroke-width:1.91px;}</style></defs><circle class="cls-1" cx="12" cy="7.25" r="5.73"></circle><path class="cls-1" d="M1.5,23.48l.37-2.05A10.3,10.3,0,0,1,12,13h0a10.3,10.3,0,0,1,10.13,8.45l.37,2.05"></path></g></svg>

                    <span class="ml-2">Tài khoản</span>
                </a>
            @endif
        </li>

       <li>
    @if(auth()->user()->hasPermission('view_withdraw_requests'))
        <a href="{{ route('admin.withdraw.index') }}"
           class="flex items-center p-2.5 rounded
    {{ request()->is('admin/withdraw*') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100' }}">
    <svg  class="w-5 h-5  " viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M16 18.5C16 19.8807 17.1193 21 18.5 21C19.8807 21 21 19.8807 21 18.5C21 17.1193 19.8807 16 18.5 16M16 18.5C16 17.1193 17.1193 16 18.5 16M16 18.5H8M18.5 16V8M8 18.5C8 19.8807 6.88071 21 5.5 21C4.11929 21 3 19.8807 3 18.5C3 17.1193 4.11929 16 5.5 16M8 18.5C8 17.1193 6.88071 16 5.5 16M5.5 16V8M5.5 8C4.11929 8 3 6.88071 3 5.5C3 4.11929 4.11929 3 5.5 3C6.88071 3 8 4.11929 8 5.5M5.5 8C6.88071 8 8 6.88071 8 5.5M8 5.5H16M18.5 8C17.1193 8 16 6.88071 16 5.5M18.5 8C19.8807 8 21 6.88071 21 5.5C21 4.11929 19.8807 3 18.5 3C17.1193 3 16 4.11929 16 5.5" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                <span class="ml-2">Yêu cầu rút tiền</span>
            </a>
             @endif
        </li>
        
      @if(auth()->user()->hasPermission('view_shipping_fees'))
        <a href="{{ route('shipping-fees.index') }}"
           class="flex items-center p-2.5 rounded
    {{ request()->is('admin/shipping-fees*') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100' }}">
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M16.5 6H3V17.25H3.375H4.5H4.52658C4.70854 18.5221 5.80257 19.5 7.125 19.5C8.44743 19.5 9.54146 18.5221 9.72342 17.25H15.0266C15.2085 18.5221 16.3026 19.5 17.625 19.5C18.9474 19.5 20.0415 18.5221 20.2234 17.25H21.75V12.4393L18.3107 9H16.5V6ZM16.5 10.5V14.5026C16.841 14.3406 17.2224 14.25 17.625 14.25C18.6721 14.25 19.5761 14.8631 19.9974 15.75H20.25V13.0607L17.6893 10.5H16.5ZM15 15.75V9V7.5H4.5V15.75H4.75261C5.17391 14.8631 6.07785 14.25 7.125 14.25C8.17215 14.25 9.07609 14.8631 9.49739 15.75H15ZM17.625 18C17.0037 18 16.5 17.4963 16.5 16.875C16.5 16.2537 17.0037 15.75 17.625 15.75C18.2463 15.75 18.75 16.2537 18.75 16.875C18.75 17.4963 18.2463 18 17.625 18ZM8.25 16.875C8.25 17.4963 7.74632 18 7.125 18C6.50368 18 6 17.4963 6 16.875C6 16.2537 6.50368 15.75 7.125 15.75C7.74632 15.75 8.25 16.2537 8.25 16.875Z" fill="#080341"></path> </g></svg>

                <span class="ml-2">Phí vận chuyển</span>
            </a>
             @endif
        </li>
       <li>
    @if(auth()->user()->hasPermission('view_coupons'))
        <a href="{{ route('coupons.index') }}"
           class="flex items-center p-2.5 rounded
    {{ request()->is('admin/coupons*') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100' }}">
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Interface / Ticket_Voucher"> <path id="Vector" d="M14 6H6C5.06812 6 4.60192 6 4.23438 6.15224C3.74432 6.35523 3.35523 6.74481 3.15224 7.23486C3 7.60241 3 8.06835 3 9.00023C4.65685 9.00023 6 10.3429 6 11.9998C6 13.6566 4.65685 15 3 15C3 15.9319 3 16.3978 3.15224 16.7654C3.35523 17.2554 3.74432 17.6447 4.23438 17.8477C4.60192 17.9999 5.06812 18 6 18H14M14 6H18C18.9319 6 19.3978 6 19.7654 6.15224C20.2554 6.35523 20.6447 6.74481 20.8477 7.23486C20.9999 7.6024 20.9999 8.06835 20.9999 9.00023C19.343 9.00023 18 10.3431 18 12C18 13.6569 19.343 15 20.9999 15C20.9999 15.9319 20.9999 16.3978 20.8477 16.7654C20.6447 17.2554 20.2554 17.6447 19.7654 17.8477C19.3978 17.9999 18.9319 18 18 18H14M14 6V18" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g></svg>

                <span class="ml-2">Mã giảm giá</span>
            </a>
             @endif
        </li>
       <li>
    @if(auth()->user()->hasPermission('view_order_statuses'))
        <a href="{{ route('order-statuses.index') }}"
           class="flex items-center p-2.5 rounded
    {{ request()->is('admin/order-statuses*') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100' }}">
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="File / Archive"> <path id="Vector" d="M6.60035 9H17.4003M6.60035 9C6.04029 9 5.75957 9 5.54566 9.10899C5.3575 9.20487 5.20463 9.35774 5.10875 9.5459C4.99976 9.75981 4.99976 10.04 4.99976 10.6001V15.8001C4.99976 16.9202 4.99976 17.4804 5.21775 17.9082C5.40949 18.2845 5.71523 18.5905 6.09156 18.7822C6.51896 19 7.07875 19 8.19667 19H15.8025C16.9204 19 17.4794 19 17.9068 18.7822C18.2831 18.5905 18.5902 18.2844 18.782 17.9081C18.9998 17.4807 18.9998 16.9216 18.9998 15.8037V10.591C18.9998 10.037 18.9998 9.75865 18.8914 9.5459C18.7955 9.35774 18.6419 9.20487 18.4538 9.10899C18.2398 9 17.9604 9 17.4003 9M6.60035 9H4.97507C4.12597 9 3.70168 9 3.4607 8.85156C3.13911 8.65347 2.95678 8.29079 2.98902 7.91447C3.0132 7.63223 3.26593 7.29089 3.77222 6.60739C3.91866 6.40971 3.99189 6.31084 4.08152 6.23535C4.20104 6.1347 4.34286 6.06322 4.49488 6.02709C4.60889 6 4.73126 6 4.9773 6H19.0217C19.2677 6 19.3904 6 19.5044 6.02709C19.6564 6.06322 19.7982 6.1347 19.9177 6.23535C20.0074 6.31084 20.0809 6.40924 20.2273 6.60693C20.7336 7.29042 20.9867 7.63218 21.0109 7.91442C21.0432 8.29074 20.8602 8.65347 20.5386 8.85156C20.2976 9 19.8723 9 19.0232 9H17.4003M9.99976 14H13.9998" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g></svg>

                <span class="ml-2">Trạng thái đơn hàng</span>
            </a>
             @endif
        </li>
      
       <li>
    @if(auth()->user()->hasPermission('view_sliders'))
        <a href="{{ route('sliders.index') }}"
           class="flex items-center p-2.5 rounded
    {{ request()->is('admin/sliders*') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100' }}">
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Interface / Slider_01"> <path id="Vector" d="M14 15H21M3 15H5M5 15C5 16.3807 6.11929 17.5 7.5 17.5C8.88071 17.5 10 16.3807 10 15C10 13.6193 8.88071 12.5 7.5 12.5C6.11929 12.5 5 13.6193 5 15ZM20 9H21M3 9H10M16.5 11.5C15.1193 11.5 14 10.3807 14 9C14 7.61929 15.1193 6.5 16.5 6.5C17.8807 6.5 19 7.61929 19 9C19 10.3807 17.8807 11.5 16.5 11.5Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g></svg>

                <span class="ml-2">Slider</span>
            </a>
             @endif
        </li>
       <li>
    @if(auth()->user()->hasPermission('view_orders'))
        <a href="{{ route('admin.orders.index') }}"
           class="flex items-center p-2.5 rounded
    {{ request()->is('admin/orders*') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100' }}">
    <svg class="w-5 h-5" viewBox="0 0 1024 1024" fill="#000000" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M53.6 1023.2c-6.4 0-12.8-2.4-17.6-8-4.8-4.8-7.2-11.2-6.4-18.4L80 222.4c0.8-12.8 11.2-22.4 24-22.4h211.2v-3.2c0-52.8 20.8-101.6 57.6-139.2C410.4 21.6 459.2 0.8 512 0.8c108 0 196.8 88 196.8 196.8 0 0.8-0.8 1.6-0.8 2.4v0.8H920c12.8 0 23.2 9.6 24 22.4l49.6 768.8c0.8 2.4 0.8 4 0.8 6.4-0.8 13.6-11.2 24.8-24.8 24.8H53.6z m25.6-48H944l-46.4-726.4H708v57.6h0.8c12.8 8.8 20 21.6 20 36 0 24.8-20 44.8-44.8 44.8s-44.8-20-44.8-44.8c0-14.4 7.2-27.2 20-36h0.8v-57.6H363.2v57.6h0.8c12.8 8.8 20 21.6 20 36 0 24.8-20 44.8-44.8 44.8-24.8 0-44.8-20-44.8-44.8 0-14.4 7.2-27.2 20-36h0.8v-57.6H125.6l-46.4 726.4zM512 49.6c-81.6 0-148.8 66.4-148.8 148.8v3.2h298.4l-0.8-1.6v-1.6c0-82.4-67.2-148.8-148.8-148.8z" fill=""></path></g></svg>
                <span class="ml-2">Đơn hàng</span>
            </a>
             @endif
        </li>
 <li>
    @if(auth()->user()->hasPermission('view_refunds'))
        <a href="{{ route('admin.refunds.index') }}"
           class="flex items-center p-2.5 rounded
    {{ request()->is('admin/refund-requests*') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100' }}">
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4 8L3.29289 8.70711L2.58579 8L3.29289 7.29289L4 8ZM9 20C8.44772 20 8 19.5523 8 19C8 18.4477 8.44772 18 9 18L9 20ZM8.29289 13.7071L3.29289 8.70711L4.70711 7.29289L9.70711 12.2929L8.29289 13.7071ZM3.29289 7.29289L8.29289 2.29289L9.70711 3.70711L4.70711 8.70711L3.29289 7.29289ZM4 7L14.5 7L14.5 9L4 9L4 7ZM14.5 20L9 20L9 18L14.5 18L14.5 20ZM21 13.5C21 17.0898 18.0898 20 14.5 20L14.5 18C16.9853 18 19 15.9853 19 13.5L21 13.5ZM14.5 7C18.0899 7 21 9.91015 21 13.5L19 13.5C19 11.0147 16.9853 9 14.5 9L14.5 7Z" fill="#33363F"></path> </g></svg>
                <span class="ml-2">Hoàn trả</span>
            </a>
             @endif
        </li>
        <li>
    @if(auth()->user()->hasPermission('review.view'))
        <a href="{{ route('admin.reviews.index') }}"
           class="flex items-center p-2.5 rounded
    {{ request()->is('admin/reviews*') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100' }}">
    <svg class="w-5 h-5" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>comment-5</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage"> <g id="Icon-Set" sketch:type="MSLayerGroup" transform="translate(-360.000000, -255.000000)" fill="#000000"> <path d="M390,277 C390,278.463 388.473,280 387,280 L379,280 L376,284 L373,280 L365,280 C363.527,280 362,278.463 362,277 L362,260 C362,258.537 363.527,257 365,257 L387,257 C388.473,257 390,258.537 390,260 L390,277 L390,277 Z M386.667,255 L365.333,255 C362.388,255 360,257.371 360,260.297 L360,277.187 C360,280.111 362.055,282 365,282 L371.639,282 L376,287.001 L380.361,282 L387,282 C389.945,282 392,280.111 392,277.187 L392,260.297 C392,257.371 389.612,255 386.667,255 L386.667,255 Z" id="comment-5" sketch:type="MSShapeGroup"> </path> </g> </g> </g></svg>
                <span class="ml-2">Đánh giá</span>
            </a>
             @endif
        </li>
         <li>
    @if(auth()->user()->hasPermission('admin_create'))
        <a href="{{ route('admin.users.index') }}"
           class="flex items-center p-2.5 rounded
    {{ request()->is('admin/admin-user*') ? 'bg-[#ECF3FF] text-black' : 'text-gray-500 hover:bg-gray-100' }}">

               <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M11.0175 19C10.6601 19 10.3552 18.7347 10.297 18.373C10.2434 18.0804 10.038 17.8413 9.76171 17.75C9.53658 17.6707 9.31645 17.5772 9.10261 17.47C8.84815 17.3365 8.54289 17.3565 8.30701 17.522C8.02156 17.7325 7.62943 17.6999 7.38076 17.445L6.41356 16.453C6.15326 16.186 6.11944 15.7651 6.33361 15.458C6.49878 15.2105 6.52257 14.8914 6.39601 14.621C6.31262 14.4332 6.23906 14.2409 6.17566 14.045C6.08485 13.7363 5.8342 13.5051 5.52533 13.445C5.15287 13.384 4.8779 13.0559 4.87501 12.669V11.428C4.87303 10.9821 5.18705 10.6007 5.61601 10.528C5.94143 10.4645 6.21316 10.2359 6.33751 9.921C6.37456 9.83233 6.41356 9.74433 6.45451 9.657C6.61989 9.33044 6.59705 8.93711 6.39503 8.633C6.1424 8.27288 6.18119 7.77809 6.48668 7.464L7.19746 6.735C7.54802 6.37532 8.1009 6.32877 8.50396 6.625L8.52638 6.641C8.82735 6.84876 9.21033 6.88639 9.54428 6.741C9.90155 6.60911 10.1649 6.29424 10.2375 5.912L10.2473 5.878C10.3275 5.37197 10.7536 5.00021 11.2535 5H12.1115C12.6248 4.99976 13.0629 5.38057 13.1469 5.9L13.1625 5.97C13.2314 6.33617 13.4811 6.63922 13.8216 6.77C14.1498 6.91447 14.5272 6.87674 14.822 6.67L14.8707 6.634C15.2842 6.32834 15.8528 6.37535 16.2133 6.745L16.8675 7.417C17.1954 7.75516 17.2366 8.28693 16.965 8.674C16.7522 8.99752 16.7251 9.41325 16.8938 9.763L16.9358 9.863C17.0724 10.2045 17.3681 10.452 17.7216 10.521C18.1837 10.5983 18.5235 11.0069 18.525 11.487V12.6C18.5249 13.0234 18.2263 13.3846 17.8191 13.454C17.4842 13.5199 17.2114 13.7686 17.1083 14.102C17.0628 14.2353 17.0121 14.3687 16.9562 14.502C16.8261 14.795 16.855 15.1364 17.0323 15.402C17.2662 15.7358 17.2299 16.1943 16.9465 16.485L16.0388 17.417C15.7792 17.6832 15.3698 17.7175 15.0716 17.498C14.8226 17.3235 14.5001 17.3043 14.2331 17.448C14.0428 17.5447 13.8475 17.6305 13.6481 17.705C13.3692 17.8037 13.1636 18.0485 13.1099 18.346C13.053 18.7203 12.7401 18.9972 12.3708 19H11.0175Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M13.9747 12C13.9747 13.2885 12.9563 14.333 11.7 14.333C10.4437 14.333 9.42533 13.2885 9.42533 12C9.42533 10.7115 10.4437 9.66699 11.7 9.66699C12.9563 9.66699 13.9747 10.7115 13.9747 12Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                <span class="ml-2">Phân quyền quản trị </span>
            </a>
             @endif
        </li>
        
        
    </ul>
</nav>
