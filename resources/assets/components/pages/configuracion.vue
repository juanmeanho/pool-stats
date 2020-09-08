<template>
    <div>
        <div class="row">
            <div class="col-lg-12 col-xl-12">
                <!--======================== Formulario APi ===========================-->
                <b-card header="Api Settings" header-tag="h4" class="bg-success-card">
                    <div class="col-4">
                        <div class="row mt-3 mb-3">
                            <div class="col-2 mt-4 mb-3 text-center">
                                <i class="fa fa-key fb_text mt-2" style="color:green;"></i>
                            </div>
                            <div class="col-10 mt-4">
                                <input type="text" class="form-control" v-model="user" name="user" ref="user">
                                <p class="below_text">User</p>
                                
                                <input type="numeric" class="form-control" v-model="password_1" name="password_1" ref="password_1">
                                <p class="below_text">API Key</p>
                                
                                <input type="numeric" class="form-control" v-model="password_2" name="password_2" ref="password_2">
                                <p class="below_text">API Secret</p>

                                <div class="row mt-3 mb-3">
                                    <div class="col-6 mt-4 mb-3 text-center">
                                        <button @click="setPasswords()" class="text-white btn btn-primary" >
                                            Guardar
                                        </button>                                        
                                    </div>
                                    <div class="col-6 mt-4 mb-3 text-center">
                                        <button @click="clearPasswords()" :class="['text-white', (estatusApi == 'ON') ? 'btn btn-success' : 'btn btn-danger' ]">
                                            {{ estatusApi }}
                                        </button>  
                                        <p class="below_text" style="font-size:10px;">Estatus</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </b-card>
            </div>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue';

    import 'zrender/lib/vml/vml';
    require('swiper/dist/css/swiper.css')
    import moment from 'moment';

    import VueAwesomeSwiper from 'vue-awesome-swiper';
    import countTo from 'vue-count-to';
    import datatable from "components/plugins/DataTable/DataTable.vue";
    import vScroll from "components/plugins/scroll/vScroll.vue";
    import portfolio from "components/widgets/portfolio/portfolio.vue"
    import VueChartist from 'v-chartist'
    import miniToastr from 'mini-toastr';
    miniToastr.init();

    Vue.use(VueAwesomeSwiper);
    var unsub;
    export default {
        name: "index2",
        components: {

        },
        data() {
            return {
                password_1: null, password_2: null, user: null,spinner: false, monitor: 'Off', estatusApi: 'OFF'

            }
        },
        mounted: function () {

            unsub = this.$store.subscribe((mutation, state) => {
                if (mutation.type == "left_menu") {
                    this.instances.forEach(function (item, index) {
                        setTimeout(function () {
                            item.resize();
                        });
                    });
                }
            });

        },watch: {
            '$route' (to, from) {
                location.reload();
            }
        },
        methods: {
            
            setPasswords(){

                let user       = this.user ? this.user : null
                let password_2 = this.password_2 ? this.password_2 : null
                let password_1 = this.password_1 ? this.password_1 : null 

                axios
                .get(`/auth/setpasswords/${this.user}/${this.password_1}/${this.password_2}`)
                .then(response => response.data)
                .then( data => { 

                    miniToastr.info(data.msg, "Success",)
                    this.password_1 = null
                    this.password_2 = null
                    this.user       = null
                    
                }).catch(error => {
                    console.log('Error de setPasswords 40')

                });
            },
            clearPasswords(){
                this.password_1 = null
                this.password_2 = null
                this.user = null

            },
            checkPasswords(){
                    
                axios
                .get('/auth/checkpasswords')
                .then(response => response.data)
                .then( data => { 
                    this.estatusApi = data.msg
                }).catch(error => {
                    console.log('Error de checkPasswords 41')

                });
            }
        },
        computed: {
        },
        beforeRouteLeave(to, from, next) {
            unsub();
            next();
        },
        beforeRouteUpdate(to, from, next) {
            unsub();
            next();
        },
        created() {

        },
        destroyed () {

        },
        beforeDestroy() {

        },
    }
</script>
<!-- styles -->
<!-- adding scoped attribute will apply the css to this component only -->
<style src="assets/css/widgets.css" scoped></style>
<style scoped>

    .ganancia_positiva{
        background-color: #0aac0a;
    }
    .ganancia_negativa{
        background-color: red;
    }

    .text_green{
        color: #5cb85c;
    }
    .text_red{
        color: #d9534f;
    }
    .color_icon_positive{
        color: #5cb85c;

    }
    .color_icon_negative{
        color: #d9534f;

    }
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
    .mini-toastr {
        right: inherit !important;
        top: 12px !important;
        left: 40% !important;
        bottom: inherit !important;
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
        font-size: 25px;
        color: #797f82;
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
        color: #2b2c2c;
    }

    .below_text {
        color: #666363;
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
