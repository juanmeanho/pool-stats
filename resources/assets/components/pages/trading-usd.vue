<template>
    <div>
        <vue-headful :title="profit.toFixed(3) + '% - ' + monitor + ' Trading'" />
        <!--========================Formularios de Trading===========================-->
        <div class="row">
            <div class="col-lg-12 col-xl-8">
                <!--======================== Formulario Comprar ===========================-->
                <b-card :header="'Comprar ' + (dataCoin.MarketCurrencyLong || '') + ' - Disponible [' + (balanceUsd || 0) 
                                            + ' ' + (dataCoin.BaseCurrency || '') + '] - ($'+ toFix(balanceUsd * usdBtcData.Last, 2)+')'" 
                                            header-tag="h4" class="bg-success-card">
                    <form @submit.prevent="buyLimit">
                        <div class="card" style="width: 100%;">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label>Precio (Bid) </label> <span @click="setPriceBuy" class="badge badge-success"> {{ btcCoinData.Bid }}</span>
                                        <input type="numeric" @change="calculateOrderBuy(btcSell, priceBuy)" @keyup="calculateOrderBuy(btcSell, priceBuy)" 
                                        :disabled="disabledIn" class="form-control" name="rate" ref="rate" v-model="priceBuy"  placeholder="Precio" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label >Cantidad {{  dataCoin.MarketCurrency }} a comprar</label>
                                        <input type="numeric" class="form-control" name="quantityToBuy"  ref="quantityToBuy" readonly v-model="getTotalCoinToBuy" 
                                        placeholder="Cantidad" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label >Cantidad {{  dataCoin.BaseCurrency }}</label> <span @click="setBtcSell" class="badge badge-success">  {{ balanceUsd }}</span>
                                        <input type="numeric" @change="calculateOrderBuy(btcSell, priceBuy)" @keyup="calculateOrderBuy(btcSell, priceBuy)" 
                                        :disabled="disabledIn" class="form-control" name="quantityBtc" ref="quantityBtc" v-model="btcSell" placeholder="Cantidad" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <button :disabled="disabledIn" class="btn btn-success" style="width: 88px">
                                            <div class="row">
                                                <div v-if="disabledIn" class="col-sm-2">
                                                    <clip-loader class="mt-0"  :loading="true" color="white" size="10px"></clip-loader>
                                                </div>
                                                <div v-else class="col-sm-1">
                                                    <i class="fa fa-circle-o-notch " style="font-size:10px;color:white;"></i>
                                                </div>
                                                <div v-if="disabledIn" class="col-sm-0 ml-1">Comprar</div>
                                                <div v-else class="col-sm-0">Comprar</div>
                                            </div>
                                        </button>
                                    </div>
                                    <div class="col-sm-3"  >
                                        <button :disabled="cancelInButton" @click="cancelInManual()" class="text-white btn btn-primary">
                                                Cancelar Compra
                                        </button>
                                    </div>
                                    <div class="col-sm-7 mt-1">
                                        <toggle-button ref="entradaDinamica" id="entradaDinamica" @change="getValueToggleIn()"
                                        :value="entradaDinamica" :labels="{checked: 'Dinámica', unchecked: 'Estricta'}" 
                                        :width="90" :color="{checked: '#09BD8F', unchecked: '#dc6460'}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!--======================== Tabla de ordenes de Compra abiertas ===========================-->
                    <div class="table-responsive">
                        <div v-if="!openOrdersBuy.length">No hay Ordenes Abiertas</div>
                        <table v-else id="mytable" class="table table-bordred table-striped">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Market</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Fecha</th>
                                    <th>Cancelar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(order, index) in openOrdersBuy" :key="index">
                                    <td>{{ order.OrderType }}</td>
                                    <td>{{ order.Exchange }}</td>
                                    <td>{{ order.Limit }}</td>
                                    <td>{{ order.Quantity }}</td>
                                    <td>{{ timeClose(order.Opened) }}</td>
                                    <td>
                                        <button class="btn btn-primary" @click="cancelOrder(order.OrderUuid)">Cancelar Orden</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </b-card>
                <!--======================== Seccion Ganancias ===========================-->
                <b-card>
                    <h5 class="card-title mt-2">Ganancia:
                        <button class="text-white" :class="['btn-lg', (getProfit() >= 0) ? 'btn ganancia_positiva' : 'btn ganancia_negativa' ]" type="button">
                            {{ getProfit().toFixed(4) || '' }}%
                        </button> 
                        <small>&nbsp;
                             <span class="badge badge-success"> $ {{ ((this.$store.state.saldoUsd * getProfit()) / 100).toFixed(2) }}</span> 
                        </small> |
                        <small>Compré a:
                            <span class="badge badge-primary">  {{ priceIn }}</span>&nbsp;
                            <span class="badge badge-success">  ${{ (priceIn * usdBtcData.Last).toFixed(2) }}</span> 
                        </small>
                        <small>&nbsp;| Precio: 
                            <span class="badge badge-primary">  {{ this.btcCoinData.Ask }}</span>&nbsp;
                            <span class="badge badge-success">  ${{ (this.btcCoinData.Ask * usdBtcData.Last).toFixed(4) }}</span>
                        </small>
                        <!-- <small>&nbsp;| Diferencia: <span class="badge badge-primary">  {{ (this.btcCoinData.Ask - priceIn).toFixed(8) }}</span> </small> -->
                    </h5>
                </b-card>
                <!--======================== Formulario Salida Manual ===========================-->

                <b-card :header="'Vender ' + (dataCoin.MarketCurrencyLong || '') +' [Manual] - Disponible [' + (balanceCoin || 0)  
                                           + ' ' + (dataCoin.MarketCurrency || '') + '] - ($'+ toFix(balanceCoin * btcCoinData.Last * usdBtcData.Last, 2)+')'" 
                                           header-tag="h4" class="bg-danger-card">
                    <form @submit.prevent="sellLimit">
                        <div class="card" style="width: 100%;">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="col-md-3 mb-4">
                                        <label>Precio (Ask) </label> <span @click="setPriceSell" class="badge badge-danger"> {{ btcCoinData.Ask }}</span>
                                        <input :disabled="disabledExit" type="numeric" @change="calculateOrderSell(btcBuy, priceSell)" 
                                        @keyup="calculateOrderSell(btcBuy, priceSell)" 
                                        class="form-control" name="rateBuy" ref="rateBuy" v-model="priceSell"  placeholder="Precio" required>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <label >{{  dataCoin.BaseCurrency }} a Obtener</label>
                                        <input type="numeric" class="form-control" name="quantityToSell"  ref="quantityToSell" readonly v-model="getTotalCoinToSell" 
                                        placeholder="Cantidad" required>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <label >{{  dataCoin.MarketCurrency }}</label> <span @click="setCoinSell" class="badge badge-danger"> {{ balanceCoin }}</span>
                                        <input type="numeric" value="0.25706888" @change="calculateOrderSell(btcBuy, priceSell)" @keyup="calculateOrderSell(btcBuy, priceSell)" 
                                        class="form-control" :disabled="disabledExit"
                                        name="quantityAltcoin"  ref="quantityAltcoin" v-model="btcBuy" placeholder="Cantidad" required>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <label >Porcentaje</label> <br>
                                        <button style="width:100%;" type="button" 
                                            class="text-white btn btn-info">
                                            {{ getTotalCoinToSell ? (((priceSell - priceIn)* 100)/ priceIn).toFixed(4): 0 }} % - $ {{ ((this.$store.state.saldoUsd * (((priceSell - priceIn)* 100)/ priceIn)) / 100).toFixed(2) }}
                                        </button>  <br>
                                    </div>
                                </div>
                                <!-- ===================================== Porcentajes ===================================== -->
                                <div style="border-top: 1px dotted gray;" class="form-row">
                                    <div class="ml-2 mt-3">
                                        <button style="width:90px;" type="button" @click="getCustomPricePercentege(suma(getProfit().toFixed(6), (-1.5)))" 
                                            class="text-white btn btn-warning">
                                            {{ (getProfit().toFixed(4) - 1.5).toFixed(4) }} %
                                        </button> 
                                        <p class="below_text" style="font-size:10px;text-align:center;">-1.5</p>
                                    </div>
                                    <div class="ml-2 mt-3">
                                        <button style="width:90px;" type="button" @click="getCustomPricePercentege(suma(getProfit().toFixed(6), (-1)))" 
                                            class="text-white btn btn-warning">
                                            {{ (getProfit().toFixed(4) -1).toFixed(4) }} %
                                        </button>                   
                                        <p class="below_text" style="font-size:10px;text-align:center;">-1</p>                 
                                    </div>
                                    <div class="ml-2 mt-3">
                                        <button style="width:90px;" type="button" @click="getCustomPricePercentege(suma(getProfit().toFixed(6), (-0.5)))" 
                                            class="text-white btn btn-warning">
                                            {{ (getProfit().toFixed(4) - 0.5).toFixed(4) }} %
                                        </button>
                                        <p class="below_text" style="font-size:10px;text-align:center;">-0.5</p>                                 
                                    </div>
                                    <div class="ml-2 mt-3">
                                        <button style="width:90px;" type="button" @click="getCustomPricePercentege(getProfit().toFixed(6))" 
                                            class="text-white btn btn-primary">
                                            {{ getProfit().toFixed(4) }} %
                                        </button>                   
                                        <p class="below_text" style="font-size:10px;text-align:center;">Current</p>                 
                                    </div>
                                    <div class="ml-2 mt-3">
                                        <button style="width:90px;" type="button" @click="getCustomPricePercentege(suma(getProfit().toFixed(6), (1/2)))" 
                                            class="text-white btn btn-success">
                                            {{ suma(getProfit().toFixed(4), (1/2)) }} %
                                        </button>                                    
                                        <p class="below_text" style="font-size:10px;text-align:center;">+0.5</p>
                                    </div>
                                    <div class="ml-2 mt-3">
                                        <button style="width:90px;" type="button" @click="getCustomPricePercentege(suma(getProfit().toFixed(6), 1))" 
                                            class="text-white btn btn-success">
                                            {{ suma(getProfit().toFixed(4), 1) }} %
                                        </button>                                    
                                        <p class="below_text" style="font-size:10px;text-align:center;">+1</p>
                                    </div>
                                    <div class="ml-2 mt-3">
                                        <button style="width:90px;" type="button" @click="getCustomPricePercentege(suma(getProfit().toFixed(6), (3/2)))" 
                                            class="text-white btn btn-success">
                                            {{ suma(getProfit().toFixed(4), (3/2)) }} %
                                        </button>                                 
                                        <p class="below_text" style="font-size:10px;text-align:center;">+1.5</p>   
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <button :disabled="disabledExit" class="text-white btn btn-danger" style="width: 88px">
                                            <div class="row">
                                                <div v-if="disabledExit" class="col-sm-2">
                                                    <clip-loader class="mt-0"  :loading="true" color="white" size="10px"></clip-loader>
                                                </div>
                                                <div v-else class="col-sm-1">
                                                    <i class="fa fa-circle-o-notch " style="font-size:10px;color:white;"></i>
                                                </div>
                                                <div v-if="disabledExit" class="col-sm-0 ml-1">Vender</div>
                                                <div v-else class="col-sm-0">Vender</div>
                                            </div>
                                        </button>
                                    </div>
                                    <div class="col-sm-3"  >
                                        <button type="button" :disabled="cancelExitButton" @click="cancelExitManual()" class="text-white btn btn-primary">
                                                Cancelar Venta
                                        </button>
                                    </div>
                                    <div class="col-sm-3 mt-1">
                                        <toggle-button ref="salidaDinamica" id="salidaDinamica" @change="getValueToggleExit()"
                                        :value="salidaDinamica" :labels="{checked: 'Dinámica', unchecked: 'Estricta'}" 
                                        :width="90" :color="{checked: '#09BD8F', unchecked: '#dc6460'}" />
                                    </div>
                                    <div class="col-sm-2 mt-1">
                                        <input step="any" type="number" class="form-control form-control-sm"
                                        @keyup="getCustomPercentage($event.target.value)"  @change="getCustomPercentage($event.target.value)" 
                                        min="0.01" max="100" placeholder="%">
                                    </div>
                                    <div class="col-sm-2 mt-0">
                                        <button type="button" @click="getCustomPricePercentege(customPercentage)" 
                                            class="text-white btn btn-success">
                                            {{ customPercentage }} %
                                        </button>                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!--======================== Tabla de Ordenes de Venta Abiertas ===========================-->
                    
                    <div class="table-responsive">
                        <div v-if="!openOrdersSell.length">No hay Ordenes Abiertas</div>
                        <table v-else id="mytable" class="table table-bordred table-striped">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Market</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Fecha</th>
                                    <th>Ganancia</th>
                                    <th>Cancelar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(order, index) in openOrdersSell" :key="index">
                                    <td>{{ order.OrderType }}</td>
                                    <td>{{ order.Exchange }}</td>
                                    <td>{{ order.Limit }}</td>
                                    <td>{{ order.Quantity }}</td>
                                    <td>{{ timeClose(order.Opened) }}</td>
                                    <td>
                                        {{ getOrderProfit(substractOrderFees(order.Limit, order.Quantity), substractOrderFees(priceIn, order.Quantity)) }} %
                                    </td>
                                    <td>
                                        <button class="btn btn-primary" @click="cancelOrder(order.OrderUuid)">Cancelar Orden</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </b-card>

                <!--======================== Salida Automatica ===========================-->
                <b-card :header="'Vender ' + (dataCoin.MarketCurrencyLong || '') +' [Trailing] - Disponible [' + (balanceCoin || 0)  
                                           + ' ' + (dataCoin.MarketCurrency || '') + '] - ($'+ toFix(balanceCoin * btcCoinData.Last * usdBtcData.Last, 2)+')'" 
                                           header-tag="h4" class="bg-warning-card">
                    <form @submit.prevent="setTrailingParameters">
                        <div class="card" style="width: 100%;">
                            <div class="card-body">
                                <h5 class="card-title">Trailing Stop Loss</h5>
                                <div class="card" style="width: 100%;">
                                    <div class="card-body">
                                        <div class="justify-content-around row">
                                            <div class="col-xs-4 mt-3 justify-content-around">
                                                <button :class="['text-white ml-4 btn', ((priceNeededSLTrailing ? (((priceNeededSLTrailing - priceIn)* 100)/ priceIn).toFixed(4): 0) >= 0) ? 'btn-success' : 'btn-danger' ]">
                                                    {{ priceNeededSLTrailing ? (((priceNeededSLTrailing - priceIn)* 100)/ priceIn).toFixed(4): 0 }} %
                                                </button>                  
                                            </div>
                                            <div class="col-xs-4 mt-3">
                                                <button :class="['ml-4 btn text-white', (getProfit() >= 0) ? 'btn-success' : 'btn-danger']">
                                                    {{ getProfit().toFixed(4) || '' }}%
                                                </button>
                                            </div>
                                            <div class="col-xs-4 mt-3">
                                                <button class="ml-4 btn btn-primary" >
                                                    {{ priceNeededSWTrailing ? (((priceNeededSWTrailing - priceIn)* 100)/ priceIn).toFixed(4): 0 }} %
                                                </button>    
                                            </div>
                                        </div>

                                        <div class="justify-content-around row">
                                            <div class="col-xs-4 mt-3 justify-content-around">
                                                <button :class="['text-white ml-4 btn', ((priceNeededSLTrailing ? (((priceNeededSLTrailing - priceIn)* 100)/ priceIn).toFixed(4): 0) >= 0) ? 'btn-success' : 'btn-danger' ]">
                                                    StopLoss: {{ priceNeededSLTrailing }}
                                                </button>                    
                                            </div>
                                            <div class="col-xs-4 mt-3">
                                                <button :class="['ml-4 btn text-white', (getProfit() >= 0) ? 'btn-success' : 'btn-danger']">
                                                    Actual: {{ btcCoinData.Ask }}
                                                </button>                
                                            </div>
                                            <div class="col-xs-4 mt-3">    
                                                <button class="ml-4 btn btn-primary" >
                                                    Trailing: {{ priceNeededSWTrailing }}
                                                </button>    
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- <small>StopWin +1%: Precio: {{ getPriceToProfit(1) }} | Btc {{ btcToWin }} </small>
                                <div class="form-row alert alert-success">
                                    <div class="col-md-2">
                                        <p class="card-text mt-1">StopWin%: 
                                    </p>
                                    </div>
                                    <div class="col-md-2">
                                    <p class="card-text">
                                        <input step="any" type="number" class="form-control form-control-sm" :disabled="disabledStop"
                                        @keyup="getCustomPriceProfitWin($event.target.value)"  @change="getCustomPriceProfitWin($event.target.value)" 
                                        name="stopWin" placeholder="%" required>
                                    </p>
                                    </div>
                                    <div class="col-md-7 mt-1">
                                        Precio {{ priceNeededSW }} | Btc {{ btcToWinSW }}
                                    </div>
                                </div> -->
                                <!-- <div class="mt-2">
                                    <small class="mt-2">StopLoss -1%: Precio: {{ getPriceToProfitLoss(1) }} | Btc {{ btcToLoss }} </small>
                                </div> -->
                                <div v-if="!showTrailingFields" class="form-row alert alert-danger mt-3">
                                    <div class="col-md-2">
                                        <p class="card-text mt-1">StopLoss%: </p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="card-text">
                                            <input  type="number" step="any" class="form-control form-control-sm"  :disabled="disabledStop"
                                            @keyup="getCustomPriceProfitLoss($event.target.value, 0)" @change="getCustomPriceProfitLoss($event.target.value, 0)" 
                                            name="stopLoss" placeholder="-%" required>
                                        </p>
                                    </div>
                                    <div class="col-md-7 mt-1">
                                        Precio {{ priceNeededSL }} | Btc {{ btcToLossSL }}
                                    </div>
                                </div>
                                <div class="card" style="width: 100%;">
                                    <div class="card-body">
                                        <div class="row justify-content-start">
                                            <div class="col-sm-3 mt-1">
                                                <toggle-button ref="trailingAutomaticOn" id="trailingAutomaticOn" @change="showTrailingOptions()"
                                                :value="trailingAutomaticOn" :labels="{checked: 'Automático', unchecked: '  Manual'}" 
                                                :width="100" :color="{checked: '#09BD8F', unchecked: '#dc6460'}"/>
                                            </div>
                                            <template v-if="showTrailingFields">
                                                <div class="col-sm-3 mt-2">
                                                    StopLoss (%):
                                                </div>
                                                <div class="col-sm-2 mt-1" style="margin-left: -75px;">
                                                    <input step="any" type="number" class="form-control form-control-sm" v-model="stopLossTrailing"
                                                    @keyup="setStopLossTrailing($event.target.value)"  @change="setStopLossTrailing($event.target.value)" 
                                                    max="100" placeholder="%">
                                                </div>
                                                <div class="col-sm-2 mt-2">
                                                    Inicio (%):
                                                </div>
                                                <div class="col-sm-2 mt-1" style="margin-left: -45px;">
                                                    <input step="any" type="number" class="form-control form-control-sm" v-model="valueToInitTrailing"
                                                    @keyup="setvalueToInitTrailing($event.target.value)"  @change="setvalueToInitTrailing($event.target.value)" 
                                                    max="100" placeholder="%">
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-sm-6">
                                        <button type="button" :class="classExit" style="width: 150px;">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <clip-loader v-if="disabledButtonExit_1" class="mt-0"  :loading="true" color="white" size="10px"></clip-loader>
                                                        <i v-else class="fa fa-circle-o-notch " style="font-size:10px;color:white;"></i>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <div class="col-sm-0 ml-2">Estatus {{ statusProfit }}</div>
                                                    </div>
                                                </div>
                                            </div>                                        
                                        </button>
                                    </div>
                                    <div class="text-right col-sm-6">
                                        <button class="btn btn-success" :disabled="disabledButtonExit_1" type="submit"> Colocar datos de Salida</button>
                                        <button class="btn btn-danger text-white" :disabled="disabledButtonExit_2" @click="cancelExitAutomatic()"> Cancelar Salida</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </b-card>

                <!--====================== Historico de Ordenes ========================-->

                <b-card :header="'Histórico de Ordenes'" class="bg-primary-card">
                    <div class="table-responsive">
                        <font size="1.7">
                        <div v-if="!historicOrders.length">No hay Historico de Ordenes</div>
                        <table v-else id="mytable" class="table table-bordred table-striped">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Precio en {{  dataCoin.BaseCurrency }} / USD</th>
                                    <th>Precio Actual</th>
                                    <th>Cantidad {{  dataCoin.MarketCurrency }}</th>
                                    <th>Cantidad {{  dataCoin.BaseCurrency }}</th>
                                    <th>Ejecutada</th>
                                    <th>Comisión ($)</th>
                                    <th>En $</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(order, index) in historicOrders" :key="index">
                                    <td v-if="historicOrders[index].OrderType == 'LIMIT_BUY'">COMPRA</td>
                                    <td v-else>VENTA</td>                                    
                                    <td>{{ historicOrders[index].Limit }} $({{ (historicOrders[index].Limit * usdBtcData.Last).toFixed(2) }})</td>
                                    <td>{{ btcCoinData.Ask }} $({{ (btcCoinData.Ask * usdBtcData.Last).toFixed(2) }})</td>
                                    <td>{{ historicOrders[index].Quantity }}</td>
                                    <td>{{ (historicOrders[index].Price - historicOrders[index].Commission).toFixed(8)}}</td>
                                    <td>{{ timeClose(historicOrders[index].Closed) }}</td>
                                    <td>{{ (historicOrders[index].Commission * usdBtcData.Last).toFixed(2)  }}</td>
                                    <td>{{ ((historicOrders[index].Price - historicOrders[index].Commission) * usdBtcData.Last).toFixed(2) }}</td>
                                    <td v-if="historicOrders[index].OrderType == 'LIMIT_SELL' && index < (historicOrders.length - 1)" style="color: #000000;"
                                    :class="[calculateHistoricProfit(historicOrders[index + 1].Price, historicOrders[index].Price) > 0 ? 'table-success' : 'table-danger' ]" >
                                        {{ calculateHistoricProfit(historicOrders[index + 1].Price, historicOrders[index].Price) }}
                                    </td>
                                    <td v-else>-</td>
                                </tr>
                            </tbody>
                        </table>
                        </font>
                    </div>
                </b-card>
            </div>
            <!--====================== Datos Para Trading ========================-->
            <div class="col-lg-12 col-xl-4">
                <div class="row">
                    <div class="col-lg-6 col-xl-12 col-md-6">
                        <b-card>
                            <div class="row">
                                <div class="col-12 text-center " style="border-bottom: 1px dotted gray;">
                                    <h5 class="text_head_Color">
                                        <a target="_blank" :href="'https://global.bittrex.com/Market/Index?MarketName='+market">{{ dataCoin.MarketCurrencyLong }} </a>
                                        <img :src="dataCoin.LogoUrl" class="mb-2" alt="" height="30px" width="30px">
                                    </h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="row ">
                                        <div class="col-3 mt-4 mb-3 text-center">
                                            <i class="fa fa-arrow-circle-down fb_text mt-2" style="color:green;"></i>
                                        </div>
                                        <div class="col-9 mt-4">
                                            <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                                            <h5 v-else class="text_head_Color">{{ btcCoinData.Bid }} <i class="fa fa-sm fa-btc fb_text" style="font-size: 18px;"></i></h5>
                                            <p class="below_text">Bid (Comprar {{ getNameCoin }}) - ${{ toFix(btcCoinData.Bid * usdBtcData.Last, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="row mt-3 mb-3  line">
                                        <div class="col-3 mt-4 text-center">
                                            <i class="fa fa-arrow-circle-up fb_text mt-2" style="color:red;"></i>
                                        </div>
                                        <div class="col-9 mt-4">
                                            <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                                            <h5 v-else class="text_head_Color">{{ btcCoinData.Ask }} <i class="fa fa-sm fa-btc fb_text" style="font-size: 18px;"></i></h5>
                                            <p class="below_text">Ask (Vender {{ getNameCoin }}) - ${{ toFix(btcCoinData.Ask * usdBtcData.Last, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="row mt-3 mb-3  line">
                                        <div class="col-3 mt-4 text-center">
                                            <i :class="['fa fa-bar-chart fb_text mt-2', ((((btcCoinData.Last - btcCoinData.PrevDay)/btcCoinData.PrevDay) * 100) >= 0) ? 'color_icon_positive' : 'color_icon_negative' ]"></i>
                                        </div>
                                        <div class="col-9 mt-4">
                                            <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                                            <h5 v-else class="text_head_Color" :class="[ ((((btcCoinData.Last - btcCoinData.PrevDay)/btcCoinData.PrevDay) * 100) >= 0) ? 'color_icon_positive' : 'color_icon_negative' ]">
                                                {{ toFix(((btcCoinData.Last - btcCoinData.PrevDay)/btcCoinData.PrevDay) * 100, 2)  }} %
                                            </h5>
                                            <p class="below_text">Variación</p>
                                        </div>
                                    </div>
                                    <div class="row mt-3 mb-3  line">
                                        <div class="col-3 mt-4 text-center">
                                            <i class="fa fa-shopping-cart fb_text mt-4'"></i>
                                        </div>
                                        <div class="col-9 mt-4">
                                            <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                                            <h5 v-else class="text_head_Color">
                                                {{ toFix(btcCoinData.BaseVolume, 2)  }} <i class="fa fa-sm fa-btc fb_text" style="font-size: 18px;"></i>
                                            </h5>
                                            <p class="below_text">Volumen - ${{ toFix(btcCoinData.BaseVolume * usdBtcData.Last, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="row mt-3 mb-3  line">
                                        <div class="col-3 mt-4 text-center">
                                            <i class="fa fa-cloud-upload fb_text mt-2" style="color:green;"></i>
                                        </div>
                                        <div class="col-9 mt-4">
                                            <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                                            <h5 v-else class="text_head_Color">
                                                {{ toFix(btcCoinData.High, 8)  }} <i class="fa fa-sm fa-btc fb_text" style="font-size: 18px;"></i>
                                            </h5>
                                            <p class="below_text">High 24H - ${{ toFix(btcCoinData.High * usdBtcData.Last, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="row mt-3 mb-3  line">
                                        <div class="col-3 mt-4 text-center">
                                            <i class="fa fa-cloud-download fb_text mt-2" style="color:red;"></i>
                                        </div>
                                        <div class="col-9 mt-4">
                                            <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                                            <h5 v-else class="text_head_Color">
                                                {{ toFix(btcCoinData.Low, 8)  }} <i class="fa fa-sm fa-btc fb_text" style="font-size: 18px;"></i>
                                            </h5>
                                            <p class="below_text">Low 24H - ${{ toFix(btcCoinData.Low * usdBtcData.Last, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="row mt-3 mb-3  line">
                                        <div class="col-3 mt-4 text-center">
                                            <i class="fa fa-info-circle fb_text mt-2" style="color:red;"></i>
                                        </div>
                                        <div class="col-9 mt-4">
                                            <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                                            <h5 v-else class="text_head_Color">
                                                {{ toFix(dataCoin.MinTradeSize, 8)  }} <img :src="dataCoin.LogoUrl" class="mb-2" alt="" height="30px" width="30px">
                                            </h5>
                                            <p class="below_text">Min Trade </p>
                                        </div>
                                    </div>
                                    <div class="row mt-3 mb-3  line">
                                        <div class="col-3 mt-4 text-center">
                                            <i class="fa fa-calendar fb_text mt-2" style="color:green;"></i>
                                        </div>
                                        <div class="col-9 mt-4">
                                            <pulse-loader v-if="spinner" :loading="true" :color="color" :size="size"></pulse-loader>
                                            <h5 v-else class="text_head_Color">
                                                {{ timeClose(dataCoin.Created) }}
                                            </h5>
                                            <p class="below_text">Creado</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </b-card>
                    </div>
                    <!--====================== Set Parametros ========================-->
                    <div class="col-lg-6 col-xl-12 col-md-6">
                        <b-card>
                            <div class="row">
                                <div class="col-12 text-center" style="border-bottom: 1px dotted gray;">
                                    <h5 class="text_head_Color mb-4">Ajustar Parametros 
                                        <i class="fa fa-cog fb_text fb_text ml-4" style="color:green;"></i>
                                    </h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="row ">
                                        <div class="col-2 mt-4 mb-3 text-center">
                                            <i class="fa fa-bell fb_text mt-2" style="color:red;"></i>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <h5> Ganancia</h5>
                                            <p class="below_text" style="font-size:10px;">Alarma</p>
                                        </div>
                                        <div class="col-3 mt-4 mb-3 text-center">
                                            <select style="width: auto;" name="conditionAlarm" @change="setConditionAlarm()" ref="conditionAlarm" class="form-control" size="1">
                                                <option value="0">
                                                    Select
                                                </option>
                                                <option value="0">Mayor</option>
                                                <option value="1">Menor</option>
                                            </select>  
                                            <p v-if="statusAlarm" class="mt-3" style="font-size:11px;">Encendida</p>                              
                                            <p v-else  class="mt-3" style="font-size:12px;">Apagada</p>                              
                                        </div>
                                        <div class="col-3 mt-4 mb-3 text-center">
                                            <input step="any" type="number" ref="valueAlarm" class="form-control form-control-sm"
                                            max="100" placeholder="%" @change="setValueAlarm()">
                                            <button v-if="statusAlarm" :disabled="disableAlarmButton" @click="setAlarm()" class="text-white btn btn-danger mt-2" >
                                                Off
                                            </button>
                                            <button v-else @click="setAlarm()" :disabled="disableAlarmButton" class="text-white btn btn-success mt-2" >
                                                On
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row line">
                                        <div class="col-2 mt-4 mb-3 text-center">
                                            <i class="fa fa-arrow-circle-down fb_text mt-2" style="color:green;"></i>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <input style="width:95px;" type="numeric" class="form-control" name="incrementToBuy" ref="incrementToBuy" v-model="incrementToBuy"  placeholder="Comprar" required>
                                            <p class="below_text">(+) COMPRAR</p>
                                        </div>
                                        <div class="col-3 mt-4 mb-3 text-center">
                                            <button @click="setIncremenToBuyValue()" class="text-white btn btn-success" >
                                                Guardar
                                            </button>                                        
                                        </div>
                                        <div class="col-3 mt-4 mb-3 text-center">
                                            <button @click="setIncremenToBuyValueDefault()" class="text-white btn btn-primary" >
                                                Default
                                            </button>  
                                            <p class="below_text" style="font-size:10px;">0.0000001</p>
                                        </div>
                                    </div>

                                    <div class="row mt-3 mb-3  line">
                                        <div class="col-2 mt-4 mb-3 text-center">
                                            <i class="fa fa-arrow-circle-up fb_text mt-2" style="color:red;"></i>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <input style="width:95px;" type="numeric" class="form-control" name="decrementToSell" ref="decrementToSell" v-model="decrementToSell"  placeholder="Vender" required>
                                            <p class="below_text">(-) VENDER</p>
                                        </div>
                                        <div class="col-3 mt-4 mb-3 text-center">
                                            <button @click="setDecremenToSellValue()" class="text-white btn btn-danger" >
                                                Guardar
                                            </button>                                        
                                        </div>
                                        <div class="col-3 mt-4 mb-3 text-center">
                                            <button @click="setDecrementToSellValueDefault()" class="text-white btn btn-primary" >
                                                Default
                                            </button>  
                                            <p class="below_text" style="font-size:10px;">0.0000001</p>
                                        </div>
                                    </div>

                                    <!-- <div class="row mt-3 mb-3  line">
                                        <div class="col-2 mt-4 mb-3 text-center">
                                            <i class="fa fa-exclamation-triangle fb_text mt-2" style="color:red;"></i>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <input type="numeric" class="form-control" name="stopLossGlobal" ref="stopLossGlobal" v-model="stopLossGlobal"  placeholder="SL" required>
                                            <p class="below_text">StopLoss</p>
                                        </div>
                                        <div class="col-3 mt-4 mb-3 text-center">
                                            <button @click="setStopLossGlobalValue()" class="text-white btn btn-danger" >
                                                Guardar
                                            </button>                                        
                                        </div>
                                        <div class="col-3 mt-4 mb-3 text-center">
                                            <button @click="setStopLossGlobalDefault()" class="text-white btn btn-primary" >
                                                Default
                                            </button>  
                                            <p class="below_text" style="font-size:10px;">5%</p>
                                        </div>
                                    </div> -->

                                    <div class="row mt-3 mb-3  line">
                                        <div class="col-2 mt-4 text-center">
                                            <i class="fa fa-battery-4 fb_text mt-2"></i>
                                        </div>
                                        <div class="col-6 mt-4">
                                            <toggle-button ref="timeInForce" id="timeInForce" @change="setTimeInForceGlobal()"
                                            :value="timeInForceToggle" :labels="{checked: 'Good-Til-Cancelled', unchecked: 'Inmediate or Cancel'}" 
                                            :width="150" :color="{checked: '#09BD8F', unchecked: '#dc6460'}" />                                            
                                            <p class="below_text" >Time In Force</p>
                                        </div>
                                    </div>

                                    <div class="row mt-3 mb-3  line">
                                        <div class="col-2 mt-4 mb-3 text-center">
                                            <i class="fa fa-key fb_text mt-2" style="color:green;"></i>
                                        </div>
                                        <div class="col-10 mt-4">
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
                            </div>
                        </b-card>
                    </div>
                </div>
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
                market: '', headerTitle: '', allMarketSummarie: '', btcCoinData: '', spinner: true, btcMarket: [], color: "#2c3e50", size: "10px",
                dataCoin: '', usdBtcData: '', instances: [], balances: 0, balanceUsd: 0, balanceCoin: 0, getTotalCoinToBuy: 0, priceBuy: '', btcSell: '',
                sound: 'https://www.pacdv.com/sounds/mechanical_sound_effects/coins_4.wav', disabledIn: false, cancelInButton: true, cancelExitButton: true,
                 getTotalCoinToSell: 0, entradaDinamica: true, salidaDinamica: false, timeRefresh: '', openOrdersSell: [], openOrdersBuy: [], disabledExit: false, 
                 priceSell: '', btcBuy: '', priceIn: 0, quantityIn: 0, historicOrders: '', disabledStop: false, priceNeededSW: 0, priceNeededSL: 0, btcToWinSW: 0,
                 btcToLossSL: 0, classExit: 'btn btn-primary', disabledSpinnerAutomatic: false, statusProfit: 'Apagado', disabledButtonExit_1: false,
                 disabledButtonExit_2: true, profit: 0, monitor: 'Off', incrementToBuy: 0, decrementToSell: 0, stopWin: 0, stopLoss: 0, stopLossGlobal: 5,
                 customPercentage: 1, timeInForceToggle: true, timeInForce : 'GTC', password_1: '', password_2: '', estatusApi: 'OFF', priceNeededSLTrailing: 0,
                 priceNeededSWTrailing: 0, trailingPrice: 0, soundOrderOn: 'https://www.pacdv.com/sounds/mechanical_sound_effects/camera_click.wav',
                 soundStopLoss: 'https://www.pacdv.com/sounds/machine_sound_effects/electric-drill-2.mp3', askVentaRapida: 0, ventaRapidaOn: false, 
                 noSeHaCancelado: null, soundCancelOrder: 'https://www.pacdv.com/sounds/machine_sound_effects/electric-drill-3.mp3', askCompraRapida: 0,
                 trailingAutomaticOn: false, stopLossTrailing: 0.5, valueToInitTrailing: 1.5, showTrailingFields: false, disableAlarmButton: true,
                 soundUnoPorciento: 'https://www.pacdv.com/sounds/miscellaneous_sounds/knocking_6.wav', conditionAlarm: null, valueAlarm: 0, statusAlarm: false,
            }
        },
        mounted: function () {

            clearInterval(this.interval)
            this.getOrderHistory()
            this.refreshData();
            this.getMarketSummary();

            //Arreglo de las altcoins por variación
            this.arrayCoinsByVariation = this.$store.state.arrayCoinsByVariation
            //Time In Force
            this.timeInforce = this.$store.state.timeInForceGlobal

            unsub = this.$store.subscribe((mutation, state) => {
                if (mutation.type == "left_menu") {
                    this.instances.forEach(function (item, index) {
                        setTimeout(function () {
                            item.resize();
                        });
                    });
                }
            });
            //Incremento y decremento
            this.incrementToBuy = this.toFix(this.$store.state.incrementToBuy, 8)
            this.decrementToSell = this.toFix(this.$store.state.decrementToSell, 8)

        },watch: {
            '$route' (to, from) {
                location.reload();
            }
        },
        methods: {
            getMarketSummary(){ //Traer todo los datos del mercado

                this.$store.commit("setCalling", true) //Encender la señal de api

                console.log('interval trading...')
                axios
                .get('/auth/api/getmarketsummaries')
                .then( response => response.data) 
                .then(data => {
                    this.allMarketSummarie = data.getmarketSummaries.result
                    //Datos moneda trade
                    this.btcCoinData = data.getmarketSummaries.result.filter(summarie => summarie.MarketName == this.market)[0]
                    
                    //Datos USD-BTC
                    this.usdBtcData = data.getmarketSummaries.result.filter(summarie => summarie.MarketName == 'USD-BTC')[0]
                    
                    this.spinner = false
                    this.variationCoin = ((this.btcCoinData.Last - this.btcCoinData.PrevDay)/this.btcCoinData.PrevDay) * 100

                    this.btcMarket = data.getmarketSummaries.result.filter(summarie => summarie.MarketName[0] == 'B' && summarie.PrevDay != 0)
                    
                    //Ordena el mercado btc por variación
                    this.btcMarket.sort((a, b) => (
                        (((a.Last) - (a.PrevDay))/(a.PrevDay) < ((b.Last) - (b.PrevDay))/(b.PrevDay))
                    ) ? 1 : -1)

                    this.$store.commit("setCalling", true)

                    this.getCustomPriceProfitWin(1) //Set precio de remolque (Trailing)

                    /**************************************************************/
                    /*   Trailing automático si se cumple un porcentaje (1.5%)    */
                    /*   Si la ganancia llega a 1.5% se activa el trailing        */
                    /*   automatico para seguir la ganancia o salir si baja 0.5%  */
                    /**************************************************************/

                    if((this.getProfit() > this.valueToInitTrailing) && this.trailingAutomaticOn){
                        console.log('Iniciando trailing automático')
                        this.trailingAutomaticOn = true
                        this.getCustomPriceProfitLoss(this.stopLossTrailing, 1)
                        this.setTrailingParameters()
                    }

                    if(this.statusAlarm){
                        if(this.conditionAlarm == 0)
                            if(this.getProfit() > this.valueAlarm){
                                var audio = new Audio(this.soundUnoPorciento);
                                audio.play();
                            }
                        
                        if(this.conditionAlarm == 1)
                            if(this.getProfit() < this.valueAlarm){
                                var audio = new Audio(this.soundUnoPorciento);
                                audio.play();
                            }
                    }


                }).catch(error => {
                    this.$store.commit("setCalling", false) //Apagar la señal de api
                    console.log('Error de Getmarketsummaries 8')
                });

                //set Favicon
                document.querySelector("link[rel='shortcut icon']").href = this.getProfit() > 0 ? "images/win.png" : "images/loss.png";

                this.getMarkets()
                this.getBalance()
                this.getOpenOrders()
                this.checkPasswords()

                this.$store.state.calling ? this.monitor = "Working!" : this.monitor = "Off"
                
            },
            setAlarm(){
                this.statusAlarm = !this.statusAlarm
            },
            setConditionAlarm(){
                this.conditionAlarm = this.$refs.conditionAlarm.value
                if(this.conditionAlarm && this.valueAlarm)
                    this.disableAlarmButton = false
            },
            setValueAlarm(){
                this.valueAlarm = this.$refs.valueAlarm.value
                if(this.conditionAlarm && this.valueAlarm)
                    this.disableAlarmButton = false
            },
            substractOrderFees(rate, quantity){
                return ((rate * quantity) - ((rate * quantity * 0.25) / 100)).toFixed(7)
            },
            getOrderProfit(newOrder, originalOrder){
                return (((newOrder - originalOrder) * 100) / originalOrder).toFixed(2)
            },
            setTrailingParameters(){

                if(this.priceIn){

                    this.disabledButtonExit_1 = true
                    this.disabledButtonExit_2 = false
                    this.disabledStop = true
                    this.disabledSpinnerAutomatic = true

                    if(!this.showTrailingFields)
                        this.sellLimitAutomatic(this.btcCoinData.Ask, false,false)
                    else
                        this.trailingAutomatic()

                }else
                    miniToastr.success('Sin saldo', "Error",)

            },
            trailingAutomatic(){
                this.trailingAutomaticOn = true
                this.statusProfit = 'Encendido'

            },
            setStopLossTrailing(stopLoss){
                this.stopLossTrailing = stopLoss
            },
            setvalueToInitTrailing(valueToInitTrailing){
                this.valueToInitTrailing = valueToInitTrailing
            },
            sellLimitAutomatic(askSemilla, pasePrevio, uuid){ //Salida automatica nueva
                    //===================Trabajar=================//

                if(this.disabledButtonExit_1 && !this.ventaRapidaOn) //Si no se ha cancelado
                {
                    this.statusProfit = 'Encendido'

                    //******************************************************************************************************************//
                    // differencePipsSemilla: Diferencia en PIPS entre Precio Remolque (Techo) y precio (Ask) al momento de comenzar    // 
                    // differencePipsActual: Diferencia en PIPS entre Precio Remolque (Techo) y prcio (Ask) actual (dinámico)           //  
                    // Precio remolque => this.priceNeededSWTrailing                                                                    //  
                    // StopLoss => this.priceNeededSLTrailing                                                                           //  
                    //******************************************************************************************************************//

                    let differencePipsSemilla = this.priceNeededSWTrailing - askSemilla
                    let differencePipsActual  = this.priceNeededSWTrailing - this.btcCoinData.Ask

                    //******************************************************************************************************************//
                    // Si differencePipsActual es menor que differencePipsSemilla (El precio Ask Subio)                                 // 
                    //******************************************************************************************************************//
                    
                    if(differencePipsActual < differencePipsSemilla){ 
                        
                    //******************************************************************************************************************//
                    // Si sube el precio debo incrementar el precio remolque y el StopLoss                                              // 
                    // Debo utilizar la diferencia para incrementar el precio remolque y el StopLoss                                    // 
                    //******************************************************************************************************************//

                        console.log('1. Subio')

                        let differencePipsPositiva = differencePipsSemilla - differencePipsActual
                        this.priceNeededSWTrailing = (parseFloat(this.priceNeededSWTrailing) + parseFloat(differencePipsPositiva)).toFixed(8)
                        this.priceNeededSLTrailing = (parseFloat(this.priceNeededSLTrailing) + parseFloat(differencePipsPositiva)).toFixed(8)

                        console.log('2. Cambio el valor del precio semilla de: ', askSemilla, ' a: ',  this.btcCoinData.Ask)

                        //*************************************************************************************************************************//
                        // Y el nuevo precio semilla (Ask) sera el precio actual, para poder ir arrastrando el stopLoss en la siguiente iteracion  // 
                        //*************************************************************************************************************************//

                        askSemilla = this.btcCoinData.Ask

                    }else{

                        if(differencePipsSemilla != differencePipsActual){

                            //**********************************************************************//
                            // Si baja el precio debo mantener el precio remolque y el StopLoss     // 
                            //**********************************************************************//

                            console.log('Bajo')

                            if(this.btcCoinData.Ask <= this.priceNeededSLTrailing){

                                //*************************************************************************//
                                // Si el precio actual es menor o igual que el StopLoss debo salir rapido  // 
                                //*************************************************************************//

                                miniToastr.success('StopLoss Alcanzado', "Info",)
                                console.log('Salida Rápida a', this.btcCoinData.Ask )

                                this.ventaRapida(false, 0, 0) //uuid, precioPrevio, disabledButtonExit_1

                                this.ventaRapidaOn = true
                            }
                        }
                        else{
                            //***************************************//
                            // Sin cambio de precio, sigue el ciclo  // 
                            //***************************************//
                            console.log('Sin Cambio')
                        }
                    }

                    //***********************//
                    // Rellamo a la funcion  // 
                    //***********************//

                    if(this.disabledButtonExit_1) //Si no se ha cancelado
                        setTimeout(function () {
                            this.sellLimitAutomatic(askSemilla, true, false)
                        }.bind(this), 5000)
                }else{

                    //******************//
                    // Se ha cancelado  // 
                    //******************//
                    if(!this.disabledButtonExit_1) //Si no se ha cancelado
                        this.statusProfit = 'Apagado'
                }
            },
            ventaRapida(uuid, precioPrevio, cancelValue){

                //****************************************************************************//
                // Comoe uni dos funciones, debo unir las variables que cancelan en una sola  // 
                // disableExit: Salida manual, disabledButtonExit_1: salida trailing          // 
                //****************************************************************************//

                this.noSeHaCancelado = cancelValue ? this.disabledExit : this.disabledButtonExit_1

                if(this.noSeHaCancelado){ //Si no se ha cancelado

                    this.statusProfit = 'Saliendo'

                    //*******************************************************//
                    // Funcion para garantizar vender lo mas rapido posible  // 
                    //*******************************************************//
                    if(!uuid)
                        console.log('0. Inicio venta Rápida')

                    clearInterval(this.timeRefresh); //detener peticiones a la api

                    //*******************************************************//
                    // Obtengo(Ask) Actual para mejorarlo si es necesario    // 
                    //*******************************************************//
                    
                    axios
                    .get(`/auth/api/getticker/${this.market}`)
                        .then(response => response.data)
                        .then( data => { 
                                                        
                            this.askVentaRapida = data.getTicker.result.Ask

                            console.log('1 Obtengo Ask actual: ', this.askVentaRapida)
                            
                            let precioMejor = this.askVentaRapida - this.$store.state.decrementToSell // debe ser - state decrement
                            //let precioMejor = this.askVentaRapida + 0.00008123 // debe ser - state decrement (PRUEBAS)

                            console.log('2 Mejoro el precio Ask Actual', precioMejor)

                            //*******************************************************//
                            // Si hay una orden colocada verifico que este primera   // 
                            // sino debo cancelar y mejorar la orden                 // 
                            //*******************************************************//

                            if(uuid){ //Si hay una orden
                                console.log('6. Hay uuid: ', uuid)
                                console.log('7. Hay precioPrevio: ', precioPrevio)

                            //******************************************//
                            // Si el Ask actual es mejor que mi orden   // 
                            // debo cancelar y mejorar la orden         // 
                            //******************************************//
                                if(this.askVentaRapida < precioPrevio){
                                //if(this.askVentaRapida ){ //para pruebas
                                    console.log('Cancelo la orden, no es la mejor')
                                    axios
                                    .get(`/auth/api/cancel/${uuid}`)
                                    .then(response => response.data)
                                    .then( data => {          
                                        console.log('8. orden Cancelada')      

                                        //****************************************************//
                                        // Cancelo la orden y rellamo a la funcion sin uuid   // 
                                        //****************************************************// 
                                        if(this.noSeHaCancelado) //Si no se ha cancelado
                                            this.ventaRapida(null, 0, cancelValue)
                                        else{
                                            this.statusProfit = 'Apagado'
                                            this.refreshData();
                                        }

                                        console.log('response orden cancelada', data)               
                                        miniToastr.info("Orden Cancelada", "Success",)

                                        var audio = new Audio(this.soundCancelOrder);
                                        audio.play();

                                    }).catch(error => {
                                        this.$store.commit("setCalling", false) //Apagar la señal de api
                                        console.log('Error de cancelorder 56')
                                    });
                                }else{
                                    console.log('9. Estoy de primero')
                                    if(this.noSeHaCancelado) //Si no se ha cancelado
                                        this.ventaRapida(uuid, precioPrevio, cancelValue)
                                    else{
                                        this.statusProfit = 'Apagado'
                                        this.refreshData();
                                    }

                                }

                            }else{
                            //******************************************//
                            // Poner Orden a precio a AskVentaRapida    // 
                            //******************************************//

                                console.log('3. Colocar orden a mejor precio: ', precioMejor)

                                axios.get(`/auth/api/selllimit/${this.market}/${this.balanceCoin}/${precioMejor}/${this.$store.state.timeInForceGlobal}`)
                                .then(response => response.data)
                                .then( data => { 
                                    if(data.sellLimit.message){

                                            miniToastr.success(data.sellLimit.message, "Error")
                                            console.log('Orden ejcutada o cancelada', data.sellLimit.message)
                                            

                                            this.refreshData();

                                            var audio = new Audio(this.sound);
                                            audio.play();

                                        }else{
                                            
                                            //*********************************//
                                            // Coloco orden y recibo su uuid   // 
                                            //*********************************//

                                            this.cancelExitButton = false 


                                            console.log('4. Orden colocada a: ', precioMejor)
                                            
                                            let uuidVentaRapida = data.sellLimit.result.uuid

                                            var audio = new Audio(this.soundOrderOn);
                                            audio.play();

                                            //********************************//
                                            // Rellamo la funcion con la uuid // 
                                            //********************************//

                                            console.log('5. Rellamo la funcion con la uuid: ', uuidVentaRapida)


                                            if(this.noSeHaCancelado) //Si no se ha cancelado
                                                this.ventaRapida(uuidVentaRapida, precioMejor, cancelValue)
                                            else{
                                                this.statusProfit = 'Apagado'
                                                this.refreshData();
                                            }

                                            miniToastr.success('Orden colocada con Exito', "Info")
                                        }
                                }).catch(error => {
                                    console.log('Error de venta rapida 56')
                                });
                            }

                        }).catch(error => {
                            this.$store.commit("setCalling", false) //Apagar la señal de api
                            console.log('Error de getticker 54')
                        });
                }else{
                    this.statusProfit = 'Apagado'
                    this.refreshData();
                }
            },
            getCustomPricePercentege(tasa){
                this.quantityPlusPercentageSW = this.quantityIn + ((tasa * this.quantityIn)/100)
                let priceNeededSW = ((this.quantityPlusPercentageSW * this.priceIn) / this.quantityIn).toFixed(8)
                this.priceSell = priceNeededSW

                this.calculateOrderSell(this.btcBuy, this.priceSell)
                this.setCoinSell()

            },
            getCustomPriceProfitWin(tasa){
                this.quantityPlusPercentageSW = this.quantityIn + (((this.getProfit() + 1) * this.quantityIn)/100)
                this.priceNeededSW = ((this.quantityPlusPercentageSW * this.priceIn) / this.quantityIn).toFixed(8)
                this.priceNeededSWTrailing = this.priceNeededSW
                this.btcToWinSW = ((this.quantityIn * this.priceNeededSW)-(this.quantityIn * this.priceNeededSW)*0.0025).toFixed(8)
            },
            getCustomPriceProfitLoss(tasa, modo){ //0 Manual, 1 Automatico

                let tasaNew = modo ? (this.getProfit() - tasa) : tasa
                this.quantityMinusPercentageSL = this.quantityIn + ((tasaNew * this.quantityIn)/100)
                this.priceNeededSL = ((this.quantityMinusPercentageSL * this.priceIn) / this.quantityIn).toFixed(8)
                this.priceNeededSLTrailing = this.priceNeededSL
                this.btcToLossSL = ((this.quantityIn * this.priceNeededSL)-(this.quantityIn * this.priceNeededSL)*0.0025).toFixed(8)
            },
            getProfit(){
                if(this.priceIn){
                    this.profit = ((this.btcCoinData.Ask -  this.priceIn)/ this.priceIn)*100
                    this.$store.commit("setProfit", this.profit) 
                        return ((this.btcCoinData.Ask -  this.priceIn)/ this.priceIn)*100
                }else
                    return 0
            },
            getPriceToProfit(tasa){
                this.quantityPlusPercentage = this.quantityIn + ((tasa * this.quantityIn)/100)
                this.priceNeeded = (this.quantityPlusPercentage * this.priceIn) / this.quantityIn
                let fee = (this.quantityIn * this.priceNeeded)*0.025
                this.btcToWin = ((this.quantityIn * this.priceNeeded)-(this.quantityIn * this.priceNeeded)*0.0025).toFixed(8)
                    return this.priceNeeded.toFixed(8)
            },
            getPriceToProfitLoss(tasa){
                this.quantityMinusPercentage = this.quantityIn - ((tasa * this.quantityIn)/100)
                this.priceNeededLoss = (this.quantityMinusPercentage * this.priceIn) / this.quantityIn
                this.btcToLoss = ((this.quantityIn * this.priceNeededLoss)-(this.quantityIn * this.priceNeededLoss)*0.0025).toFixed(8)

                    return this.priceNeededLoss.toFixed(8)
            },
            getOpenOrders(){
                axios
                .get(`/auth/api/getopenorders/${this.market}`)
                .then( response => response.data) 
                .then(data => {

                    this.openOrdersSell = data.getOpenOrders.result.filter(order => order.OrderType == 'LIMIT_SELL')
                    this.openOrdersBuy = data.getOpenOrders.result.filter(order => order.OrderType == 'LIMIT_BUY')


                }).catch(error => {
                    this.$store.commit("setCalling", false) //Apagar la señal de api
                    console.log('Error de GetOpenOrders 12')

                });
            },
            sellLimit(){
                if(this.getTotalCoinToSell > 0){ //Si hay datos completos

                    this.disabledExit = true

                    this.$store.commit("setCalling", true) //Encender la señal de api
                    
                    if(!this.salidaDinamica){ //Si la venta es estatica
                        axios
                        .get(`/auth/api/selllimit/${this.market}/${this.$refs.quantityAltcoin.value}/${this.$refs.rateBuy.value}/${this.$store.state.timeInForceGlobal}`)
                            .then(response => response.data)
                            .then( data => { 
                                if(data.sellLimit.message){
                                    miniToastr.error(data.sellLimit.message, "Error",)
                                    this.disabledExit = false
                                }else{
                                    this.cancelExitButton = false

                                    miniToastr.success("Orden colocada con Exito", "Success",)

                                    setTimeout(function () {
                                        this.verifyStrictOrder(data.sellLimit.result.uuid, 'sell')        
                                    }.bind(this), 5000)
                                    
                                }
                                return data 
                            }).catch(error => {
                                this.$store.commit("setCalling", false) //Apagar la señal de api
                                console.log('Error de sellLimit Estatico 13')
                            });
                    }else{
                            console.log('Salida Dinamica')
                            this.ventaRapida(false, 0, 1) //uuid, precioPrevio, disabledExit

                    }
                }else{
                    this.disabledExit = false
                    miniToastr.success("Error de Datos", "Error",)
                }
            },
            buyLimit(){ //Poner orden de Compra
                if(this.getTotalCoinToBuy > 0){ //Si hay datos completos
                
                    this.disabledIn = true //Deshabilitar campos de texto y boton

                    //clearInterval(this.timeRefresh); //detener peticiones de estatus
                    this.$store.commit("setCalling", true) //Encender la señal de api
                    
                    if(!this.entradaDinamica){ //Si la compra es estatica
                        
                        axios
                        .get(`/auth/api/buylimit/${this.market}/${this.$refs.quantityToBuy.value}/${this.$refs.rate.value}/${this.$store.state.timeInForceGlobal}`)
                            .then(response => response.data)
                            .then( data => { 
                                if(data.buyLimit.message){ //Mensaje de Error
                                    miniToastr.error(data.buyLimit.message, "Error",)
                                    console.log('Error 17',data.buyLimit.message)

                                    this.disabledIn = false //Habilito campos de texto y boton de compra
                                    this.cancelInButton = true //Habilito campos de texto y boton de compra


                                }else{
                                    miniToastr.success("Orden colocada con Exito", "Success",)

                                    this.cancelInButton = false //Se habilita boton de cancelar compra

                                    setTimeout(function () {
                                        this.verifyStrictOrder(data.buyLimit.result.uuid, 'buy')     //Se verifica el estado la orden   
                                    }.bind(this), 5000)
                                
                            }
                            return data 
                            }).catch(error => {
                                this.$store.commit("setCalling", false) //Apagar la señal de api
                                console.log('Error de buylimit 17')
                            });
                    }else{

                        console.log('Entrada Dinamica') //La salida es dinamica (Burbuja)

                        this.compraRapida(false, 0) //uuid, precioPrevio, disabledIn
                    }
                }else{
                    this.disabledIn = false
                    miniToastr.success("Error de Datos", "Error",)
                }
            },
            compraRapida(uuid, precioPrevio){

                this.disabledIn = true //Inhabilito campos de texto y boton de compra
                this.cancelInButton = false //Habilito campos de texto y boton de compra


                if(this.disabledIn){ //Si no se ha cancelado

                    //*******************************************************//
                    // Funcion para garantizar vender lo mas rapido posible  // 
                    //*******************************************************//
                    if(!uuid)
                        console.log('0. Inicio Compra Rápida')

                    clearInterval(this.timeRefresh); //detener peticiones a la api

                    //*******************************************************//
                    // Obtengo(Ask) Actual para mejorarlo si es necesario    // 
                    //*******************************************************//
                    
                    axios
                    .get(`/auth/api/getticker/${this.market}`)
                        .then(response => response.data)
                        .then( data => { 
                                                        
                            this.askCompraRapida = data.getTicker.result.Bid

                            console.log('1 Obtengo Bid actual: ', this.askCompraRapida)
                            
                            let precioMejor = this.askCompraRapida + this.$store.state.incrementToBuy // debe ser - state decrement
                            //let precioMejor = this.askCompraRapida - 0.00008123 // debe ser + state increment (PRUEBAS)

                            console.log('2 Mejoro el precio Bid Actual', precioMejor)

                            //*******************************************************//
                            // Si hay una orden colocada verifico que este primera   // 
                            // sino debo cancelar y mejorar la orden                 // 
                            //*******************************************************//

                            if(uuid){ //Si hay una orden
                                console.log('6. Hay uuid: ', uuid)
                                console.log('7. Hay precioPrevio: ', precioPrevio)

                            //******************************************//
                            // Si el Bid actual es mejor que mi orden   // 
                            // debo cancelar y mejorar la orden         // 
                            //******************************************//
                                if(this.askCompraRapida > precioPrevio){
                                //if(this.askCompraRapida ){ //para pruebas
                                    console.log('Cancelo la orden, no es la mejor')
                                    axios
                                    .get(`/auth/api/cancel/${uuid}`)
                                    .then(response => response.data)
                                    .then( data => {          
                                        console.log('8. orden Cancelada')      

                                        //****************************************************//
                                        // Cancelo la orden y rellamo a la funcion sin uuid   // 
                                        //****************************************************// 
                                        if(this.disabledIn) //Si no se ha cancelado
                                            this.compraRapida(null, 0)
                                        else{
                                            this.refreshData();
                                        }

                                        console.log('response orden cancelada', data)               
                                        miniToastr.info("Orden Cancelada", "Success",)

                                        var audio = new Audio(this.soundCancelOrder);
                                        audio.play();

                                    }).catch(error => {
                                        this.$store.commit("setCalling", false) //Apagar la señal de api
                                        console.log('Error de cancelorder 56')
                                    });
                                }else{
                                    console.log('9. Estoy de primero')
                                    if(this.disabledIn) //Si no se ha cancelado
                                        this.compraRapida(uuid, precioPrevio)
                                    else{
                                        this.refreshData();
                                    }

                                }

                            }else{
                            //******************************************//
                            // Poner Orden a precio a AskCompraRapida    // 
                            //******************************************//

                                console.log('3. Colocar orden a mejor precio: ', precioMejor)

                                let withoutFees = this.balanceUsd / precioMejor
                                let getTotalCoinToBuy = (withoutFees - ((withoutFees * 0.25) / 100)).toFixed(8)

                                axios.get(`/auth/api/buylimit/${this.market}/${getTotalCoinToBuy}/${precioMejor}/${this.$store.state.timeInForceGlobal}`)
                                .then(response => response.data)
                                .then( data => { 
                                    if(data.buyLimit.message){

                                            miniToastr.success(data.buyLimit.message, "Error")
                                            console.log('Orden ejcutada o cancelada', data.buyLimit.message)

                                            this.disabledIn = false 
                                            this.cancelInButton = true

                                            this.refreshData();

                                            var audio = new Audio(this.sound);
                                            audio.play();

                                        }else{
                                            
                                            //*********************************//
                                            // Coloco orden y recibo su uuid   // 
                                            //*********************************//

                                            console.log('4. Orden colocada a: ', precioMejor)
                                            
                                            let uuidCompraRapida = data.buyLimit.result.uuid

                                            var audio = new Audio(this.soundOrderOn);
                                            audio.play();

                                            //********************************//
                                            // Rellamo la funcion con la uuid // 
                                            //********************************//

                                            console.log('5. Rellamo la funcion con la uuid: ', uuidCompraRapida)


                                            if(this.disabledIn) //Si no se ha cancelado
                                                this.compraRapida(uuidCompraRapida, precioMejor)
                                            else{
                                                this.refreshData();
                                            }

                                            miniToastr.success('Orden colocada con Exito', "Info")
                                        }
                                }).catch(error => {
                                    console.log('Error de compra rapida 56')
                                });
                            }

                        }).catch(error => {
                            this.$store.commit("setCalling", false) //Apagar la señal de api
                            console.log('Error de getticker 54')
                        });
                }else{
                    this.refreshData();
                }
            },
            verifyStrictOrder(uuid, typeOrder){ //Verificar estado de orden estatica hasta que se ejecuta

                axios
                .get(`/auth/api/getopenorders/${this.market}`)
                .then(response => response.data)
                .then(data => {

                    if(data.getOpenOrders.result.length > 0){ //Si hay ordenes abiertas

                        miniToastr.success("Orden Estática en espera", "Success",)

                        setTimeout(function () {
                             this.verifyStrictOrder(uuid, typeOrder)        
                        }.bind(this), 5000)

                    }else{
                        if(typeOrder == 'buy'){ 
                            this.cancelInButton = true //Deshabilito boton de cancelar entrada
                            this.disabledIn = false    //Habilito boton de entrada
                        }else{
                              this.cancelExitButton = true //Deshabilito boton de cancelar salida
                              this.disabledExit = false //Habilito boton de salida

                            //  this.disabledButtonExit_1 = false //deshabilitar botones de salida automatica
                            //  this.disabledStop = false
                        }

                        var audio = new Audio(this.sound);
                        audio.play();

                        miniToastr.success("Orden Cancelada o Ejecutadaa", "Success",)
                        this.getOrderHistory()
                        this.refreshData();
                        this.$store.commit("setCalling", true) //Encender la señal de api

                     }
                 }).catch(error => {
                    this.$store.commit("setCalling", false) //Apagar la señal de api
                    console.log('Error de Getopenorders 22')
                });
            },
            cancelInManual(){ //Cancelar Entrada (Compra) Manual (Dinamica o Estricta)

                        this.disabledIn = false 
                        this.cancelInButton = true
                axios
                .get(`/auth/api/getopenorders/${this.market}`)
                .then(response => response.data)
                .then(data => {

                    if(data.getOpenOrders.result[0].OrderType == 'LIMIT_BUY'){
                        console.log('CANCELAR ORDEN: ', data.getOpenOrders.result[0].OrderUuid)
                        
                        this.cancelOrder(data.getOpenOrders.result[0].OrderUuid)

                        this.refreshData();
                    }
                }).catch(error => {
                    this.$store.commit("setCalling", false) //Apagar la señal de api
                    console.log('Error de getopenorders 23')
                });
            },
            cancelExitManual(){ //Cancelar Entrada (Compra) Manual (Dinamica o Estricta)
                axios
                .get(`/auth/api/getopenorders/${this.market}`)
                .then(response => response.data)
                .then(data => {

                    if(data.getOpenOrders.result[0].OrderType == 'LIMIT_SELL'){

                        this.cancelOrder(data.getOpenOrders.result[0].OrderUuid)

                        console.log('CANCELAR ORDEN: ', data.getOpenOrders.result[0].OrderUuid)
                        this.disabledExit = false
                        this.cancelExitButton = true

                        this.disabledStop = false //habilitar boton y campos de salida automatica
                        this.disabledButtonExit_1 = false
                            
                        this.refreshData();
                    }
                }).catch(error => {
                    this.$store.commit("setCalling", false) //Apagar la señal de api
                    console.log('Error de getopenorders 24')
                });
            },
            cancelExitAutomatic(){

                this.trailingAutomaticOn = false
                this.priceNeededSLTrailing = 0
                axios
                .get(`/auth/api/getopenorders/${this.market}`)
                .then(response => response.data)
                .then(data => {
                    if(data.getOpenOrders.result.length)
                        if(data.getOpenOrders.result[0].OrderType == 'LIMIT_SELL'){
                        console.log('CANCELAR ORDEN: ',data.getOpenOrders.result[0].OrderUuid)

                            this.cancelOrder(data.getOpenOrders.result[0].OrderUuid)
                            this.disabledExit = false
                        }
                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de Getopenorders 25')

                });

                this.disabledButtonExit_1 = false
                this.disabledButtonExit_2 = true
                this.disabledStop = false
                this.disabledExit = false
                this.stopWin = ''
                this.stopLoss = ''
                this.statusProfit = 'Apagado',
                this.classExit = 'btn btn-primary'

            },
            cancelOrder(uuid){
                axios
                .get(`/auth/api/cancel/${uuid}`)
                .then(response => {
                    miniToastr.info("Orden Cancelada", "Success",)
                }).catch(error => {
                    this.$store.commit("setCalling", false) //Apagar la señal de api
                    console.log('Error de cancelorder 26')
                });
            },
            getMarkets(){ //Trae los datos de las monedas

                axios
                .get('/auth/api/getmarkets')
                .then( response => response.data) 
                .then(data => {
                    //Datos de la Coin no base
                    this.dataCoin = data.getMarkets.result.filter(item => item.MarketCurrency == this.getNameCoin && item.BaseCurrency == 'USD')[0]
                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de Getmarkets 27')
                });
            },
            getBalance(){ //Trae el balance de todas las monedas
                axios
                    .get('/auth/api/getbalances')
                    .then( response => response.data) 
                    .then(data => {
            
                        this.balances = data.getBalances.result
                        //Saldo moneda base
                        let datosUSD = data.getBalances.result.filter(item => item.Currency == this.dataCoin.BaseCurrency)

                        if(datosUSD.length == 0)
                            this.balanceUsd = 0
                        else
                            this.balanceUsd = datosUSD[0].Available



                        //Saldo moneda no base
                        data.getBalances.result.filter(item => {
                            if(item.Currency == this.getNameCoin){
                                this.balanceCoin = item.Available
                            }//else Revisar
                               // this.balanceCoin = 0
                        })


                    })/*.catch(error => {
                        this.$store.commit("setCalling", false)
                        console.log('Error de Getbalances 28')

                    });*/
            },
            getOrderHistory(){ //Traer historico de Ordenes
                axios
                .get(`/auth/api/getorderhistory/${this.market}`)
                .then(response => response.data)
                .then( data => { 

                    this.historicOrders = Object.values(data.getOrderHistory.result)

                    if(data.getOrderHistory.result.length > 0)
                        if(data.getOrderHistory.result[0].OrderType == 'LIMIT_BUY'){
                            this.priceIn = data.getOrderHistory.result[0].Limit
                            this.quantityIn = data.getOrderHistory.result[0].Quantity
                        }else{
                            this.priceIn = 0
                            this.quantityIn = 0
                        }
                        return data 
                }).catch(error => {
                    this.$store.commit("setCalling", false)
                    console.log('Error de Getorderhistory 29')

                });
            },
            setPriceBuy(){  
                this.priceBuy = this.btcCoinData.Bid; //Set el precio al campo de texto
                this.calculateOrderBuy(this.btcSell, this.priceBuy)
            },
            setBtcSell(){ //Set la cantidad de btc al campo de texto
                this.btcSell = this.balanceUsd;
                this.calculateOrderBuy(this.btcSell, this.priceBuy)
            },
            calculateOrderBuy(quantity, rate){ //Calcular orden de compra con fees
                let withoutFees = quantity / rate
                this.getTotalCoinToBuy = (withoutFees - ((withoutFees * 0.25) / 100)).toFixed(8)
            },
            setPriceSell(){ //sete de precio a vender al campo de texto
                this.priceSell = this.btcCoinData.Ask;
                this.calculateOrderSell(this.btcBuy, this.priceSell)
            },
            setCoinSell(){ //Set de cantidad de monedas a vender al campo de texto
                this.btcBuy = this.balanceCoin;
                this.calculateOrderSell(this.btcBuy, this.priceSell)
            },
            calculateOrderSell(quantity, rate){
                let withoutFees = quantity * rate
                this.getTotalCoinToSell = (withoutFees - ((withoutFees * 0.25) / 100)).toFixed(8) //btc a obtener
            },
            toFix(value, decimals){ //Colocar Decimales
                if(value && value !=0)
                    return value.toFixed(decimals)
                else
                    return 0
            },
            timeClose(time){ //Convierte la fecha a legible
                let times = moment(new Date(moment(time).subtract(3, 'hour')));
                let now = moment(new Date());
                    return times.from(now);
            },
            getValueToggleIn(){ //Capturar valor del toggle Compra
                this.entradaDinamica = !this.entradaDinamica
            },
            showTrailingOptions(){ //Capturar valor del toggle Compra
                this.showTrailingFields = !this.showTrailingFields
            },
            getValueToggleExit(){ //Capturar valor del toggle Venta
                this.salidaDinamica = !this.salidaDinamica
            },
            calculateHistoricProfit(compra, venta){
                let profit = ((venta -  compra)/ compra)*100
                //this.profitHistoric = profit
                    return profit.toFixed(2)
            },
            setIncremenToBuyValue(){
                this.$store.commit("setIncrementToBuy", this.$refs.incrementToBuy.value)
            },
            setIncremenToBuyValueDefault(){
                this.$store.commit("setIncrementToBuy", this.toFix(0.0000001, 7))
                this.incrementToBuy = this.toFix(0.0000001, 7)
            },
            setDecremenToSellValue(){
                this.$store.commit("setDecrementToSell", this.$refs.decrementToSell.value)
            },
            setDecrementToSellValueDefault(){
                this.$store.commit("setDecrementToSell", this.toFix(0.0000001, 7))
                this.decrementToSell = this.toFix(0.0000001, 7)
            },
            setStopLossGlobalValue(){
                this.$store.commit("setStopLossGlobal", this.$refs.stopLossGlobal.value)
            },
            setStopLossGlobalDefault(){
                this.$store.commit("setStopLossGlobal", 5)
                this.stopLossGlobal = 5
            },
            setTimeInForceGlobal(){

                this.timeInForceToggle = !this.timeInForceToggle
                if(this.timeInForceToggle)
                    this.$store.commit("setTimeInForceGlobal", 'GTC')
                else
                    this.$store.commit("setTimeInForceGlobal", 'IOC')
                    
            },
            suma(a, b){
                return (parseFloat(a) + b).toFixed(4)
            },
            getCustomPercentage(value){
                this.customPercentage = value
            },
            setPasswords(){

                let password_1
                let password_2

                !this.password_1 ? password_1 = 0 : password_1 = this.password_1

                !this.password_2 ? password_2 = 0 : password_2 = this.password_2
                    
                axios
                .get(`/auth/setpasswords/${password_1}/${password_2}`)
                .then(response => response.data)
                .then( data => { 

                    miniToastr.info(data.msg, "Success",)
                    this.password_1 = ''
                    this.password_2 = ''
                    
                }).catch(error => {
                    console.log('Error de setPasswords 40')

                });
            },
            clearPasswords(){
                this.password_1 = ''
                this.password_2 = ''
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
            },
            refreshData() { //Rellamadas a la api
                this.timeRefresh = setInterval(() => {    
                    this.getMarketSummary();
                }, 5000);    
            }
        },
        computed: {
            getNameCoin: function () {    //Nombre de la moneda no base
                return this.market.split('-')[1] 
            }
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
            this.market = this.$route.params.MarketName
            this.headerTitle = 'Trading Area - [' + this.market + ']'
        },
        destroyed () {
            clearInterval(this.interval)
            this.$store.commit("setCalling", false)
        },
        beforeDestroy() {
            clearInterval(this.timeRefresh);
            this.$store.commit("setCalling", false)
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
