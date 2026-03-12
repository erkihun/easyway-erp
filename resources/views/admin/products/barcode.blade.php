@extends('layouts.admin')

@section('content')
<div class="card">
    <h3>{{ $product->name }} - Barcode</h3>
    <div>{!! $barcode !!}</div>
</div>
@endsection



