<x-mail::message>
# {{ $carName }} booking process approved

The owner has aprrove the booking process.
Please proceed with proof of payment before picking up the vehicle.
<x-mail::table>
|   |   |
|---|---|
|Car | {{ $carName}} |
|From | {{ $from }} |
|Until | {{ $from }} |
|Deposit | RM {{ $deposit }} |
</x-mail::table>

<x-mail::button :url="$url">
Check the booking status
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
