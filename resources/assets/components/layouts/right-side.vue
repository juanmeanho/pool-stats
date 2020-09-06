<template>
    <aside class="right-aside">
        <!-- Content Header (Page header) -->
        <section class="content-header ">
            <div class="container">
                <div class="row">
                    <div class="col-xs-2 mt-3">
                        <button class="ml-4 btn btn-primary">
                            1 <i class="fa fa-btc text-white"></i> = ${{ toFix(this.$store.state.valueBtc, 2) }}
                        </button>                    
                    </div>
                </div>

            </div>
        </section>
        <!-- Main content -->
        <section class="content">
            <slot></slot>
        </section>
        <!-- /.content -->
    </aside>
</template>
<script>
export default {
    name: "right-side",
    data(){
        return {
        }
    },
    mounted: function() {
    },
    methods: {
            toFix(value, decimals){ //Colocar Decimales
                if(value && value !=0)
                    return value.toFixed(decimals)
                else
                    return 0
            },
    }
}
</script>
<style lang="scss" scoped>
.right-aside {
    padding: 0 20px 10px 20px;
    width: 100%;
    max-width: 100%;
    min-height: 100vh;
    .content-header > h1 {
        margin: 2px;
        padding-left: 13px;
        padding-top: 12px;
        font-size: 20px;
        line-height: 1.5;
    }
    .content-header {
        margin: -2px -20px 25px -20px;
        height: 55px;
        background: #f9fafb ;
        box-shadow: 3px 1px 5px #ccc;
    }
}
</style>
