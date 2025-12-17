<!-- Sidebar -->
<aside style="background-color: #1e3a8a;" class="w-64 text-white flex flex-col shadow-xl h-screen transition-all duration-300 z-50">
    <nav class="mt-4">
        <ul class="space-y-2 mt-8 -ml-1">
            <li>
                <!-- Toggle Button -->
                <button id="toggleSidebar" class="fixed top-4 left-4 z-50 p-2 bg-primary text-white rounded-lg shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16">
                        </path>
                    </svg>
                </button>
            </li>
            <li>
                <a href="{{ route('home') }}"
                    class="flex items-center px-4 py-3 text-white hover:bg-primary rounded-lg transition duration-300 {{ $home ? 'sidebar-active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span class="font-medium">Trang chủ</span>
                </a>
            </li>
            <li>
                <a href="{{ route('qls') }}"
                    class="flex items-center px-4 py-3 text-white hover:bg-primary rounded-lg transition duration-300 {{ $qls ? 'sidebar-active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    <span class="font-medium">Quản lý sách</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/quan-ly-ban-doc') }}"
                    class="flex items-center px-4 py-3 text-white hover:bg-primary rounded-lg transition duration-300 {{ $qlbd ? 'sidebar-active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <span class="font-medium">Quản lý bạn đọc</span>
                </a>
            </li>
            <li>
                <a href="{{ route('qlmt') }}"
                    class="flex items-center px-4 py-3 text-white hover:bg-primary rounded-lg transition duration-300 {{ $qlmt ? 'sidebar-active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span class="font-medium">Quản lý mượn/trả</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
