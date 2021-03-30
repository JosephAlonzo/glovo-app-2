@if ($message = Session::get('success') )
    <section class="spy-width-medium-10-10">
        <article>
            <div style="width: 100% !important;" class="alert success">
                <dl>
                    <dt> Success!</dt>
                    <dd>{{ $message }}</dd>
                </dl>
            </div>
        </article>
        @push('customscripts')
            <script> ShopifyApp.flashNotice("{!! str_replace(array("\n","\t"), " ", $message) !!}"); </script>
        @endpush
    </section>
@endif

@if ($message = Session::get('errors') )
    <section class="spy-width-medium-10-10">
        <article>
            <div style="width: 100% !important;" class="alert warning">
                <dl>
                    <dt> Warning!</dt>
                    <dd>{!! $message !!}</dd>
                </dl>
            </div>
        </article>
        @push('customscripts')
            <script> ShopifyApp.flashError("{!! strip_tags(str_replace(array("\n","\t"), " ", $message)) !!}"); </script>
        @endpush
    </section>
@endif
