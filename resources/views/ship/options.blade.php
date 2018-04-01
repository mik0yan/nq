@extends('app')
@section('content')

<p id="p11" style="display: none;">First Paragraph</p>
<p id="p12" style="display: none;">Second Paragraph</p>
<p id="p13" style="display: none;">Yet one more Paragraph</p>


{!! Form::open(['url' => 'product_transfer/store']) !!}

{{Form::label('product_id', '选择产品:')}}
{{Form::select('product_id',$product,null, ['placeholder' => '产品型号'])}}
{{Form::hidden('transfer_id',$id)}}

<input class="btn btn-primary btn-lg btn-block" type="submit" value="保存">


<script>
    $( "#product_id" ).change(function() {
        var checkValue=$("#product_id").val();
        $("input[type=checkbox]").remove();
        $.get( "check",  { id: checkValue }, function( data ) {
            console.log( data );
            if(data==1)
                $.get( "item",  { id: checkValue }, function( data ) {
                    console.log( data.serials );
                    $.each(data.serials, function(key, item) {
                        console.log(item);
//                $("form[method=post]").after(
                        $("#product_id").after(
                                "</br><input type='checkbox' name= "+ key +" value=" + item + " /> " + item
                        )
                    });
                });
            else
                $("#product_id").after(
                        '<label for="amount">产品数量:</label>'+
                        '<input name="amount" type="number" value="1" id="amount" >'
            )

        });

        console.log(checkValue);



        $( "#"+checkValue ).show();
//        $( "#pa" ).slideUp();
    });
</script>



@endsection
