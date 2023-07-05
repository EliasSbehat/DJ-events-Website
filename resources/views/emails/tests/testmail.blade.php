@component('mail::message')
# Request Song

{{ $body }}
<br>
has requested:
<br>
{{ $song }}
<br>
{{ $msg }}
<br>
{{ $date }}

Thanks!
<!-- {{ config('app.name') }} -->
@endcomponent
