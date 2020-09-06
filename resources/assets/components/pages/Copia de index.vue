<template>
    <div>
        <vue-headful :title="profit.toFixed(3) + '% - ' + monitor + ' Dashboard'" />
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="text-center p-3 widget_social_icons box_shadow">
                    <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                        <div v-else>
                            <p>Hoy</p>
                            <div class="text-ash">
                                <pulse-loader v-if="!productionToday" :loading="true" :color="color" :size="size"></pulse-loader>
                                <h4 v-else class="mb-0 mt-2 text_size"><i class="fa fa-btc" style="color: blue; font-size: inherit;"></i> {{  toFix(productionToday, 8) }}</h4>
                                <h5 class="mb-0 mt-2 text_size"><i class="fa fa-usd" style="color: green; font-size: inherit;"></i> {{ toFix((productionToday * usdBtcData.Last), 2) }}</h5>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col-lg-3  col-sm-6 mb-3">
                <div class="text-center p-3 widget_social_icons box_shadow ">
                    <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                        <div v-else>
                            <p>Ayer</p>
                            <div class="text-ash">
                                <pulse-loader v-if="!account" :loading="true" :color="color" :size="size"></pulse-loader>
                                <h4 v-else class="mb-0 mt-2 text_size"><i class="fa fa-btc" style="color: blue; font-size: inherit;"></i> {{ account.earn24Hours }}</h4>
                                <h5 class="mb-0 mt-2 text_size"><i class="fa fa-usd" style="color: green; font-size: inherit;"></i> {{ toFix((account.earn24Hours * usdBtcData.Last), 2) }}</h5>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col-lg-3  col-sm-6 mb-3">
                <div class="text-center p-3 widget_social_icons box_shadow">
                    <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                        <div v-else>
                            <p>Balance</p>
                            <div class="text-ash">
                                <pulse-loader v-if="!account" :loading="true" :color="color" :size="size"></pulse-loader>
                                <h4 v-else class="mb-0 mt-2 text_size"><i class="fa fa-btc" style="color: blue; font-size: inherit;"></i> {{ account.balance }}</h4>
                                <h5 class="mb-0 mt-2 text_size"><i class="fa fa-usd" style="color: green; font-size: inherit;"></i> {{ toFix((account.balance * usdBtcData.Last), 2) }}</h5>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col-lg-3  col-md-6 mb-3">
                <div class="text-center p-3 widget_social_icons box_shadow">
                    <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                        <div v-else>
                            <p>Ganancias Totales</p>
                            <div class="text-ash">
                                <pulse-loader v-if="!account" :loading="true" :color="color" :size="size"></pulse-loader>
                                <h4 v-else class="mb-0 mt-2 text_size"><i class="fa fa-btc" style="color: blue; font-size: inherit;"></i> {{ account.earnTotal }}</h4>
                                <h5 class="mb-0 mt-2 text_size"><i class="fa fa-usd" style="color: green; font-size: inherit;"></i> {{ toFix((account.earnTotal * usdBtcData.Last), 2) }}</h5>
                            </div>
                        </div>
                </div>
            </div>
            
            <div class="col-lg-3  col-md-6 mb-3">
                <div class="text-center p-3 widget_social_icons box_shadow">
                    <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                        <div v-else>
                            <div class=" widget_social_inner1">
                                <i class="fa fa-line-chart fb_text" style="color:green;"></i>
                            </div>
                            <div class="text-ash">
                                <pulse-loader v-if="!totalSaldoDollar" :loading="true" :color="color" :size="size"></pulse-loader>
                                <h4 v-else :class="[(btcDiff >= 0) ? 'text_green mb-0 mt-2' : 'text_red mb-0 mt-2' ]"> {{ toFix(btcDiff, 8) }} BTC</h4>
                                
                                <p class="m-0 mt-2">
                                    Ganancias 
                                    (<small :class="[(btcDiff >= 0) ? 'text_green_small' : 'text_red_small' ]">${{ toFix(dollarDiff, 2) }}</small>)
                                    (<small :class="[(btcDiff >= 0) ? 'text_green_small' : 'text_red_small' ]">{{ toFix(percentageDiffProfit, 2) }} %</small>)
                                        <img :src="[(btcDiff >= 0) ? '/images/Icons-10(1).png' : '/images/Icons_1-10.png' ]">
                                </p>
                            </div>
                        </div>
                </div>
            </div>
        <!--========================Balances===========================-->

            <div v-for="(balance, index) in balances" :key="index"  class="col-lg-3  col-sm-6 mb-3">
                <div class="text-center p-3 widget_social_icons box_shadow">
                     <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                        <div v-else>
                            <div class="widget_social_inner1">
                                <img :src="getDataCoin(balance.Currency)" height="30px" width="30px" alt="">
                            </div>
                            <div class="text-ash">
                                <h4 class="mt-2 text_size"> {{ balance.Available }}</h4>
                                <p class="m-0 mt-2">Saldo {{ balance.Currency }}</p>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        <!--========================Mercados===========================-->
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <b-card header='Mercados' header-tag="h4" class="bg-success-card">
                    <pulse-loader v-if="!totalSaldoDollar" :loading="true" :color="color" :size="size"></pulse-loader>
                    <b-tabs v-else>
                        <b-tab title="BTC Market" class="sales_text">
                            <div>
                                <div class="table-responsive">
                                    <table id="mytable" class="table table-bordred table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Market</th>
                                                <th>% Change</th>
                                                <th>Volumen</th>
                                                <th>Bid</th>
                                                <th>Ask</th>
                                                <th>Bid - Ask</th>
                                                <th>Creado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in btcMarket" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td><router-link :to="'/trading-btc/' + item.MarketName" > {{ item.MarketName }} </router-link></td>
                                                <td :class="[((((item.Last - item.PrevDay)/item.PrevDay) * 100) >= 0) ? 'text_green_medium mb-0 mt-2' : 'text_red_medium mb-0 mt-2' ]" style="font-weight:700;">
                                                    {{ toFix(((item.Last - item.PrevDay)/item.PrevDay) * 100, 2) }}
                                                </td>
                                                <td>{{ toFix(item.BaseVolume, 2) }}</td>
                                                <td>{{ toFix(item.Bid, 8) }}</td>
                                                <td>{{ toFix(item.Ask, 8) }}</td>
                                                <td>{{ toFix(item.Ask - item.Bid, 8) }}</td>
                                                <td>{{ timeClose(item.Created) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </b-tab>
                        <b-tab title="USD Market" class="sales_text">
                            <div>
                                <div class="table-responsive">
                                    <table id="mytable" class="table table-bordred table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Market</th>
                                                <th>% Change</th>
                                                <th>Volumen</th>
                                                <th>Bid</th>
                                                <th>Ask</th>
                                                <th>Bid - Ask</th>
                                                <th>Creado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in usdMarket" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ item.MarketName }}</td>
                                                <td :class="[((((item.Last - item.PrevDay)/item.PrevDay) * 100) >= 0) ? 'text_green_medium mb-0 mt-2' : 'text_red_medium mb-0 mt-2' ]" style="font-weight:700;">
                                                    {{ toFix(((item.Last - item.PrevDay)/item.PrevDay) * 100, 2) }}
                                                </td>
                                                <td>{{ toFix(item.BaseVolume, 2) }}</td>
                                                <td>{{ toFix(item.Bid, 8) }}</td>
                                                <td>{{ toFix(item.Ask, 8) }}</td>
                                                <td>{{ toFix(item.Ask - item.Bid, 8) }}</td>
                                                <td>{{ timeClose(item.Created) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>                        
                        </b-tab>
                        <b-tab title="ETH Market" class="sales_text">
                            <div>
                                <div class="table-responsive">
                                    <table id="mytable" class="table table-bordred table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Market</th>
                                                <th>% Change</th>
                                                <th>Volumen</th>
                                                <th>Bid</th>
                                                <th>Ask</th>
                                                <th>Bid - Ask</th>
                                                <th>Creado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in ethMarket" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ item.MarketName }}</td>
                                                <td :class="[((((item.Last - item.PrevDay)/item.PrevDay) * 100) >= 0) ? 'text_green_medium mb-0 mt-2' : 'text_red_medium mb-0 mt-2' ]" style="font-weight:700;">
                                                    {{ toFix(((item.Last - item.PrevDay)/item.PrevDay) * 100, 2) }}
                                                </td>
                                                <td>{{ toFix(item.BaseVolume, 2) }}</td>
                                                <td>{{ toFix(item.Bid, 8) }}</td>
                                                <td>{{ toFix(item.Ask, 8) }}</td>
                                                <td>{{ toFix(item.Ask - item.Bid, 8) }}</td>
                                                <td>{{ timeClose(item.Created) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>                                        
                        </b-tab>
                        <b-tab title="USDT Market" class="sales_text">
                            <div>
                                <div class="table-responsive">
                                    <table id="mytable" class="table table-bordred table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Market</th>
                                                <th>% Change</th>
                                                <th>Volumen</th>
                                                <th>Bid</th>
                                                <th>Ask</th>
                                                <th>Bid - Ask</th>
                                                <th>Creado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in usdtMarket" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ item.MarketName }}</td>
                                                <td :class="[((((item.Last - item.PrevDay)/item.PrevDay) * 100) >= 0) ? 'text_green_medium mb-0 mt-2' : 'text_red_medium mb-0 mt-2' ]" style="font-weight:700;">
                                                    {{ toFix(((item.Last - item.PrevDay)/item.PrevDay) * 100, 2) }}
                                                </td>
                                                <td>{{ toFix(item.BaseVolume, 2) }}</td>
                                                <td>{{ toFix(item.Bid, 8) }}</td>
                                                <td>{{ toFix(item.Ask, 8) }}</td>
                                                <td>{{ toFix(item.Ask - item.Bid, 8) }}</td>
                                                <td>{{ timeClose(item.Created) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>                                  
                        </b-tab>
                        <b-tab title="Historico de Ordenes" class="sales_text">
                            <div class="table-responsive">
                                <div v-if="!historicOrders.length">No hay Historico de Ordenes</div>
                                <table v-else id="mytable" class="table table-bordred table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Mercado</th>
                                            <th>Precio en BTC</th>
                                            <th>Cantidad COIN</th>
                                            <th>Cantidad BTC</th>
                                            <th>Ejecutada</th>
                                            <!-- <th>Comisión</th> -->
                                            <th>En $</th>
                                            <th>%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(order, index) in historicOrders" :key="index">
                                            <td v-if="historicOrders[index].OrderType == 'LIMIT_BUY'">COMPRA</td>
                                            <td v-else>VENTA</td>                                    
                                            <td>{{ historicOrders[index].Exchange }}</td>
                                            <td>{{ historicOrders[index].Limit }} $({{ (historicOrders[index].Limit * usdBtcData.Last).toFixed(2) }})</td>
                                            <td>{{ historicOrders[index].Quantity }}</td>
                                            <td>{{ (historicOrders[index].Price - historicOrders[index].Commission).toFixed(8)}}</td>
                                            <td>{{ timeClose(historicOrders[index].Closed) }}</td>
                                            <!-- <td>{{ historicOrders[index].Commission }}</td> -->
                                            <td>{{ ((historicOrders[index].Price - historicOrders[index].Commission) * usdBtcData.Last).toFixed(2) }}</td>
                                            <td v-if="historicOrders[index].OrderType == 'LIMIT_SELL' && index < (historicOrders.length - 1)" style="color: #000000;"
                                            :class="[calculateHistoricProfit(historicOrders[index + 1].Price, historicOrders[index].Price) > 0 ? 'table-success' : 'table-danger' ]" >
                                                {{ calculateHistoricProfit(historicOrders[index + 1].Price, historicOrders[index].Price) }}
                                            </td>
                                            <td v-else>-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>                               
                        </b-tab>
                    </b-tabs>
                </b-card>
            </div>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue';

    import IEcharts from 'vue-echarts-v3/src/full.js';

    import 'zrender/lib/vml/vml';
    require('swiper/dist/css/swiper.css')
    import moment from 'moment';

    import VueAwesomeSwiper from 'vue-awesome-swiper';
    import countTo from 'vue-count-to';

    import datatable from "components/plugins/DataTable/DataTable.vue";
    import vScroll from "components/plugins/scroll/vScroll.vue";
    import portfolio from "components/widgets/portfolio/portfolio.vue"
    import VueChartist from 'v-chartist'

    Vue.use(VueAwesomeSwiper);
    var unsub;
    export default {
        name: "index_new",
        components: {
 
        },
        data() {
            return {
                account: 0, earningPages: 0, totalEarnings: 0, productionToday: 0,

                usdBtcData: '', balances: 0, allMarket: '', loading: true, color: "#2c3e50", size: "10px", spinner: true, allMarketSummarie: '',
                totalSaldoDollar: 0, variationBtc: 0, totalDepositsBtc: 0, totalWithdrawalsBtc: 0, totalBtc: 0, btcDiff: 0, dollarDiff: 0, btcMarket: [],
                usdMarket: [], ethMarket: [], usdtMarket: [], inactiveMarkets: [], timeRefresh: '', priceIn: 0, quantityIn: 0, historicOrders: '', profit: 0,
                monitor: 'Off', percentageDiffProfit: 0
            }
        },
        mounted: function () {
            clearInterval(this.interval)
            this.getAccount()
            this.getNumEarningPages()

            this.getOrderHistory()
            this.getDepositHistory()
            this.getWithdrawalHistory()
            this.getMarketSummary();
            this.refreshData();
            
            this.profit = this.$store.state.profit
            this.$store.state.calling ? this.monitor = "Working!" : this.monitor = "Off"


            unsub = this.$store.subscribe((mutation, state) => {
                if (mutation.type == "left_menu") {
                    this.instances.forEach(function (item, index) {
                        setTimeout(function () {
                            item.resize();
                        });
                    });
                    setTimeout(() => {
                        this.$refs.swiper.swiper.update();
                    });
                }
            });

        },
        destroyed () {
            clearInterval(this.interval)
            this.$store.commit("setCalling", false)
        },
        beforeDestroy() {
            clearInterval(this.timeRefresh);
            this.$store.commit("setCalling", false)
        },
        beforeRouteLeave(to, from, next) {
            unsub();
            next();
        },
        methods: {
            getAccount() {

                axios
                .get('/auth/api/getdata/account/BTC/0')
                .then( response => response.data) 
                .then(data => {
                    this.account = data.getData
                }).catch(error => {
                    console.log('Error de Get Account')
                }); 

            },
            getNumEarningPages() { 

                axios
                .get('/auth/api/getdata/paymentHistoryV2/BTC/1')
                .then( response => response.data) 
                .then(data => {
                    let earningPages = data.getData.totalPage
                    this.getEarningHistory(earningPages)

                }).catch(error => {
                    console.log('Error de getNumEarningPages')

                }); 
            },
            getEarningHistory(earningNumPages) { 
                
                let earnings = this.$store.state.ajuste ? parseFloat(this.$store.state.ajuste): 0; //ajuste
                 

                for(var page = 1; page <= earningNumPages; page++){
                    var count = 0;
                    axios
                    .get(`/auth/api/getdata/paymentHistoryV2/BTC/${page}`)
                    .then( response => response.data) 
                    .then(data => {

                        let allEarnigs = data.getData.rows
                        let pageEarnings = 0

                        allEarnigs.forEach(function(valor) { 
                            pageEarnings += (parseFloat(valor.ppsAmount) + parseFloat(valor.pplnsAmount))
                        });

                        count++;
                        earnings += pageEarnings;
                        this.totalEarnings = earnings;


                        if(count == earningNumPages){
                            this.productionToday = this.account.balance - earnings;
                            console.log("earnings = ",earnings)
                        }


                    }).catch(error => {
                        console.log('Error de Get paymentHistoryV2')

                    }); 
                }
            },
            getMarketSummary(){ //Traer todo los datos del mercado
                console.log('interval Dashboard...')
                axios
                .get('/auth/api/getmarketsummaries')
                .then( response => response.data) 
                .then(data => {
                    this.allMarketSummarie = data.getmarketSummaries.result
                    this.usdBtcData = data.getmarketSummaries.result.filter(summarie => summarie.MarketName == 'USD-BTC')[0]
                    this.spinner = false
                    
                    this.variationBtc = ((this.usdBtcData.Last - this.usdBtcData.PrevDay)/this.usdBtcData.PrevDay) * 100

                    this.btcMarket = data.getmarketSummaries.result.filter(summarie => summarie.MarketName[0] == 'B' && summarie.PrevDay != 0)

                    //Ordena el mercado btc por variación
                    this.btcMarket.sort((a, b) => (
                        (((a.Last) - (a.PrevDay))/(a.PrevDay) < ((b.Last) - (b.PrevDay))/(b.PrevDay))
                    ) ? 1 : -1)

                    this.usdMarket = data.getmarketSummaries.result.filter(summarie => summarie.MarketName[0] == 'U' && summarie.MarketName[3] == '-' && summarie.PrevDay != 0 )

                    //Ordena el mercado usd por variación
                    this.usdMarket.sort((a, b) => (
                        (((a.Last) - (a.PrevDay))/(a.PrevDay) < ((b.Last) - (b.PrevDay))/(b.PrevDay))
                    ) ? 1 : -1)

                    this.ethMarket = data.getmarketSummaries.result.filter(summarie => summarie.MarketName[0] == 'E' && summarie.PrevDay != 0 )

                    //Ordena el mercado usd por variación
                    this.ethMarket.sort((a, b) => (
                        (((a.Last) - (a.PrevDay))/(a.PrevDay) < ((b.Last) - (b.PrevDay))/(b.PrevDay))
                    ) ? 1 : -1)

                    this.usdtMarket = data.getmarketSummaries.result.filter(summarie => summarie.MarketName[0] == 'U' && summarie.MarketName[3] == 'T' && summarie.PrevDay != 0 )

                    //Ordena el mercado usd por variación
                    this.usdtMarket.sort((a, b) => (
                        (((a.Last) - (a.PrevDay))/(a.PrevDay) < ((b.Last) - (b.PrevDay))/(b.PrevDay))
                    ) ? 1 : -1)

                    let arrayCoinsByVariation = []

                    this.btcMarket.forEach(function(v, index, object) { //popular arreglo con monedas y su variacion
                        arrayCoinsByVariation.push(v.MarketName +' : '+ (((v.Last - v.PrevDay)/v.PrevDay) * 100).toFixed(2) + '%')
                    });

                    this.$store.commit("setCoinsByVariation", arrayCoinsByVariation)

                    this.$store.commit("setCalling", true)


                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de Getmarketsummaries 2')

                });
                this.getMarkets()
                //this.getOpenOrders()
                //this.getOrderHistory()
                this.getBalance()
                //this.getStatusProfit()
                //this.calculateOrderBuy()
                //this.calculateOrderSell()
                
            },
            getMarkets(){ //Trae los datos de las monedas

                let inactiveMarketsArray = []

                axios
                .get('/auth/api/getmarkets')
                .then( response => response.data) 
                .then(data => {
                    this.allMarket = data.getMarkets.result

                    let arrayCoinsExample = []

                    let inactiveMarkets = this.allMarket.filter(item => item.IsActive == false)
                     inactiveMarkets.forEach(function(v) { //guardar par de mercado inactivos en un arreglo
                         inactiveMarketsArray.push(v.MarketName)
                     });

                    this.btcMarket.forEach(function(v, index, object) { //eliminar elementos inactivos del arreglo btc
                        if(inactiveMarketsArray.includes(v.MarketName))
                           object.splice(index, 1);
                        
                        //arrayCoinsExample.push(v.MarketName +' : '+ (((v.Last - v.PrevDay)/v.PrevDay) * 100).toFixed(2))
                    });

                    //console.log(arrayCoinsExample)

                    this.usdMarket.forEach(function(v, index, object) { //eliminar elementos inactivos del arreglo usd
                        if(inactiveMarketsArray.includes(v.MarketName))
                           object.splice(index, 1);
                    });

                    this.ethMarket.forEach(function(v, index, object) { //eliminar elementos inactivos del arreglo eth
                        if(inactiveMarketsArray.includes(v.MarketName))
                           object.splice(index, 1);
                    });

                    this.usdtMarket.forEach(function(v, index, object) { //eliminar elementos inactivos del arreglo usdt
                        if(inactiveMarketsArray.includes(v.MarketName))
                           object.splice(index, 1);
                    });
                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de Getmarkets 3')

                });
            },
            getDataCoin(nameCoin){ //Retorna la Imagen de la moneda
                let infoCoin = null
                
                if(this.allMarket)
                    infoCoin = this.allMarket.filter(item => item.MarketCurrency == nameCoin)[0]

                if(infoCoin)
                    return infoCoin.LogoUrl
            },
            getBalance(){ //Trae el balance de todas las monedas
                axios
                .get('/auth/api/getbalances')
                .then( response => response.data) 
                .then(data => {
                    this.balances = data.getBalances.result
                    this.getDollarSaldo()
                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de Getbalances 4')

                }); 
            },
            toFix(value, decimals){ //Colocar Decimales
                if(value && value !=0)
                    return value.toFixed(decimals)
                else
                    return 0
            },
            getDollarSaldo(){ //Calcular el saldo en $
                
                let arrayCoins = []
                this.balances.forEach(function(v) { //recorrer el balance para tener las monedas con disponible para tener nombre y cantidad
                    arrayCoins.push({
                        currency: v.Currency,
                        quantity: v.Available
                    })
                });

                let arrayMarkets = this.allMarketSummarie
                let arrayAcumulativo = []
                arrayCoins.forEach(function(v) { //recorrer las monedas con balance para tener precio y convertir a btc
                    if(v.currency != 'BTXCRD') 
                        if(v.currency != 'BTC'){
                            let data = arrayMarkets.filter(summarie => summarie.MarketName == `BTC-${v.currency}`)
                            arrayAcumulativo.push(v.quantity * data[0].Last)
                        }else{
                            arrayAcumulativo.push(v.quantity)
                        }
                });

                let totalBtc = arrayAcumulativo.reduce((a, b) => a + b, 0); //sumar todo el arreglo de btc's
                this.totalBtc = totalBtc
                this.totalSaldoDollar = totalBtc * this.usdBtcData.Last
                
                this.$store.commit("setSaldoUsd", (this.totalSaldoDollar).toFixed(2))
                this.$store.commit("setSaldoBtc", this.totalBtc)

                this.getProfitData()

            },
            getProfitData(){ //Calcular diferencia de btc's saldo - deposito

                this.btcDiff = (this.totalBtc - this.totalDepositsBtc - this.totalWithdrawalsBtc)
                this.dollarDiff = this.btcDiff * this.usdBtcData.Last

                this.percentageDiffProfit = (this.btcDiff * 100)/(this.totalBtc)

                this.$store.commit("setGlobalProfit", { percentageGlobalProfit: (this.percentageDiffProfit).toFixed(2), dollarGlobalProfit: this.dollarDiff.toFixed(2) })

                
            },
            getDepositHistory() { //Traer historico de depositos en btc

                let totalDeposits = 0;

                axios
                .get('/auth/api/getdeposithistory/BTC')
                .then( response => response.data) 
                .then(data => {
                    let allDeposits = data.getDepositHistory.result

                    allDeposits.forEach(function(v) { //recorrer el balance para tener las monedas con disponible para tener nombre y cantidad
                        totalDeposits += v.Amount
                    });
                    this.totalDepositsBtc = totalDeposits

                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de Getdeposithistory 5')

                }); 
            },
            getWithdrawalHistory() { //Traer historico de retiros en btc

                let totalWithdrawals = 0;

                axios
                .get('/auth/api/getwithdrawalhistory/BTC')
                .then( response => response.data) 
                .then(data => {
                    let allWithdrawals = data.getWithdrawalHistory.result

                    allWithdrawals.forEach(function(v) { //recorrer el balance para tener las monedas con disponible para tener nombre y cantidad
                        totalWithdrawals += v.Amount
                    });
                    this.totalWithdrawalsBtc = totalWithdrawals
                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de Getdeposithistory 6')

                });    
                
            },
            getOrderHistory(){ //Traer historico de Ordenes
                axios
                .get('/auth/api/getorderhistoryall')
                .then(response => response.data)
                .then( data => { 
                    this.historicOrders = Object.values(data.getOrderHistoryAll.result)

                    if(data.getOrderHistoryAll.result.length > 0)
                        if(data.getOrderHistoryAll.result[0].OrderType == 'LIMIT_BUY'){
                            this.priceIn = data.getOrderHistoryAll.result[0].Limit
                            this.quantityIn = data.getOrderHistoryAll.result[0].Quantity
                        }else{
                            this.priceIn = 0
                            this.quantityIn = 0
                        }
                        return data 
                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de getOrderHistory 7')

                }); 
            },
            calculateHistoricProfit(compra, venta){
                let profit = ((venta -  compra)/ compra)*100
                //this.profitHistoric = profit
                    return profit.toFixed(2)
            },
            timeClose(time){

                let times = moment(new Date(time));
                let now = moment(new Date());
                    return times.from(now);
            },
            refreshData() {
                this.timeRefresh = setInterval(() => {    
                    this.getMarketSummary();
                }, 5000);    
            }
        }
    }
</script>
<!-- styles -->
<!-- adding scoped attribute will apply the css to this component only -->
<style src="assets/css/widgets.css" scoped></style>
<style scoped>
    .swiper-pagination {
        position: relative;
    }

    .swiper-pagination-fraction,
    .swiper-pagination-custom,
    .swiper-container-horizontal>.swiper-pagination-bullets {
        top: 5px;
    }

    .swiper-container {
        margin-top: 0px !important;
    }
</style>
<style type="text/css" lang="scss">
    .text_green{
        color: #5cb85c;
        font-size: 20px;
    }
    .text_red{
        color: #d9534f;
        font-size: 20px;
    }

    .text_green_medium{
        color: #5cb85c;
        font-size: 14px;
    }
    .text_red_medium{
        color: #d9534f;
        font-size:14px;
    }
    
    .text_green_small{
        color: #5cb85c;
        font-size: 13px;
    }
    .text_red_small{
        color: #d9534f;
        font-size: 13px;
    }
    .index2_table .table-responsive .card {
        border: none;
        box-shadow: none;
        margin-bottom: 0;
    }

    .index2_swiper .swiper-pagination-bullet-active {
        background: #08aa80;
    }
    /*===============================notes========*/

    .notes {
        line-height: 22px;
        font-size: 13px;
        padding: 0 15px 0 36px;
        position: relative;
        outline: none;
        background: #fff;
        background-size: 100% 30px;
    }

    .notes p {
        border-bottom: 1px solid #dfe8ec;
        ;
    }

    .notes::after {
        content: '';
        position: absolute;
        width: 0;
        top: 0;
        left: 25px;
        bottom: 0;
        border-left: 1px solid #0fd1c1;
    }

    .social .bg-default-card .info {
        font-size: 12px;
    }

    .social .bg-default-card span {
        display: block;
        text-transform: uppercase;
    }

    .social .bg-default-card .value {
        font-size: 40px;
        line-height: normal;
    }


    .social .bg-default-card {
        i {
            transition: 300ms;
        }
        &:hover {
            i {
                font-size: 45px;
                transition: 300ms;
            }
        }
    }

    .social_cuntdata {
        font-weight: bold;
        font-size: 18px;
    }

    .subscribe_btn {
        background-color: transparent;
        border: 0;
        outline: none;
    }

    .widget_social_icons {
        background-color: #fff;
    }

    .fb_text {
        color: #215fe2;
        font-size: 28px;
    }

    .box_shadow {
        box-shadow: 2px 2px 15px 0px #ccc;
    }

    .head_color {
        color: #686868;
    }

    .text_size {
        font-size: 20px;
        color: #42494d;
    }

    .text-ash {
        color: #575f65;
    }

    .text_color {
        color: #646161 !important;
    }

    .swiper-pagination {
        margin: 0 !important;
    }

    .text-blue {
        color: #215fe2;
    }

    .progress_color2 .progress-bar {
        background-color: #7FAFF7;
    }

    .icon_color {
        font-size: 27px;
        color: #828686;
    }

    .icon_color1 {
        font-size: 25px;
        color: #6e8dce;
    }

    .line {
        border-top: 1px solid #ccc;
    }

    .text_head_Color {
        color: #707373;
    }

    .below_text {
        color: #a2a1a1;
    }

    .index2_table th {
        color: #686868;
    }

    .index2_table td {
        color: #686868;
    }

    .user_color {
        color: #8e8c8e;
    }

    .count1 {
        font-size: 18px;
        color: #686868;
    }

    .ti_color {
        color: #215fe2;
    }

    .fb_color {
        font-size: 18px;
        color: #3B5998;
    }

    .twi_color {
        color: #00aced;
        font-size: 18px;
    }

    .gi_color {
        color: red;
        font-size: 18px;
    }

    .pin_color {
        color: red;
    }

    .cam_color {
        color: green
    }

    .events1 {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding-top: 2px;
        padding-bottom: 2px;
        margin-top: 15px;
        margin-right: -20px;
        font-size: 18px;
        color: #555;
    }

    .online_dot img {
        position: relative;
    }

    .online_dot::after {
        content: "";
        position: absolute;
        margin-top: -28px;
        height: 11px;
        width: 11px;
        border: 2px solid #FFF;
        border-radius: 50%;
        background-color: green;
        margin-left: -13px;
    }

    .conversation-list .ctext-wrap p {
        margin: 0;
        padding-top: 3px;
    }

    .conversation-list .odd .ctext-wrap:after {
        border-color: rgba(238, 238, 242, 0);
        left: 99%;
        margin-right: -1px;
        border-top: 0 solid #D8F1E4;
        border-left: 12px solid #D8F1E4;
        border-bottom: 14px solid transparent;
    }

    .conversation-list .ctext-wrap p {
        margin: 0;
        /*padding-top: 3px;*/
    }

    .ctext-wrap p {
        margin-bottom: 10px;
    }

    .conversation-list .odd .conversation-text {
        float: right;
        margin-right: 25px;
        text-align: right;
        width: 95%;
    }

    .conversation-list .ctext-wrap i {
        color: #777;
        display: block;
        font-size: 11px;
        font-style: normal;
        position: relative;
    }

    .conversation-list .conversers1 .ctext-wrap {
        background: #D8F1E4;
    }

    .conversation-list .ctext-wrap {
        border-radius: 3px;
        display: inline-block;
        padding: 5px 10px;
        position: relative;
        box-shadow: 2px -2px 4px 0px rgba(0, 0, 0, 0.1)
    }

    .text-field {
        padding: 6px 10px;
    }

    .conversation-list .odd .ctext-wrap:after {
        border-color: rgba(238, 238, 242, 0);
        left: 99%;
        margin-right: -1px;
        border-top: 0 solid #D8F1E4;
        border-left: 12px solid #D8F1E4;
        border-bottom: 14px solid transparent;
    }

    .conversation-list .ctext-wrap:after {
        right: 100%;
        top: 0;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        margin-left: -1px;
        border-top: 0 solid #fff;
        border-right: 12px solid #e9f9ff;
        border-bottom: 14px solid transparent;
    }

    .clearfix:after {
        clear: both;
    }

    .back_color1 {
        width: auto;
        height: 274px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .conversation-list .conversation-text {
        float: left;
        font-size: 13px;
        width: 70%;
    }

    .clearfix:before,
    .clearfix:after {
        content: " ";
        display: table;
    }

    .conversation-list .conversers2 .ctext-wrap {
        background: #e9f9ff;
    }

    .m-t-10 {
        margin-top: 10px !important;
    }

    .conversation-list {
        width: auto;
        height: 340px;
        padding-left: 27px;
    }

    .profile-img {
        background-color: #fff;
    }
    .chat-conversation{
        width: 100%;
    }
</style>
<style src="chartist/dist/chartist.css"></style>
