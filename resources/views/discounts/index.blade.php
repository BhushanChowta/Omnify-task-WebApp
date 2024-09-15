@extends('layouts.app')

@section('content')
    <h1>Discounts</h1>
    <a href="{{ route('discounts.create') }}">Create New Discount</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Discount Type</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($discounts as $discount)
                <tr>
                    <td>{{ $discount->id }}</td>
                    <td>{{ $discount->name }}</td>
                    <td>{{ $discount->discountType }}</td>
                    <td>{{ $discount->value }}</td>
                    <td>
                        <a href="{{ route('discounts.show', $discount->id) }}">View</a>
                        <a href="{{ route('discounts.edit', $discount->id) }}">Edit</a>
                        <form action="{{ route('discounts.delete', $discount->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
