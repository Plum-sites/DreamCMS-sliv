<script type="application/javascript">
    window.Laravel.charts.{!! $element !!} = {
        type: '{!! $type !!}',
        style:{
            position: 'relative'
        },
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: {!! json_encode($datasets) !!}
        },
        @if(!empty($optionsRaw))
        options: {!! $optionsRaw !!}
                @elseif(!empty($options))
            options: {!! json_encode($options) !!}
    @endif
    };
</script>
