@extends('layouts.app')

@section('content')
    <div class="container px-2  mx-auto ">

        <div class="">
            <div class="lg:flex justify-between items-center mb-3 border-b border-gray-800">
                <h1 class="my-0 md:flex items-center gap-2 text-3xl py-3">
                    <a href="javascript:location.reload()"
                        class="text-blue-800 hover:underline">{{ $manuscript->getDisplayname() }}</a>
                    <small>
                        @if ('CSRPC' === $manuscript->name)
                            Printed edition ©
                        @else
                            Images ©
                        @endif
                    </small>
                    </a>


                    @foreach ($manuscript->getMedia('partners') as $partner)
                        <a href="{{ $partner->getCustomProperty('url') ? $partner->getCustomProperty('url') : '#' }}"
                            style="text-decoration: none;" target="_blank">
                            <img src="{{ $partner->getUrl() }}" alt="{{ $manuscript->getMeta('provenance') }}"
                                style="max-width: 150px; max-height: 150px;">
                        </a>
                    @endforeach

                    <a href="https://bnuto.cultura.gov.it" target="_blank">
                        <img class="h-16 inline"
                            src="{{ Vite::asset('resources/images/biblioteca-nazionale-universita-di-torino.jpg') }}"
                            alt="Biblioteca nazionale università di Torino">
                    </a>



                </h1>

                @auth
                    <div>
                        <a href='/mirador-annotations/?manuscript={{ $manuscript->name }}' target="edit-annotations">EDIT
                            ANNOTATIONS</a>
                    </div>
                @endauth
            </div>

            <div class="lg:flex justify-between pb-3">
                <div class="w-1/3">
                    <p>
                        <dcterms:alternative>NT.VMR Doc ID 200001</dcterms:alternative>
                    </p>
                    <p><span class="show-metadata">Shelfmark: </span>
                        <dcterms:isFormatOf>G.VII.15</dcterms:isFormatOf>
                    </p>
                    <p><span class="show-metadata">Date: </span>
                        <dcterms:date xml:lang="en">{{ $manuscript->getMeta('date') }}</dcterms:date>
                    </p>
                    <p><span class="show-metadata">Language: </span>
                        <dcterms:language xml:lang="en">{{ $manuscript->getLangExtended() }}</dcterms:language>
                    </p>
                </div>


                <div class="w-1/3">
                    <p>
                        <span class="show-metadata">
                            Author(s):
                        </span>
                        <dcterms:creator>{{ $manuscript->authors }}</dcterms:creator>
                    </p>

                    @if ($manuscript->getMeta('contributor'))
                        <p>
                            <span class="show-metadata">Encoding: </span>

                            <dcterms:creator>{{ $manuscript->getMeta('contributor') }}</dcterms:creator>
                        </p>
                    @endif

                    @if ($manuscript->nakala_url)
                        <p>
                            <span class="show-metadata">Nakala: </span>
                            <a target="_blank" href="{{ $manuscript->nakala_url }}">
                                Metadata
                            </a>
                        </p>
                    @endif

                    @if ($manuscript->dasch_url)
                        <p>
                            <span class="show-metadata">DaSCH: </span>
                            <a target="_blank" href="{{ $manuscript->dasch_url }}">
                                Metadata
                            </a>
                        </p>
                    @endif

                </div>


                <div class="">
                    <div class="flex justify-end">

                        <div>
                            <button data-ripple-light="true" data-popover-target="menu_folios" class="btn_blue"
                                {{-- close other menu if open --}}
                                onClick="if(document.querySelector('[data-popover=\'menu_html\']').classList.contains('opacity-1')) {
                                            document.querySelector('[data-popover-target=\'menu_html\']').click()
                                        }">
                                Folios PDF
                            </button>
                            <ul role="menu" data-popover="menu_folios" data-popover-placement="bottom-end"
                                class="absolute z-50 min-w-[180px] overflow-auto rounded-md border border-blue-gray-50 bg-white p-3 font-sans text-sm font-normal text-blue-gray-500 shadow-lg shadow-blue-gray-500/10 focus:outline-none">
                                @foreach ($manuscript->folios as $folio)
                                    @if ($folio->getFirstMediaUrl('pdf'))
                                        <li role="menuitem"
                                            class="block w-full cursor-pointer select-none rounded-md px-3 pt-[9px] pb-2 text-start leading-tight transition-all hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900">

                                            <a class="dropdown-item block" href="{{ $folio->getFirstMediaUrl('pdf') }}"
                                                target="_blank">{{ $folio->name }}</a>
                                        </li>
                                    @else
                                        <li role="menuitem"
                                            class="block w-full cursor-pointer select-none rounded-md px-3 pt-[9px] pb-2 text-start leading-tight transition-all hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900">

                                            <a class="dropdown-item block" href="#">{{ $folio->name }} - NO PDF</a>
                                    @endif
                                @endforeach
                            </ul>
                        </div>


                        <div>
                            <button data-ripple-light="true" data-popover-target="menu_html" class="btn_blue z-50"
                                {{-- close other menu if open --}}
                                onClick="if(document.querySelector('[data-popover=\'menu_folios\']').classList.contains('opacity-1')) {
                                    document.querySelector('[data-popover-target=\'menu_folios\']').click()
                                }">
                                Folios TEI/XML
                            </button>
                            <ul role="menu" data-popover="menu_html" data-popover-placement="bottom-start"
                                class="absolute z-50 min-w-[180px] overflow-auto rounded-md border border-blue-gray-50 bg-white p-3 font-sans text-sm font-normal text-blue-gray-500 shadow-lg shadow-blue-gray-500/10 focus:outline-none">

                                @foreach ($manuscript->folios as $folio)
                                    @if ($folio->getFirstMediaUrl('tei'))
                                        <li role="menuitem"
                                            class="block w-full cursor-pointer select-none rounded-md px-3 pt-[9px] pb-2 text-start leading-tight transition-all hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900">

                                            <a class="dropdown-item block" href="{{ $folio->getFirstMediaUrl('tei') }}"
                                                target="_blank">{{ $folio->name }}</a>
                                        </li>
                                    @else
                                        <li role="menuitem"
                                            class="block w-full cursor-pointer select-none rounded-md px-3 pt-[9px] pb-2 text-start leading-tight transition-all hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900">

                                            <a class="dropdown-item block" href="#">{{ $folio->name }} - NO
                                                TEI/XML</a>
                                    @endif
                                @endforeach
                            </ul>
                        </div>





                        <div class="opacity-0"></div>
                    </div>
                </div>

            </div>
        </div>


        <div class="relative">
            <div class="h-[800px] w-full">
                <div x-data="manuscriptShow({
                    manuscriptName: '{{ $manuscript->name }}',
                    manifest: '{{ route('iiif.presentation.manifest', $manuscript->name) }}'
                })">

                    <div id="mirador"></div>

                </div>
            </div>
        </div>



    </div>
@endsection
