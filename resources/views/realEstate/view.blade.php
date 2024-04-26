<style>
    .hidden {
        display: none;
    }
</style>
<div id="service_info" class="card">
    <div class="card-body">
        <h4 class="mb-4 mt-2 text-capitalize text-primary d-flex justify-content-center">{{!empty($products->name)?$products->name:''}}</h4>
        <div class="row">
            <div class="col-md-3 col-sm-3">
                <div class="d-flex align-items-start">
                    <div class="theme-avtar bg-primary">
                        <i class="ti ti-building-bank"></i>
                    </div>
                    <div class="ms-2">
                        <p class="text-muted text-sm mb-0">{{__('Property Purpose')}}</p>
                        <h5 class="mb-0 text-primary">{{!empty($realEstate->propertyPurpose)?$realEstate->propertyPurpose:''}}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-3">
                <div class="d-flex align-items-start">
                    <div class="theme-avtar bg-warning">
                        <i class="ti ti-location"></i>
                    </div>
                    <div class="ms-2">
                        <p class="text-muted text-sm mb-0">{{__('Property Location')}}</p>
                        <h5 class="mb-0 text-warning text-capitalize">{{!empty($realEstate->locationChoice)?str_replace('_',' ',$realEstate->locationChoice).' ':''}}</h5>

                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 hidden" id="viewBuySection">
                <div class="d-flex align-items-start">
                    <div class="theme-avtar bg-info">
                        <i class="ti ti-cash"></i>
                    </div>
                    <div class="ms-2">
                        <p class="text-muted text-sm mb-0">{{__('Property Type')}}</p>
                        <h5 class="mb-0 text-info">{{!empty($realEstate->propertyType )?$realEstate->propertyType :''}}</h5>

                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 hidden" id="viewSaleSection">
                <div class="d-flex align-items-start">
                    <div class="theme-avtar bg-info">
                        <i class="ti ti-coin"></i>
                    </div>
                    <div class="ms-2">
                        <p class="text-muted text-sm mb-0">{{__('Property Type Sale')}}</p>
                        <h5 class="mb-0 text-info">{{ !empty($realEstate->propertyTypeSale) ? $realEstate->propertyTypeSale : 'No' }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 ">
                <div class="d-flex align-items-start">
                        <div class="theme-avtar bg-danger">
                            <i class="ti ti-affiliate"></i>
                        </div>
                        <div class="ms-2">
                            <p class="text-muted text-sm mb-0">{{__('Created')}}</p>
                            <h5 class="mb-0 text-danger">{{\Auth::user()->dateFormat($lead->date)}}</h5>
                        </div>
                    </div>
            </div>
            <div class="col-md-3 col-sm-3 mt-4">
                <div class="d-flex align-items-start">
                    <div class="theme-avtar bg-primary">
                        <i class="ti ti-server"></i>
                    </div>
                    <div class="ms-2">
                        <p class="text-muted text-sm mb-0">{{__('Pipeline')}}</p>
                        <h5 class="mb-0 text-primary">{{!empty($lead->pipeline->name)?$lead->pipeline->name:''}}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 mt-4">
                <div class="d-flex align-items-start">
                    <div class="theme-avtar bg-warning">
                        <i class="ti ti-calendar"></i>
                    </div>
                    <div class="ms-2">
                        <p class="text-muted text-sm mb-0">{{__('Stage')}}</p>
                        @if (!empty($lead->is_reject))
                            <h5 class="mb-0 text-warning">{{('Rejected')}}</h5>
                        @else
                            <h5 class="mb-0 text-warning">{{$lead->stage->name}}</h5>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 mt-4">
                <div class="d-flex align-items-start">
                    <div class="theme-avtar bg-info">
                        <i class="ti ti-file"></i>
                    </div>
                    <div class="ms-2">
                        <p class="text-muted text-sm mb-0">{{__('Source')}}</p>
                        <h5 class="mb-0 text-info">{{optional($sources)->name}}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 mt-4">
                <div class="d-flex align-items-start">
                    <div class="theme-avtar bg-danger">
                        <i class="ti ti-social"></i>
                    </div>
                    <div class="ms-2">
                        <p class="text-muted text-sm mb-0">{{__('Lead From')}}</p>
                        <h5 class="mb-0 text-danger">{{optional($lead->leadType)->name}}</h5>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var propertyPurpose = {!! json_encode($realEstate->propertyPurpose) !!};
    console.log(propertyPurpose);
    var viewBuySection = document.getElementById("viewBuySection");
    var viewSaleSection = document.getElementById("viewSaleSection");

    // Add or remove the 'hidden' class based on the value of propertyPurpose
    if (propertyPurpose === "buy") {
        viewBuySection.classList.remove("hidden");
        viewSaleSection.classList.add("hidden");
    } else if (propertyPurpose === "sale") {
        viewBuySection.classList.add("hidden");
        viewSaleSection.classList.remove("hidden");
    }
});
</script>
