<x-mail::message>
# {{ $carName }} booked

The following car has been booked.
Please wait for owner confirmation to proceed.
<x-mail::table>
|   |   |    
|---|---|
|Car | {{ $carName }} |
|From | {{ $from }} |
|Until | {{ $from }} |
|By | {{ $by }} |
</x-mail::table>

<x-mail::button :url="$url">
Validate the booking status
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
