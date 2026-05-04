<!-- Content for Distribution Waterfalls -->

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .tag-text {
            font-size: 0.7rem;
        }

        .tag-cross {
            width: 1rem;
            height: 1rem;
            padding-left: 10px;
        }

        .btn.btn-link {
            width: 20px;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect/dist/vue-multiselect.min.css">
    @vite(['resources/js/waterfall.js'])
    <style>
        .multiselect__tag {
            background-color: #6d7ab4;
        }
        .multiselect__tag span {
            background-color: #6d7ab4;
            border-radius: 5px;
            color: white;
            padding: 2px 5px;
            margin: 2px;
        }
    </style>
@endpush
{{--  <div class="card p-3 shadow-sm">
    
</div>  --}}

@php
    if(auth('admin')->user()->hasRole('partner')) {
        $prefix = 'partner';
    } else {
        $prefix = 'admin';
    }
@endphp

<div id="v-waterfall">
    <div>
        {{-- <counter /> --}}
        <waterfall-component :waterfall="{{$waterfall}}" :waterfalls="{{$waterfalls}}" :classes="{{$classes}}" :buckets="{{$buckets}}" />
        {{--  <hurdle-component :classes="{{$classes}}" />  --}}

    </div>
</div>

@push('script')

    <script>
        window.urls = {
            waterfallSave: "{{ route($prefix . '.waterfalls.store', $deal->id) }}",
        }
        function alpineHelpers() {
            return {
                moneyFormat(el) {
                    let value = el.value;
                    // Remove non-numeric characters except for the decimal point
                    value = value.replace(/[^\d.]/g, '');

                    // Remove leading zeros
                    value = value.replace(/^0+(?=\d)/, '');
                    // If there's more than one decimal point, keep only the first one
                    const parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                    }
                    // Limit the decimal part to two digits
                    if (parts[1]) {
                        parts[1] = parts[1].slice(0, 2);
                        value = parts.join('.');
                    }
                    // Add commas for thousands separator
                    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    // Ensure that the value starts with $ and no other non-numeric characters
                    if (value !== '') {
                        el.value = '$' + value;
                    } else {
                        el.value = '$0';
                    }
                },
                percentFormat(el) {
                    let value = el.value;
                    // Remove non-numeric characters except for the decimal point
                    value = value.replace(/[^\d.]/g, '');

                    // Remove leading zeros
                    value = value.replace(/^0+(?=\d)/, '');
                    // If there's more than one decimal point, keep only the first one
                    const parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                    }
                    // Limit the decimal part to two digits
                    if (parts[1]) {
                        parts[1] = parts[1].slice(0, 2);
                        value = parts.join('.');
                    }
                    // Ensure that the value ends with % and no other non-numeric characters
                    if (value !== '') {
                        el.value = value + '%';
                    } else {
                        el.value = '0%';
                    }
                    el.setSelectionRange(el.value.length - 1, el.value.length - 1);
                }
            }
        }
    </script>
@endpush
