<x-mail::message>
# {{ $car }} Rental Status
<x-mail::table>
|   |   |
|---|---|
|Car | {{ $car}} |
|From | {{ $from }} |
|Until | {{ $from }} |
|Status | {{ $status }} |
</x-mail::table>

<x-mail::button :url="$url">
Check the booking status
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
