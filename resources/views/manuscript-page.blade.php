{{-- {!! $manuscriptContentHtml->content !!} --}}
{!! $manuscriptContentHtml ? $manuscriptContentHtml->getAlteredHtml() : 'NO CONTENT FOUND' !!}

