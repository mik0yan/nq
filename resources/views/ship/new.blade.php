@extends('app')
@section('content')
{!! Form::open(['url' => '/product_ship/store']) !!}

{{Form::label('product_id', '选择产品:')}}
{{Form::select('product_id',$product,null, ['placeholder' => '产品型号'])}}
<p></p>
{{Form::label('amount', '产品数量:')}}
    {{Form::number('amount', 1)}}
<p></p>

{{Form::label('serials', '序列号2:')}}
<p></p>

{{Form::textarea('serials','#')}}
{{Form::hidden('transfer_id',$id)}}

<input class="btn btn-primary btn-lg btn-block" type="submit" value="保存">


{!! Form::close() !!}

<script>
    $( "#product_id" ).change(function() {
        var checkValue=$("#product_id").val();
        console.log(checkValue);
        $.get( "check",  { id: checkValue }, function( data ) {
            console.log( data );
//            $.each(data.serials, function(i, ti) {
//                console.log(ti);
//                $("form[method=post]").append(
//                        "<input type='checkbox' name= "+ ti +" value=" + i + " /> " + ti
//                )
//            });
        });


        $( "#"+checkValue ).show();
//        $( "#pa" ).slideUp();
    });


</script>
@endsection
