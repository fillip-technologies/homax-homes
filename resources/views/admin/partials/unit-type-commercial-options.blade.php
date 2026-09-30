@php
    $commercialTypes = [
        'Office Space',
        'Shop',
        'Showroom',
        'Commercial Office',
        'Bare Shell Office',
        'Furnished Office',
        'Warehouse / Godown',
        'Retail Space',
        'Co-working Space',
        'Commercial Land / Plot',
        'Industrial Shed',
        'Restaurant / Cafe Space',
        'Clinic / Medical Space',
    ];
@endphp
@foreach ($commercialTypes as $type)
    <option value="{{ $type }}">
@endforeach
