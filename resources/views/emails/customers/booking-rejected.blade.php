<x-mail::message>
# {{ $carName }} booked

The following car booked was rejected by the owner.
Please wait for owner confirmation to proceed.
<x-mail::table>
|   |   |
|---|---|
|Car | {{ $carName}} |
|From | {{ $from }} |
|Until | {{ $from }} |
</x-mail::table>

<x-mail::button :url="$url">
Check the booking status
</x-mail::button>

<x-mail::button :url="$url2">
Book different car
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
