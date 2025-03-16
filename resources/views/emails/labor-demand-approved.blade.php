@component('mail::message')
# Labor Demand Request Approved

Dear HR Team,

The labor demand request with ID: **{{ $demand->ptk_id }}** has been approved by the General Manager.

**Approved Request Details:**
- Position: {{ $demand->position }}
- Department: {{ $demand->department }}
- Quantity Needed: {{ $demand->qty_needed }}
- Opening Date: {{ date('d M Y', strtotime($demand->opening_date)) }}
- Closing Date: {{ date('d M Y', strtotime($demand->closing_date)) }}

Please proceed with the recruitment process for this position.

@component('mail::button', ['url' => $url])
View Request Details
@endcomponent

Thank you,<br>
{{ config('app.name') }}
@endcomponent