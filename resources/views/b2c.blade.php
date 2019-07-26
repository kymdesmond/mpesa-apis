@extends('layouts.app')

@section('content')
    <div class="container">
        <form method="post" route="/b2c">
            @csrf
            <label for="amount">Amount</label>
            <input class="form-control" type="number" name="amount" placeholder="100">
            <label for="remarks" >Remarks</label>
            <input class="form-control" type="text" name="remarks" placeholder="Remarks">
            <label for="Occasion" >Occasion</label>
            <input class="form-control" type="text" name="occasion" placeholder="Occasion">
            <br>
            <input class="form-control btn-primary" type="submit">
        </form>
    </div>
@endsection
