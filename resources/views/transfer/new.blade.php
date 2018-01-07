@extends('app')
@section('content')
{!! Form::open(['url' => 'admin/product_stock/store']) !!}

{{Form::label('product_id', '选择产品:')}}
{{Form::select('product_id',$product,null, ['placeholder' => '产品型号'])}}
<p></p>
{{Form::label('amount', '产品数量:')}}
    {{Form::number('amount', 1)}}
<p></p>

{{Form::label('serials', '序列号:')}}
<p></p>

{{Form::textarea('serials','#')}}
{{Form::hidden('transfer_id',$id)}}

<input class="btn btn-primary btn-lg btn-block" type="submit" value="保存">


{!! Form::close() !!}

@endsection
