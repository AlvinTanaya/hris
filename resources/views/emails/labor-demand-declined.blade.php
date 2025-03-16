@component('mail::message')
# Labor Demand Request Declined

Dear HR Team,

The labor demand request with ID: **{{ $demand->ptk_id }}** has been declined by the General Manager.

**Declined Request Details:**
- Position: {{ $demand->position }}
- Department: {{ $demand->department }}
- Quantity Needed: {{ $demand->qty_needed }}

**Reason for Declining:**
{{ $demand->declined_reason }}

@component('mail::button', ['url' => $url])
View Request Details
@endcomponent

Thank you,<br>
{{ config('app.name') }}
@endcomponent