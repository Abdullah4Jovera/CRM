<style>
    .hidden {
      display: none;
    }
</style>

<h5 class="text-capitalize text-primary d-flex justify-content-center">Real Estate Service Info</h5>
<div class="col-6 form-group">
    <select class="form-control select" name="locationChoice" id="locationChoice">
        <option selected disabled>Location Choice</option>
        <option {{($data->locationChoice == 'Abu Dhabi')?'selected':''}}  value="Abu Dhabi">Abu Dhabi</option>
        <option {{($data->locationChoice == 'Dubai')?'selected':''}}  value="Dubai">Dubai</option>
        <option {{($data->locationChoice == 'Sharjah')?'selected':''}}  value="Sharjah">Sharjah</option>
        <option {{($data->locationChoice == 'Ajman')?'selected':''}}  value="Ajman">Ajman</option>
        <option {{($data->locationChoice == 'Umm Al-Quwain')?'selected':''}}  value="Umm Al-Quwain">Umm Al-Quwain</option>
        <option {{($data->locationChoice == 'Fujairah')?'selected':''}}  value="Fujairah">Fujairah</option>
        <option {{($data->locationChoice == 'Ras Al Khaimah')?'selected':''}}  value="Ras Al Khaimah">Ras Al Khaimah</option>
    </select>

</div>


<div class="col-6 form-group">
    <select class="form-control select" name="propertyPurpose" id="propertyPurpose" onchange="updatePropertyType()">
      <option selected disabled>Property Purpose</option>
      <option {{($data->propertyPurpose == 'buy')?'selected':''}} value="buy">Buy</option>
      <option {{($data->propertyPurpose == 'sale')?'selected':''}} value="sale">Sale</option>
    </select>
</div>

<div id="buySection" class="hidden row">
    <div class="col-6-6 form-group">
        <select class="form-control select" name="propertyType" id="propertyType">
          <option selected disabled >Property Type </option>
          <option {{($data->propertyType == 'offPlan')?'selected':''}} value="offPlan">Off Plan</option>
          <option {{($data->propertyType == 'readyToMove')?'selected':''}} value="readyToMove">Ready to Move</option>
        </select>
    </div>
    <div class="col-6-6 form-group">
        <select class="form-control select" name="priceRange" id="priceRange">
            <option selected disabled >Price Range </option>
          <option {{($data->priceRange == '100000-200000')?'selected':''}} value="100000-200000">$100,000 - $200,000</option>
          <option {{($data->priceRange == '200000-300000')?'selected':''}} value="200000-300000">$200,000 - $300,000</option>
          <option {{($data->priceRange == '300000-Above')?'selected':''}} value="300000-Above">300000-Above</option>
        </select>
    </div>
</div>

<div id="saleSection" class="hidden row">
    <div class="col-6-6 form-group">
        <select class="form-control select" name="propertyTypeSale" id="propertyTypeSale">
          <option selected disabled >Property Type </option>
            <option {{($data->propertyTypeSale == 'Apartment')?'selected':''}} value="Apartment">Apartment</option>
            <option {{($data->propertyTypeSale == 'Townhouse')?'selected':''}} value="Townhouse">Townhouse</option>
            <option {{($data->propertyTypeSale == 'Villa')?'selected':''}} value="Villa">Villa</option>
        </select>
    </div>
    <div class="col-6-6 form-group">
        <select class="form-control select" name="bedrooms" id="bedrooms">
            <option selected disabled >Bedrooms </option>
            <option {{($data->bedrooms == 'Studio')?'selected':''}} value="Studio">Studio</option>
            <option {{($data->bedrooms == '1 Bedroom')?'selected':''}} value="1 Bedroom">1 Bedroom</option>
            <option {{($data->bedrooms == '2 Bedrooms')?'selected':''}} value="2 Bedrooms">2 Bedrooms</option>
            <option {{($data->bedrooms == '3 Bedrooms')?'selected':''}} value="3 Bedrooms">3 Bedrooms</option>
            <option {{($data->bedrooms == '4 Bedrooms')?'selected':''}} value="4 Bedrooms">4 Bedrooms</option>
            <option {{($data->bedrooms == '5 Bedrooms')?'selected':''}} value="5 Bedrooms">5 Bedrooms</option>
        </select>
    </div>
</div>

<div class="col-12 form-group">
    {{ Form::textarea('notes',$data->notes , array('placeholder'=>'Message','maxlength'=>'200' ,'class' => 'form-control' ,'id'=>'notes', 'rows'=>"3")) }}
</div>

<script>
  function updatePropertyType() {
    var propertyPurpose = document.getElementById("propertyPurpose").value;
    var buySection = document.getElementById("buySection");
    var saleSection = document.getElementById("saleSection");

    buySection.style.display = (propertyPurpose === "buy") ? "block" : "none";
    saleSection.style.display = (propertyPurpose === "sale") ? "block" : "none";
  }
  $(function () {
    var propertyPurpose = document.getElementById("propertyPurpose").value;
    var buySection = document.getElementById("buySection");
    var saleSection = document.getElementById("saleSection");

    buySection.style.display = (propertyPurpose === "buy") ? "block" : "none";
    saleSection.style.display = (propertyPurpose === "sale") ? "block" : "none";
  });
</script>
