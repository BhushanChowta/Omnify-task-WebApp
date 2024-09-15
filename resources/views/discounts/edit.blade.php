@extends('layouts.app')

@section('title', 'Edit Discount')

@section('content')
    <h1>Edit Discount</h1>
    @include('discounts.form', ['discount' => $discount])
@endsection
