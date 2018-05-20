<template>
    <div>
        <h1>归还单</h1>

        <el-table
                :data="lends"
                style="width: 100%"
                max-height="250">
            <el-table-column
                fixed
                prop="id"
                label="序号">
            </el-table-column>
            <el-table-column
                    fixed
                    prop="user"
                    label="出库员">
            </el-table-column>
            <el-table-column
                    fixed
                    prop="hospital"
                    label="医院">
            </el-table-column>
            <el-table-column
                    fixed
                    prop="agent"
                    label="代理商">
            </el-table-column>
            <el-table-column
                    fixed
                    prop="comment"
                    label="备注">
            </el-table-column>
            <el-table-column
                    fixed
                    prop="ship_at"
                    label="发货期">
            </el-table-column>
        </el-table>


        <el-form label-position ='right' label-width="120px">
            <el-form-item  label="出库仓" >
                <el-input v-model="outstock.name" :disabled="true"></el-input>
            </el-form-item>
            <el-form-item  label="出库地址">
                <el-input v-model="outstock.address" :disabled="true"></el-input>
            </el-form-item>
            <el-form-item  label="出库员">
                <el-select
                        v-model="outstock.user_id"
                        filterable
                        style="width: 300px">
                    <el-option
                            v-for="item in users"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item  label="订单信息">
                <el-select
                        v-model="outstock.order_id"
                        filterable
                        style="width: 500px">
                    <el-option
                            v-for="item in orders"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                            :disabled="item.disabled">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item  label="发货日期">
                <el-col :span="10">
                    <el-date-picker
                            type="date"
                            placeholder="发货日期"
                            v-model="transfer.ship_at"
                            style="width: 100%;"
                            format="yyyy 年 MM 月 dd 日"
                            value-format="yyyy-MM-dd">
                    </el-date-picker>
                </el-col>
            </el-form-item>
            <el-form-item  label="到货日期">
                <el-col :span="10">
                    <el-date-picker
                            type="date"
                            placeholder="到货日期"
                            v-model="transfer.arrival_at"
                            style="width: 100%;"
                            format="yyyy 年 MM 月 dd 日"
                            value-format="yyyy-MM-dd">
                    </el-date-picker>
                </el-col>
            </el-form-item>
            <el-form-item  label="物流单号">
                <el-input v-model="transfer.track_id"></el-input>
            </el-form-item>
            <el-form-item  label="协议号">
                <el-input v-model="transfer.contractno"></el-input>
            </el-form-item>

            <el-form-item  label="发票号">
                <el-input v-model="transfer.invoiceno"></el-input>
            </el-form-item>
            <el-form-item  label="备注">
                <el-input v-model="transfer.comment"></el-input>
            </el-form-item >
            <el-form-item
                    v-for="(product, index) in transfer.list"
                    :label="'产品编号:' + product.id"
                    :key="index"
                    style="width: 100%;">
                <el-row>
                    <el-col :span="2">
                        <el-button @click.prevent="removeProduct(product)" type="danger" icon="el-icon-delete" circle></el-button>
                    </el-col >
                    <el-col :span="8">
                        <el-select
                                v-model="product.id"
                                filterable
                                placeholder="请输入关键词"
                                @change="refreshProduct(product)"
                                style="width: 300px">
                            <el-option
                                    v-for="item in list"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id">
                            </el-option>
                        </el-select>
                    </el-col>
                    <el-col :span="8" >
                        <el-input-number v-model="product.num"  :min="0" :max="product.max" label="描述文字"></el-input-number>
                    </el-col>
                    <el-col :span="6" v-if="product.core">
                        <el-input
                                v-model="product.serials"
                                label="产品编号"
                                clearable
                                @keyup.enter.native="submit(product)"
                                placeholder="请输入序列号">
                        </el-input>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="12" :offset="6">
                        <el-tag
                                v-for="tag in product.seriallist"
                                :key="tag"
                                closable
                                type="success"
                                @close="handleClose(tag,product)">
                                {{tag}}
                        </el-tag>
                    </el-col>
                </el-row>
                <!--<el-row >-->
                    <!--<el-col v-if="product.core">-->
                        <!--<el-input-->
                                <!--v-model="product.comment"-->
                                <!--label="产品备注"-->
                                <!--clearable-->
                                <!--placeholder="产品备注">-->
                        <!--</el-input>-->
                    <!--</el-col>-->
                <!--</el-row>-->
            </el-form-item>
            <el-form-item>
                <el-button type="success" @click="addProduct" icon="el-icon-plus" circle></el-button>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="postData">保存</el-button>
                <el-button type="text" @click="$router.back(-1)">取消</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>

<script>
    import Vue from 'vue'
//    import VueRouter from 'vue-router'
    import axios from 'axios'
