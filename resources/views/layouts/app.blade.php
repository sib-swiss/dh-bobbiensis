@extends('layouts.base')

@section('body')
    <div class="bg-[#4a74ac]">
        <nav id="topmenu">
            <div>
                <div class="flex items-center justify-between text-white">

                    <div class="flex items-center justify-between grow">
                        <div>
                            <a href="{{ route('home') }}"
                                class="mr-4 block cursor-pointer py-1.5 text-md font-normal leading-normal text-inherit antialiased">
                                Codex Bobbiensis (G.VII.15 or VL 1)
                            </a>
                            <small>
                                <a href="https://bnuto.cultura.gov.it/" target="_blank">Biblioteca
                                    Nazionale Universitaria di Torino</a>
                                <br>
                                <a href="https://sib.swiss" target="_blank">SIB Swiss
                                    Institute of Bioinformatics</a>

                            </small>

                        </div>

                        <ul class="hidden items-center justify-center grow gap-6 lg:flex">
                            <li class="block p-1 text-md font-normal leading-normal text-inherit antialiased">
                                <a class="flex items-centerk" href="{{ route('vl1') }}">
                                    VL 1
                                </a>
                            </li>
                            <li class="block p-1 text-md font-normal leading-normal text-inherit antialiased">
                                <a class="flex items-centerk" href="{{ route('about') }}">
                                    About
                                </a>
                            </li>
                            <li class="block p-1 text-md font-normal leading-normal text-inherit antialiased">
                                <a class="flex items-center " href="{{ route('home') }}">
                                    Content
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="hidden lg:inline-block">
                        <form action="{{ route('results') }}" method="get" class="flex">
                            <input type="text" name="subject" class="text-gray-800" placeholder=""
                                value="{{ request()->subject }}">
                            <button type="submit">Search</button>
                        </form>
                    </div>
                    <button
                        class="middle none relative ml-auto h-6 max-h-[40px] w-6 max-w-[40px] rounded-lg text-center text-xs font-medium uppercase text-white transition-all hover:bg-transparent focus:bg-transparent active:bg-transparent disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none lg:hidden"
                        data-collapse-target="navbar">
                        <span class="absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2 transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor"
                                strokeWidth="2">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </span>
                    </button>
                </div>

                <div class="block h-0 w-full basis-full overflow-hidden text-white transition-all duration-300 ease-in lg:hidden"
                    data-collapse="navbar">{{-- not format this line otherwise cllapse will not work!! --}}<div class="container mx-auto pb-2">
                        <ul class="mt-2 mb-4 flex flex-col gap-2">

                            <li class="block p-1 text-sm font-normal leading-normal text-inherit antialiased">
                                <a class="flex items-center" href="{{ route('vl1') }}">
                                    VL 1
                                </a>
                            </li>
                            <li class="block p-1 text-sm font-normal leading-normal text-inherit antialiased">
                                <a class="flex items-center" href="{{ route('about') }}">
                                    About
                                </a>
                            </li>

                            <li class="block p-1 text-sm font-normal leading-normal text-inherit antialiased">
                                <a class="flex items-center" href="{{ route('home') }}">
                                    Content
                                </a>
                            </li>
                        </ul>

                        <div class="none">
                            <form action="{{ route('results') }}" method="get" class="flex">
                                <input type="text" name="subject" class="text-gray-800" placeholder=""
                                    value="{{ request()->subject }}">
                                <button type="submit">Search</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    @yield('content')

    @isset($slot)
        {{ $slot }}
    @endisset
@endsection
