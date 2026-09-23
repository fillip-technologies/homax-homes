@php
    $unitTypes = ['Studio', '1 RK', '1 BHK', '1.5 BHK', '2 BHK', '2.5 BHK', '3 BHK', '3.5 BHK', '4 BHK', '4+ BHK', 'Penthouse', 'Villa', 'Duplex', 'Row House', 'Office Space', 'Shop/Showroom', 'Warehouse', 'Plot'];
@endphp
@foreach ($unitTypes as $type)
    <option value="{{ $type }}">
@endforeach
