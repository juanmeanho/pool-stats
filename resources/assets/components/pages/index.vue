<template>
    <div>
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="text-center p-3 widget_social_icons box_shadow card" style="margin-bottom:0px">
                    <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                        <div v-else>
                            <p>Today</p>
                            <div class="text-ash">
                                <pulse-loader v-if="!productionToday" :loading="true" :color="color" :size="size"></pulse-loader>
                                <h4 v-else class="mb-0 mt-2 text_size" style="color: #42494d;"><i class="fa fa-btc" style="color: blue; font-size: inherit;" ></i> {{  toFix(productionToday, 8) }}</h4>
                                <h5 class="mb-0 mt-2 text_size" style="color: #42494d;"><i class="fa fa-usd" style="color: green; font-size: inherit;"></i> {{ toFix((productionToday * btcPrice), 2) }}</h5>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col-lg-3  col-sm-6 mb-3">
                <div class="text-center p-3 widget_social_icons box_shadow card" style="margin-bottom:0px">
                    <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                        <div v-else>
                            <p>Yesterday</p>
                            <div class="text-ash">
                                <pulse-loader v-if="!account" :loading="true" :color="color" :size="size"></pulse-loader>
                                <h4 v-else class="mb-0 mt-2 text_size" style="color: #42494d;"><i class="fa fa-btc" style="color: blue; font-size: inherit;"></i> {{ account.earn24Hours }}</h4>
                                <h5 class="mb-0 mt-2 text_size" style="color: #42494d;"><i class="fa fa-usd" style="color: green; font-size: inherit;"></i> {{ toFix((account.earn24Hours * btcPrice), 2) }}</h5>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col-lg-3  col-sm-6 mb-3">
                <div class="text-center p-3 widget_social_icons box_shadow card" style="margin-bottom:0px">
                    <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                        <div v-else>
                            <p>Balance</p>
                            <div class="text-ash">
                                <pulse-loader v-if="!account" :loading="true" :color="color" :size="size"></pulse-loader>
                                <h4 v-else class="mb-0 mt-2 text_size" style="color: #42494d;"><i class="fa fa-btc" style="color: blue; font-size: inherit;"></i> {{ account.balance }}</h4>
                                <h5 class="mb-0 mt-2 text_size" style="color: #42494d;"><i class="fa fa-usd" style="color: green; font-size: inherit;"></i> {{ toFix((account.balance * btcPrice), 2) }}</h5>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col-lg-3  col-md-6 mb-3">
                <div class="text-center p-3 widget_social_icons box_shadow card" style="margin-bottom:0px">
                    <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                        <div v-else>
                            <p>Total Earnings</p>
                            <div class="text-ash">
                                <pulse-loader v-if="!account" :loading="true" :color="color" :size="size"></pulse-loader>
                                <h4 v-else class="mb-0 mt-2 text_size" style="color: #42494d;"><i class="fa fa-btc" style="color: blue; font-size: inherit;"></i> {{ account.earnTotal }}</h4>
                                <h5 class="mb-0 mt-2 text_size" style="color: #42494d;"><i class="fa fa-usd" style="color: green; font-size: inherit;"></i> {{ toFix((earnTotalUsd), 2) }}</h5>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6 col-lg-12">
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <b-card header='Pool Stats' header-tag="h4" class="bg-success-card">
                            <div class="card-block" style="margin-bottom: 15px;">
                                <div class="row text-center">
                                    <div class="col-12">
                                        <h4 class="text_color text_size_big">{{ toFix(hashLast10m, 2) }} TH/s</h4>
                                        <p class="pb-3 border_bottom">Hash rate last 10 minutes</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row m-0 text-center" style="margin-top: 15px;">
                                <div class="col-4  pb-0 post-fllow">
                                    <span class="mt-3"><strong>{{ toFix(poolHash, 2) }} PH/s</strong></span>
                                    <p class="mt-2">Hash Pool</p>
                                </div>
                                <div class="col-4 pb-0 post-fllow">
                                    <span><strong>  {{ currentRound }}</strong></span>
                                    <p class="mt-2">Current Round</p>
                                </div>
                                <div class="col-4 pb-0 post-fllow">
                                    <span><strong>{{ toFix((networkDiff / 1000000000000 ), 2) }} T </strong></span>
                                    <p class="mt-2">Network Difficulty</p>
                                </div>
                            </div>
                        </b-card>
                    </div>
                    <div class="col-xl-12 col-lg-12">
                        <b-card header-tag="h4" class="bg-default-card">
                            <div style="height: 320px;padding-bottom: 0px;padding-top: 0px;">
                                <IEcharts :option="area_chart" :loading="loadingChart" @ready="onReady" ref="areachart"></IEcharts>
                            </div>
                        </b-card>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="col-md-12 tot_boxline3">
                    <h4 style="margin-bottom: 15px;" class="name_visitors">Last Blocks</h4>
                </div>
                <b-card class="line_remove">
                    <table width="100%"  class="table table-hover hover_table">
                        <thead>
                            <tr>
                                <th width="50%" class="text-gray">Height</th>
                                <th width="50%" class="text-gray">Seen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!lastBlocks" style="align:center;height:400px;">
                                <td colspan="2" style="align:center;height:400px;">
                                    <rotate-loader style="text-align:center" :loading="loading" :color="color" :size="size"></rotate-loader>
                                </td>
                            </tr>
                            <tr v-else v-for="(item, index) in lastBlocks" :key="index">
                                <td><span :class="setNewBlock(item.time)">{{ item.height }}</span></td>
                                <td>{{ convertDate(item.time) }}</td>
                            </tr>
                        </tbody>
                    </table>
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
            IEcharts
        },
        data() {
            return {
                flagRefresh: 0, account: 0, earningPages: 0, totalEarnings: 0, productionToday: 0, spinner: false,
                loading: true, color: "#2c3e50", size: "10px", timeRefresh: '', workerData: null, hashLast10m: 0, networkDiff: 0, earnTotalUsd: 0, instances: [], loadingChart: true, currentRound: 0,
                monitor: 'Off', usdBtcData: 0, btcPrice: 0, poolStats: null, lastBlocks: null, poolHash: 0, workers: 0,
                area_chart: {
                    title: { text: 'Last 7 Days', subtext: 'USD/BTC' },
                    tooltip: { trigger: 'axis' },
                    legend: { show: true, data: ['Últimos 7 días', 'Últimos 7 días'] },
                    color: { pattern: ['red', 'red'] },
                    calculable: true,
                    xAxis: [{ type: 'category', boundaryGap: false, data: [] }],
                    yAxis: [{ type: 'value' }],
                    series: [{
                        name: 'USD',type: 'line',smooth: true,
                        itemStyle: {
                            normal: {
                                areaStyle: { type: 'default', color: '#16af81' },
                                lineStyle: { color: '#009966' }
                            }
                        },
                        data: []
                    }, {
                        name: 'BTC', type: 'line', smooth: true,
                        itemStyle: {
                            normal: {
                                areaStyle: { type: 'default', color: '#16af81' },
                                lineStyle: { color: '#009966' }
                            }
                        },
                        data: []
                    }]
                },
            }
        },
        mounted: function () {
            this.getValueBtc()
            this.getAccount()
            this.refreshData();
            

            unsub = this.$store.subscribe((mutation, state) => {
                if (mutation.type == "left_menu") {
                    this.instances.forEach(function (item, index) {
                        setTimeout(function () {
                            item.resize();
                        });
                    });
                }
            });

        },
        destroyed () {
        },
        beforeDestroy() {
        },
        beforeRouteLeave(to, from, next) {
            unsub();
            next();
        },
        methods: {
            getAccount() {

                this.getNumEarningPages()
                //this.saveBlocks(); 
                this.getLastBlocksDb();
                this.getWorkerData();
                this.getPoolStats();        

                axios
                .get('/api/getdata/account/BTC/0')
                .then( response => response.data) 
                .then(data => {
                    this.account = data.data
                    this.earnTotalUsd = this.account.earnTotal * this.btcPrice
                    this.$store.commit("setCalling", true)
                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de Get Account')
                }); 

            },
            getNumEarningPages() { 

                axios
                .get('/api/getdata/paymentHistoryV2/BTC/1')
                .then( response => response.data) 
                .then(data => {
                    let earningPages = data.data.totalPage
                    this.getEarningHistory(earningPages)

                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de getNumEarningPages')

                }); 
            },
            getEarningHistory(earningNumPages) { 
                
                let earnings = this.$store.state.ajuste ? parseFloat(this.$store.state.ajuste): 0; //ajuste
                let btcPrice = this.btcPrice

                for(var page = 1; page <= earningNumPages; page++){
                    var count = 0;
                    axios
                    .get(`/api/getdata/paymentHistoryV2/BTC/${page}`)
                    .then( response => response.data) 
                    .then(data => {

                        let allEarnigs = data.data.rows
                        let pageEarnings = 0
                        let dateX = []
                        let dateY = []
                        let dateYBack = []
                        let dateYUsd = []
                        let earning = 0;

                        allEarnigs.forEach(function(valor, index) { 

                            earning = parseFloat(valor.ppsAmount) + parseFloat(valor.pplnsAmount) + parseFloat(valor.ppappsAmount) + parseFloat(valor.ppapplnsAmount);
                            pageEarnings += earning;
                            
                            if(index < 7 && count == 0){
                                let fecha = moment(valor.timestamp, 'YYYY-MM-DD').subtract(1, 'days')
                                dateX.push(fecha.format('MMM D')); 
                                dateY.push(earning * btcPrice); 
                                dateYBack.push(earning); 
                            }
                        });

                        dateY.forEach(item => { dateYUsd.push(this.toFix(item, 2))})
                        count++;
                        earnings += pageEarnings;
                        this.totalEarnings = earnings;
                        this.area_chart.xAxis[0].data = dateX.reverse()
                        this.area_chart.series[0].data = dateYUsd.reverse()
                        this.area_chart.series[1].data = dateYBack.reverse()
                        
                        if(count == earningNumPages){
                            this.productionToday = this.account.balance - earnings;
                            this.loadingChart = false
                            console.log("Earnings = ", this.toFix(earnings, 8))
                        }


                    }).catch(error => {
                        this.$store.commit("setCalling", false)

                        console.log('Error de Get getEarningHistory')

                    }); 
                }
            },
            getPoolStats() {

                axios
                .get('/api/getdata/poolStats/BTC/0')
                .then( response => response.data) 
                .then(data => {

                    
                    this.poolStats = data.data
                    this.poolHash = this.poolStats.poolHashrate * parseFloat(0.0000010) * parseFloat(0.0000010);
                    this.networkDiff = this.poolStats.networkDiff                    
                    let dateLastBlock = moment(this.lastBlocks[0].time).subtract(3, 'hours');
                    let now = moment(new Date()); //todays date
                    var current = moment.duration(now.diff(dateLastBlock))
                    this.currentRound = current.hours() +" h "+ current.minutes() +" m";

                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de PoolStats')
                }); 
            },
            getLastBlocksDb() {

                axios
                .get('/auth/db-blocks')
                .then( response => response.data) 
                .then(data => {
                    this.lastBlocks = data.blocks
                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de getLastBlocksDb')
                }); 

            },
            getWorkerData() {

                axios
                .get('/api/getdata/workers/BTC/1')
                .then( response => response.data) 
                .then(data => {
                    this.workerData = data.data.rows

                    var hashWorkers = this.workerData.reduce((accumulator, item) => {
                         return parseFloat(accumulator) + parseFloat(item.last10m);
                    }, 0);

                    this.hashLast10m = hashWorkers * parseFloat(0.0000010); //MB to TB

                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de getWorkerData')
                }); 

            },
            toFix(value, decimals){ //Colocar Decimales
                if(value && value !=0)
                    return value.toFixed(decimals)
                else
                    return 0
            },
            refreshData() {
                this.timeRefresh = setInterval(() => {    
                    this.getAccount();
                }, 180000);    
            },
            convertDate(date){
                let adjustDate = moment(date).subtract(3, 'hours');
                return  moment(adjustDate).fromNow();
            },
            setNewBlock(date){
                var now = moment(new Date());
                let adjustDate = moment(date).subtract(3, 'hours');
            
                if(now.diff(adjustDate, 'minutes') < 30){
                    return 'success_background'
                }
            },
            saveBlocks() {

                axios
                .get('/api/last-block')
                .then( response => response.data) 
                .then(data => {
                    console.log('Chequeando Blocks')
                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de Guardado en BD')
                }); 

            },
            getValueBtc() {
                axios
                    .get('https://blockchain.info/ticker')
                    .then( response => response.data) 
                    .then(data => {
                        this.btcPrice = data.USD.last
                        this.$store.commit("setValueBtc", data.USD.last)
                    }).catch(error => {
                        console.log('Error de Get Btc Price')
                    }); 
            },
            onReady(instance) {
                this.instances.push(instance)
            },
            update_chart() {
                setInterval(() => {
                    for (var i = 0; i < this.area_chart.series.length; i++) {
                        this.area_chart.series[i].data.shift();
                        this.area_chart.series[i].data.push(Math.floor((Math.random() * (1000 - 80) + 80) + 1));

                    }

                }, 2000);
            },
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

    .text_size_big {
        font-size: 30px;
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

    .social-widget .user-pic {
    border-radius: 50%;
    width: 106px;
}

.social-widget .social-icons {
    font-size: 20px;
    padding-top: 5px;
    padding-bottom: 5px;
}
.border_bottom{
    border-bottom:1px solid #ccc;
}
.post-fllow {
    background-color: #fff !important;
}
.social-widget .post-fllow {
    background-color: #e5e5e5;
    padding-bottom: 4px;
    padding-top: 18px;
    border-top: 1px solid #ccc;
}

.social-widget .post-fllow:not(:last-child) {
    border-right: 1px solid #CCC;
}
</style>
<style src="chartist/dist/chartist.css"></style>
<style scoped src="assets/css/e_dashboard.css"></style>
