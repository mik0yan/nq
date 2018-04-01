@extends('app')
@section('content')
{!! Form::open(['url' => 'product_purchase/store']) !!}

{{Form::label('user_id', '选择产品:')}}

{{Form::select('user_id',$users,null, ['placeholder' => '选取销售员'])}}


{{Form::label('client_id', '选择医院:')}}

{{Form::select('client_id',$clients,null, ['placeholder' => '选取医院'])}}


{{Form::label('agent_id', '选择代理商:')}}

{{Form::select('agent_id',$agents,null, ['placeholder' => '选取代理商'])}}

<p></p>
{{Form::label('amount', '产品数量:')}}
    {{Form::number('amount', 1)}}
<p></p>

{{Form::label('serials', '序列号:')}}
<p></p>

{{Form::textarea('serials')}}
{{Form::hidden('transfer_id',$id)}}

<input class="btn btn-primary btn-lg btn-block" type="submit" value="保存">


{!! Form::close() !!}

@endsection
