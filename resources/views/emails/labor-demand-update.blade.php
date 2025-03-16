@component('mail::message')
# Labor Demand Request Updated

Dear General Manager,

Labor demand request with ID: **{{ $demand->ptk_id }}** has been updated.

**Updated Details:**
- Position: {{ $demand->position }}
- Department: {{ $demand->department }}
- Quantity Needed: {{ $demand->qty_needed }}
- Opening Date: {{ date('d M Y', strtotime($demand->opening_date)) }}
- Closing Date: {{ date('d M Y', strtotime($demand->closing_date)) }}

Please review the updated request and provide your response.

@component('mail::button', ['url' => $url])
Review Updated Request
@endcomponent

Thank you,<br>
{{ config('app.name') }}
@endcomponent