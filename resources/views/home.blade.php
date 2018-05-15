<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap-theme.css" rel="stylesheet">
    <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.js"></script>
    <title>货品清单</title>
</head>
<body>
    <div id="app">
        <Transaction></Transaction  >
    </div>
    <div class="content">
        <nav class="navbar navbar-default"></nav>
        <div class="container" id="app">
            <div class="panel panel-default panel-primary">
                <div class="panel-heading">
                    <div style="float: left">@{{ v.title }}单:</div>
                    <div style="float: right">操作员: @{{ v.user }}</div>
                    <div style="fix: both;">@{{ v.track.invoice }}</div>
                    <div style="clear: both;"></div>
                </div>
                <div class="panel-body">
                    <table class="table table-responsive" >
                        <tbody >
                        <tr v-if="v.to">
                            <td><b>入库仓:</b></td>
                            <td >@{{ v.to.name }}</td>
                            <td >@{{ v.to.address }}</td>
                            <td >库管员: @{{ v.to.user }}</td>
                        </tr>
                        <tr v-if="v.from">
                            <td><b>出库仓:</b></td>
                            <td >@{{ v.from.name }}</td>
                            <td >@{{ v.from.address }}</td>
                            <td >库管员: @{{ v.from.user }}</td>
                        </tr>
                        <tr v-if="v.order">
                            <td><b>订单编号:@{{ v.order.orderno }}</b></td>
                            <td >@{{ v.order.client }}</td>
                            <td >@{{ v.order.agent }}</td>
                            <td >销售员: @{{ v.order.user }}</td>
                        </tr>
                        </tbody>

                    </table>
                    <label >备注信息:</label>
                    <input type="text" class="form-control" v-model="v.comment">
                </div>

            </div>
            <div class="panel panel-default" v-for="line in v.detail">
                <div class="panel-heading" data-toggle="collapse" v-bind:data-target="'#'+line.id" >
                    <div style="float: left">@{{ line.product_name }}</div>
                    <div style="float: right">@{{ line.amount }}台</div>
                    <div style="clear: both;"></div>
                </div>
                <div v-bind:id="line.id" class="panel-body collapse in" v-if="line.core" >
                    <a v-bind:href="'/serials?id='+serial.id" v-for="serial in line.serials">
                        <span id="serial.id" class="badge pull-right"  style="font-size: large" >@{{ serial.serial }}</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</body>
{{--<script src="/js/vue.js"></script>--}}
<script src="{{ mix('js/app.js') }}"></script>
{{--<script src="https://cdn.bootcss.com/axios/0.18.0/axios.js"></script>--}}
{{--<script src="https://cdn.bootcss.com/vue-router/3.0.1/vue-router.js"></script>--}}
{{--<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.js"></script>--}}
{{--<script type="text/javascript" src="/js/home.js"></script>--}}
</html>