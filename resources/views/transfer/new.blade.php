@extends('app')
@section('content')
{!! Form::open(['url' => 'product_stock/store']) !!}

{{Form::label('product_id', '选择产品:')}}
{{Form::select('product_id',$product,null, ['placeholder' => '产品型号'])}}
<p></p>
{{Form::label('amount', '产品数量:')}}
<input name="amount" type="number" value="1" id="amount" style="display: none;">
<p></p>

{{Form::label('serials', '序列号:')}}
<p></p>

<textarea name="serials" cols="50" rows="10" id="serials" style="display: none;"></textarea>

{{Form::hidden('transfer_id',$id)}}

<input class="btn btn-primary btn-lg btn-block" type="submit" value="保存">


{!! Form::close() !!}

<script>
    $( "#product_id" ).change(function() {
        var checkValue=$("#product_id").val();
        console.log(checkValue);
        $.get( "check",  { id: checkValue }, function( data ) {
            console.log( data );
            if(data==1)
                $("#serials").show();
            else
                $("#amount").show();

        });


        $( "#"+checkValue ).show();
//        $( "#pa" ).slideUp();
    });


</script>
@endsection
