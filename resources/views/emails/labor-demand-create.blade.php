@component('mail::message')
# New Labor Demand Request

Dear General Manager,

There is a new labor demand request with ID: **{{ $demand->ptk_id }}**

**Details:**
- Position: {{ $demand->position }}
- Department: {{ $demand->department }}
- Quantity Needed: {{ $demand->qty_needed }}
- Opening Date: {{ date('d M Y', strtotime($demand->opening_date)) }}
- Closing Date: {{ date('d M Y', strtotime($demand->closing_date)) }}

Please review this request and either approve or decline it.

@component('mail::button', ['url' => $url])
Review Request
@endcomponent

Thank you,<br>
{{ config('app.name') }}
@endcomponent