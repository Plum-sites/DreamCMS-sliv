@component('vendor.mail.html.layout')
    {{-- Header --}}
    @slot('header')
        @component('vendor.mail.html.header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    {{-- Body --}}
    {!! $slot !!}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('vendor.mail.html.subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('vendor.mail.html.footer')
            © {{ date('Y') }} {{ config('app.name') }}. @lang('Все права защищены.')
        @endcomponent
    @endslot
@endcomponent
