@extends('app')
@section('content')

    {!! Form::open(['url' => 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']])  !!}
        <label for="q">筛选关键词:</label>
        <input name="q" id="q" type="text" id="filter" >
        <button type="submit" id='refresh' value="Reset">重置</button>
    {!! Form::close() !!}

    {!! Form::open(['url' => 'admin/product_purchase/store']) !!}

    <p></p>
    {{Form::label('product_id', '选择产品:')}}
    {{Form::select('product_id',$product,null, ['placeholder' => '产品型号'])}}
    <p></p>
    {{Form::label('amount', '产品数量:')}}
    <input name="amount" type="number" value="1" id="amount" style="display: none;">
    <p></p>

    {{Form::label('serials', '序列号:')}}
    <p></p>


    <textarea name="serials" cols="50" rows="10" id="serials" style="display: none;"></textarea>
    <p></p>
    {{Form::label('serials', '批量序列号:')}}

    <p></p>

    <input name="begin"  id="begin" class="batch" style="display: none;">
    <p></p>

    <input name="end"  id="end" class="batch" style="display: none;">

    {{Form::hidden('transfer_id',$id)}}

    <input name="save" id='save' class="btn btn-primary btn-lg btn-block" type="submit" value="保存" style="display: none;">


    {!! Form::close() !!}

    <script>
        $(document).ready(function(){

            $("#q").focus();

        });

        $(function(){
            $('#q').bind('keypress',function(event){
                if(event.keyCode == "13")
                {
                    $newurl = document.URL.toLowerCase().split('?')[0] + "?q=" + $('#q').val();
                    console.log( $newurl);
                    window.location = $newurl;
                }
            });
            $('#refresh').click(function() {
                location.reload();
            });
        });

        $( "#product_id" ).change(function() {
            var checkValue=$("#product_id").val();
            console.log(checkValue);
            $.get( "check",  { id: checkValue }, function( data ) {
                console.log( data );
                if(data==1){
                    $("#serials").show();
                    $("#save").show();
                    $(".batch").show();
                }

                else
                {
                    $("#save").show();
                    $("#amount").show();
                }

            });


            $( "#"+checkValue ).show();
//        $( "#pa" ).slideUp();
        });


    </script>
@endsection
