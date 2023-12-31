@if (!is_array($manuscripts))
    {{ $manuscripts->links() }}
@endif
<table class="results">
    <thead>
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Folios</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($manuscripts as $manuscript)
            <tr>
                <td><span class="bg-gray-600 text-white rounded px-2 py-1 font-bold">{{ $manuscript->temporal }}</span>
                </td>
                <td>
                    @if ($manuscript->folios->first() && $manuscript->folios->first()->getFirstMedia())
                        <img src="{{ route('iiif.image.requests', [$manuscript->folios->first()->getFirstMedia()->id, 'full', '100,', '0', 'default', 'jpg']) }}"
                            class="max-w-md border border-gray-300 bg-white p-1 rounded" loading="lazy">
                    @endif
                </td>
                <td>
                    <a class="text-blue-800"
                        href="{{ route('manuscript.show', $manuscript->name) }}">{{ $manuscript->getDisplayname() }}</a>
                    @if ($loop->first)
                        <p class="text-gray-800 whitespace-nowrap">
                            <span>
                                <dcterms:isformatof>G.VII.15</dcterms:isformatof>
                            </span><br>
                            <span>
                                <dcterms:language xml:lang="en">{{ $manuscript->getLangExtended() }}</dcterms:language>
                            </span><br>
                            <span>
                                <dcterms:date xml:lang="en">{{ $manuscript->getMeta('date') }}</dcterms:date>
                            </span><br>
                        </p>
                    @endif
                </td>
            </tr>
        @endforeach
        @if (count($manuscripts) === 0)
            <tr>
                <td colspan="4" class="text-center">
                    Sorry, nothing found. Please try to change your search query.
                </td>
            </tr>
        @endif
    </tbody>
</table>
@if (!is_array($manuscripts))
    {{ $manuscripts->links() }}
@endif
