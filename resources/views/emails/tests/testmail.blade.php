@component('mail::message')
# Request Song

Mobile Number: {{ $phone }}
<br>
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
