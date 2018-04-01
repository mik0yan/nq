@extends('app')

@section('title'){{$title}}@endsection

@section('content')

    <table class="table table-striped">
        <tr>
            <td>发货仓:{{$from_stock['name']}}</td>
            <td>{{$from_stock['address']}}</td>
            <td>发货日期:{{$transfer['ship_at']}}</td>
        </tr>
        <tr>
            <td>备注信息</td>
            <td colspan="2">{{$transfer['comment']}}</td>
        </tr>
    </table>

    <a href="/product_ship/{{$transfer['id']}}/new" class="btn btn-info" role="button">新增</a>

    <a href="/ship" class="btn btn-danger" role="button">返回</a>

    <table class="table table-striped" width="95%">
        <thead>
        <tr>
            <th>操作</th><th>型号</th><th>名称</th><th>数量</th><th>序  号</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($items as $item)
            <tr>
                <th width="8%">{{ $item['id'] }}</th>
                <td width="10%">{{ $item['sku'] }}</td>
                <td width="40%">{{ $item['name'] }}</td>
                <td width="10%">{{ $item['amount'] }}</td>
                <td width="40%">
                    <!-- {{ $item['serials'] }} -->
                    @foreach ($item['serials'] as $k=>$serial)
                        <p class="tag tag-info">{{$k+1}}:{{$serial['serial_no']}}</p>
                    @endforeach
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>



@endsection
