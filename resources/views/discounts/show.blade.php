@extends('layouts.app')

@section('content')
    <h1>Discount Details</h1>
    <p>ID: {{ $discount->id }}</p>
    <p>Name: {{ $discount->name }}</p>
    <p>Type: {{ $discount->discountType }}</p>
    <p>Amount: {{ $discount->value }}</p>
    <a href="{{ route('discounts.edit', $discount->id) }}">Edit</a>
    <a href="{{ route('discounts.index') }}">Back to List</a>
@endsection
