<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Invoice</title>
        <style type="text/css">
            *{
                font-family: 'DejaVu Sans', sans-serif;
                @if($size == 'A4')
                font-size: 14px;
                @elseif($size == 'A5')
                font-size: 11px;
                @elseif($size == 'A6')
                font-size: 7px;
                @endif
            }
            html{
                height: 100vh;
                margin: 5px;
            }
            .contentToConvert {
                position: relative;
                top: 0;
                left: 0;
                z-index: -999999;
                background: white;
                width: 100%;
                height: 100vh;
            }
            .codebar-container{
                text-align: right;
            }
            .codebar-container .barcode-wilaya{
                @if($size == 'A4')
                font-size: 24px;
                @elseif($size == 'A5')
                font-size: 18px;
                @elseif($size == 'A6')
                font-size: 12px;
                @endif
                font-weight: bold;
            }
            .w-100{
                width: 100%;
            }
            .w-50{
                width: 50%;
            }
            .p5{
                padding: 5px;
            }
            .lined{
                border-bottom: 1.5px solid #000;
                padding-bottom: 5px;
            }
            hr{
                height: 0px;
                border-bottom: 1px solid #000;
                margin: 0;
            }
            .pb-2{
                padding-bottom: 5px !important;
            }
            .pb-0{
                padding-bottom: 0px;
            }
            .text-end{
                text-align: right;
            }
            .total{
            float: right;
            margin-top: 4rem;
            }
        </style>
    </head>
    <body>
        <div class="contentToConvert" style="width: 100%">
            @foreach($orders as $order)
                @if($order->id === $orders[0]->id)
                <table class ="w-100">
                    @if ($order->delivredBy !== 'placetta')
                        <tr>
                            <td>
                                @if($size == 'A4')
                                <img  src="{{$placettaLogo}}" alt="" width="80px">
                                <img src="{{$maystroLogo}}" alt="" width="100px">
                                @elseif($size == 'A5')
                                <img  src="{{$placettaLogo}}" alt="" width="60px">
                                <img src="{{$maystroLogo}}" alt="" width="75px">
                                @elseif($size == 'A6')
                                <img  src="{{$placettaLogo}}" alt="" width="40px">
                                <img src="{{$maystroLogo}}" alt="" width="50px">
                                @endif
                            </td>
                            <td>
                                <div class="codebar-container">
                                    @if($size == 'A4')
                                    <img id="IDBarcode" src="{{$barcodes[$order->id]}}" alt="" style="width: 200px;">
                                    @elseif($size == 'A5')
                                    <img id="IDBarcode" src="{{$barcodes[$order->id]}}" alt="" style="width: 150px;">
                                    @elseif($size == 'A6')
                                    <img id="IDBarcode" src="{{$barcodes[$order->id]}}" alt="" style="width: 100px;">
                                    @endif
                                    <div class="barcode-wilaya">
                                    {{$order->wilaya}}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td>
                            @if($size == 'A4')
                            <img src="{{$placettaLogo}}" alt="" width="80px">
                            @elseif($size == 'A5')
                            <img src="{{$placettaLogo}}" alt="" width="60px">
                            @elseif($size == 'A6')
                            <img src="{{$placettaLogo}}" alt="" width="40px">
                            @endif
                            </td>
                            <td>
                                <div class="codebar-container">
                                    @if($size == 'A4')
                                    <img id="IDBarcode" src="{{$barcodes[$order->id]}}" alt=""
                                    style="width: 200px;">
                                    @elseif($size == 'A5')
                                    <img id="IDBarcode" src="{{$barcodes[$order->id]}}" alt=""
                                    style="width: 150px;">
                                    @elseif($size == 'A6')
                                    <img id="IDBarcode" src="{{$barcodes[$order->id]}}" alt=""
                                    style="width: 100px;">
                                    @endif
                                    <div class="barcode-wilaya">
                                    {{$order->wilaya}}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                </table>
                <br>
                <table class="p5 w-100">
                    <tr>
                        <td class="lined" colspan="2">
                            ID : {{$order->maystroId? $order->maystroId : 
                            ($order->displayId? $order->displayId : $order->id)}}
                        </td>
                    </tr>
                    <tr>
                        <td class="pb-5">
                        CLIENT
                        </td>
                        <td class="pb-5">
                            EXPEDITEUR
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>Nom: </span>
                            
                            <span style="direction: rtl; ">{{$order->userName}}</span>
                        </td>
                        <td>
                            <span>Nom: </span>
                            
                            <span style="direction: rtl; ">{{$order->store && $order->store->title? $order->store->title : ''}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Telephone: {{$order->customerPhone}}
                        </td>
                        <td>
                            Telephone: {{$PhoneToShow? $PhoneToShow : ''}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>
                                Addresse: 
                            </span>
                         
                            <span style="direction: rtl; ">
                            {{$order->deliveryAddress}}
                            </span>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Commune: {{$communes[$order->id]}}
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Wilaya: {{$order->wilaya}}
                        </td>
                        <td>
                        </td>
                    </tr>
                </table>
                <br>
                <table class="p5 w-100">
                    <tr>
                        <td class="pb-0">
                            ARTICLE
                        </td>
                        <td class="pb-0">
                            PRIX
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    @php($products = json_decode($order->products))
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <span>{{$product->orderQuantity}}x</span>
                                    <span style="direction: rtl;">{{$product->name}}</span>
                                    <br>
                                    @if(isset($product->cardProperties) && !empty($product->cardProperties))
                                        <p style="margin-bottom: 0;margin-top: 5px;">
                                            @foreach($product->cardProperties as $property)
                                                @foreach($property->values as $propertyValue)
                                                    @if(isset($propertyValue->subProperties) && !empty($propertyValue->subProperties))
                                                        @php($subProperties = (array) $propertyValue->subProperties)
                                                        @foreach(array_keys($subProperties) as $subPropertyName)
                                                            @if(($subProperties[$subPropertyName]->values))
                                                                @foreach($subProperties[$subPropertyName]->values as $subPropertyValue)                 
                                                                    <p style="margin: 1px 0;
                                                                        padding-left: 5rem !important;
                                                                        direction: ltr;">
                                                                            {{$property->name}} : {{$propertyValue->value}}
                                                                            {{$subPropertyName}} : {{$subPropertyValue->value}}
                                                                            ({{$subPropertyValue->quantity}}
                                                                            {{$subPropertyValue->quantity == 1?
                                                                            'Item' : 'Items'}})                                          
                                                                    </p>
                                                                @endforeach
                                                            @endif                                              
                                                        @endforeach
                                                    @else
                                                        <p style="margin: 1px 0;
                                                            padding-left: 5rem !important;
                                                            direction: ltr;">
                                                                {{$property->name}} : {{$propertyValue->value}}
                                                                ({{$propertyValue->quantity}}  {{$propertyValue->quantity == 1?
                                                                'Item' : 'Items'}})
                                                        </p>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </p>
                                    @endif
                                </td>
                                <td>
                                    {{$product->orderPrice}} DZD
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr style="border-bottom: unset;height: unset;">
                                </td>
                            </tr>
                        @endforeach       
                </table>
                <table class="p5 w-50 total text-end">
                    <tr>
                        <td>
                            PRIX
                        </td>
                        <td>
                            {{$order->totalPrice}} DZD
                        </td>
                    </tr>
                    <tr>
                        <td>
                            LIVRAISON
                        </td>
                        <td>
                            {{$order->deliveryPrice ? $order->deliveryPrice : 0 }} DZD
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            TOTAL
                        </td>
                        <td>
                            {{$order->deliveryPrice ? 
                            $order->totalPrice + $order->deliveryPrice : $order->totalPrice }} DZD
                        </td>
                    </tr>
                </table>
                @else
                <table class ="w-100" style="page-break-before: always;">
                    @if ($order->delivredBy !== 'placetta')
                        <tr>
                            <td>
                                @if($size == 'A4')
                                <img  src="{{$placettaLogo}}" alt="" width="80px">
                                <img src="{{$maystroLogo}}" alt="" width="100px">
                                @elseif($size == 'A5')
                                <img  src="{{$placettaLogo}}" alt="" width="60px">
                                <img src="{{$maystroLogo}}" alt="" width="75px">
                                @elseif($size == 'A6')
                                <img  src="{{$placettaLogo}}" alt="" width="40px">
                                <img src="{{$maystroLogo}}" alt="" width="50px">
                                @endif
                            </td>
                            <td>
                                <div class="codebar-container">
                                    @if($size == 'A4')
                                    <img id="IDBarcode" src="{{$barcodes[$order->id]}}" alt="" style="width: 200px;">
                                    @elseif($size == 'A5')
                                    <img id="IDBarcode" src="{{$barcodes[$order->id]}}" alt="" style="width: 150px;">
                                    @elseif($size == 'A6')
                                    <img id="IDBarcode" src="{{$barcodes[$order->id]}}" alt="" style="width: 100px;">
                                    @endif
                                    <div class="barcode-wilaya">
                                    {{$order->wilaya}}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td>
                            @if($size == 'A4')
                            <img src="{{$placettaLogo}}" alt="" width="80px">
                            @elseif($size == 'A5')
                            <img src="{{$placettaLogo}}" alt="" width="60px">
                            @elseif($size == 'A6')
                            <img src="{{$placettaLogo}}" alt="" width="40px">
                            @endif
                            </td>
                            <td>
                                <div class="codebar-container">
                                    @if($size == 'A4')
                                    <img id="IDBarcode" src="{{$barcodes[$order->id]}}" alt=""
                                    style="width: 200px;">
                                    @elseif($size == 'A5')
                                    <img id="IDBarcode" src="{{$barcodes[$order->id]}}" alt=""
                                    style="width: 150px;">
                                    @elseif($size == 'A6')
                                    <img id="IDBarcode" src="{{$barcodes[$order->id]}}" alt=""
                                    style="width: 100px;">
                                    @endif
                                    <div class="barcode-wilaya">
                                    {{$order->wilaya}}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                </table>
                <br>
                <table class="p5 w-100">
                    <tr>
                        <td class="lined" colspan="2">
                            ID : {{$order->maystroId? $order->maystroId : 
                            ($order->displayId? $order->displayId : $order->id)}}
                        </td>
                    </tr>
                    <tr>
                        <td class="pb-5">
                        CLIENT
                        </td>
                        <td class="pb-5">
                            EXPEDITEUR
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>Nom: </span>
                        
                            <span style="direction: rtl; ">{{$order->userName}}</span>
                        </td>
                        <td>
                            <span>Nom: </span>
                         
                            <span style="direction: rtl; ">{{$order->store && $order->store->title? $order->store->title : ''}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Telephone: {{$order->customerPhone}}
                        </td>
                        <td>
                            Telephone: {{$PhoneToShow? $PhoneToShow : ''}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>
                                Addresse: 
                            </span>
                          
                            <span style="direction: rtl; ">
                            {{$order->deliveryAddress}}
                            </span>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Commune: {{$communes[$order->id]}}
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Wilaya: {{$order->wilaya}}
                        </td>
                        <td>
                        </td>
                    </tr>
                </table>
                <br>
                <table class="p5 w-100">
                    <tr>
                        <td class="pb-0">
                            ARTICLE
                        </td>
                        <td class="pb-0">
                            PRIX
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    @php($products = json_decode($order->products))
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <span>{{$product->orderQuantity}}x</span>
                                       <span style="direction: rtl ;">{{$product->name}}</span>
                                    <br>
                                    @if(isset($product->cardProperties) && !empty($product->cardProperties))
                                        <p style="margin-bottom: 0;margin-top: 5px;">
                                            @foreach($product->cardProperties as $property)
                                                @foreach($property->values as $propertyValue)
                                                    @if(isset($propertyValue->subProperties) && !empty($propertyValue->subProperties))
                                                        @php($subProperties = (array) $propertyValue->subProperties)
                                                        @foreach(array_keys($subProperties) as $subPropertyName)
                                                            @if(($subProperties[$subPropertyName]->values))
                                                                @foreach($subProperties[$subPropertyName]->values as $subPropertyValue)                 
                                                                    <p style="margin: 1px 0;
                                                                        padding-left: 5rem !important;
                                                                        direction: ltr;">
                                                                            {{$property->name}} : {{$propertyValue->value}}
                                                                            {{$subPropertyName}} : {{$subPropertyValue->value}}
                                                                            ({{$subPropertyValue->quantity}}
                                                                            {{$subPropertyValue->quantity == 1?
                                                                            'Item' : 'Items'}})                                          
                                                                    </p>
                                                                @endforeach
                                                            @endif                                              
                                                        @endforeach
                                                    @else
                                                        <p style="margin: 1px 0;
                                                            padding-left: 5rem !important;
                                                            direction: ltr;">
                                                                {{$property->name}} : {{$propertyValue->value}}
                                                                ({{$propertyValue->quantity}}  {{$subPropertyValue->quantity == 1?
                                                                'Item' : 'Items'}})
                                                        </p>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </p>
                                    @endif
                                </td>
                                <td>
                                    {{$product->orderPrice}} DZD
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr style="border-bottom: unset;height: unset;">
                                </td>
                            </tr>
                        @endforeach       
                </table>
                <table class="p5 w-50 total text-end">
                    <tr>
                        <td>
                            PRIX
                        </td>
                        <td>
                            {{$order->totalPrice}} DZD
                        </td>
                    </tr>
                    <tr>
                        <td>
                            LIVRAISON
                        </td>
                        <td>
                            {{$order->deliveryPrice ? $order->deliveryPrice : 0 }} DZD
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            TOTAL
                        </td>
                        <td>
                            {{$order->deliveryPrice ? 
                            $order->totalPrice + $order->deliveryPrice : $order->totalPrice }} DZD
                        </td>
                    </tr>
                </table>
                @endif
            @endforeach
        </div>
    </body>
</html>