//    import VueAxios from 'vue-axios'

    export default {
        porps:['stock_id'],
        mount(){
//            axios.get('/api/stock/'+this.$route.params.stock_id).then((response) => {
//                console.log(response.data);
//                this.stock = response.data;
//            });
//            this.send()
        },
        mounted(){
            this.send();
            this.products();
            this.getUsers();
            this.getStocks();
            this.getOrders();
            this.getLends();
        },
        data() {
            return {
                input: this.$route.params.stock_id,
                outstock: {
                    name: '',
                },
                instock: {
                    id: null,
                },
                transfer:{
                    track_id: '',
                    to_stock_id: '',
                    from_stock_id: '',
                    invoiceno:'',
                    contractno: '',
                    comment: '',
                    ship_at: this.$moment(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                    arrival_at: this.$moment(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                    user_id: "",
                    list:[{
                        name: '',
                        id:'',
                        num: 1,
                        core: false,
                        serials: '',
                        seriallist: []
                    }]
                },
                list:[],
                users:[],
                stocks:[],
                lends:[],
                orders:[],
            };
        },
        computed:{
            
        },
        methods:{
            getUsers(){
                axios.get('/api/userlist').then((response)=>{
                    this.users = response.data;
                });
            },

            getStocks(){
                axios.get('/api/stocklist').then((response)=>{
                    this.stocks = response.data;
                });
            },

            getOrders(){
                axios.get('/api/orderlist').then((response)=>{
                    this.orders = response.data;
                    console.log(this.order);
                });
            },

            getLends(){
                axios.get('/api/lendlist/' + this.$route.params.stock_id).then((response)=>{
                    this.lends = response.data;
                    console.log(this.order);
                });
            },

            send(){
                axios.get('/api/stock/'+this.$route.params.stock_id).then((response) => {
                    console.log(response.data);
                    this.outstock = response.data;
                    this.transfer.user_id = response.data.user_id
                    this.transfer.from_stock_id = response.data.id
                    console.log(this.transfer);
                });
            },
            products()
            {
                axios.get('/api/productlist').then((response)=>{
                    this.list = response.data;
                    console.log(response.data);
                });
            },
            addProduct()
            {
                this.transfer.list.push({
                    name: '',
                    id:''
                });
            },

            //提交序列号 判断是否重复扫描 是否已存在
            submit:function(data)
            {
                if(data.seriallist.indexOf(data.serials)!=-1)
                {
                    alert('重复扫描')
                    data.serials = ''
                }
                else
                {
                    axios.get('/api/serialExist',c).then((response)=>{
                        if(response.data ==1)
                        {
                            data.seriallist.push(data.serials)
                            data.serials = ""
                            data.num = data.seriallist.length

                        }
                        else if(response.data ==2)
                        {
                            alert('序列号弄错产品型号')
                            data.serials = ""
                        }
                        else
                        {
                            alert('序列号不存在')
                            data.serials = ""
                        }
                    })

                }
            },

            handleClose:function (tag,data) {
                data.seriallist.splice(data.seriallist.indexOf(tag), 1);
                data.num = data.seriallist.length
            },
            removeProduct(item)
            {
                var index = this.transfer.list.indexOf(item)
                if (index !== -1) {
                    this.transfer.list.splice(index, 1)
                }
            },

            refreshStock(item)
            {
                axios.get('/api/stock/'+item.id).then((response)=>{
                    this.instock =  response.data;
                    console.log(this.instock)
                    this.transfer.to_stock_id = response.data.id
                });
            },

            refreshProduct(item)
            {
                axios.get('/api/product/' + item.id ).then((response)=>{
                    if(response.data.core == 1)
                    {
                        item.core = 1;
                        item.num = 0;
                    }
                    else
                    {
                        item.core = 0;
                    }
//                    item.core = response.data.core === 1;

                    console.log("print response ")

                    console.log(response)
                });

                axios.get('/api/product/'+ item.id + '/count',{
                    params: {
                        stock_id: this.transfer.from_stock_id
                    }
                }).then((response)=>{
                    console.log(response.data)
                    item.max = response.data
//                    console.log(item.max)
                });
                item.serials ='';
                item.seriallist = [];
            },

            postData()
            {
                console.log(this.transfer);
                axios.post('/transfer/' + this.$route.params.stock_id + '/trans', this.transfer).then((response)=>{
                    console.log("print response ")
                    console.log(this.transfer)
                });
                window.location.href = '/transfer2?catalog=4';

            }

//            remoteMethod(query) {
//                if (query !== '') {
//                    this.loading = true;
//                    setTimeout(() => {
//                        this.loading = false;
//                        this.options4 = this.name.filter(item => {
//                            return item.name.indexOf(query) > -1;
//                        });
//                    }, 200);
//                } else {
//                    this.options4 = [];
//                }
//            }
        }
    }
</script>


<style>
    .el-tag + .el-tag {
        margin-left: 10px;
    }
    .button-new-tag {
        margin-left: 10px;
        height: 32px;
        line-height: 30px;
        padding-top: 0;
        padding-bottom: 0;
    }
    .input-new-tag {
        width: 90px;
        margin-left: 10px;
        vertical-align: bottom;
    }
</style>