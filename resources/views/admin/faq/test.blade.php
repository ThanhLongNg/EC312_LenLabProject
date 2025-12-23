@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <h1>Test FAQ Page</h1>
    <p>Nếu bạn thấy trang này, nghĩa là routing và layout hoạt động tốt.</p>
    
    <h3>Categories:</h3>
    <ul>
        @foreach($categories as $key => $name)
            <li>{{ $key }}: {{ $name }}</li>
        @endforeach
    </ul>
    
    <h3>FAQs Count: {{ $faqs->count() }}</h3>
</div>
@endsection