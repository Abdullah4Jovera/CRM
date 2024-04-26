<style>
    .hidden {
      display: none;
    }
</style>

<h5 class="text-capitalize text-primary d-flex justify-content-center">Real Estate Service Info</h5>
<div class="col-6 form-group">
    <select class="form-control select" name="locationChoice" id="locationChoice" required>
        <option selected disabled>Location Choice</option>
        <option value="Abu Dhabi">Abu Dhabi</option>
        <option value="Dubai">Dubai</option>
        <option value="Sharjah">Sharjah</option>
        <option value="Ajman">Ajman</option>
        <option value="Umm Al-Quwain">Umm Al-Quwain</option>
        <option value="Fujairah">Fujairah</option>
        <option value="Ras Al Khaimah">Ras Al Khaimah</option>
    </select>

</div>


<div class="col-6 form-group">
    <select class="form-control select" name="propertyPurpose" id="propertyPurpose" onchange="updatePropertyType()">
      <option selected disabled>Property Purpose</option>
      <option value="buy">Buy</option>
      <option value="sale">Sale</option>
    </select>
</div>

<div id="buySection" class="hidden row">
    <div class="col-6-6 form-group">
        <select class="form-control select" name="propertyType" id="propertyType">
          <option selected disabled >Property Type </option>
          <option value="offPlan">Off Plan</option>
          <option value="readyToMove">Ready to Move</option>
        </select>
    </div>
    <div class="col-6-6 form-group">
        <select class="form-control select" name="priceRange" id="priceRange">
            <option selected disabled >Price Range </option>
          <option value="100000-200000">$100,000 - $200,000</option>
          <option value="200000-300000">$200,000 - $300,000</option>
          <option value="300000-Above">300000-Above</option>
        </select>
    </div>
</div>

<div id="saleSection" class="hidden row">
    <div class="col-6-6 form-group">
        <select class="form-control select" name="propertyTypeSale" id="propertyTypeSale">
          <option selected disabled >Property Type </option>
            <option value="Apartment">Apartment</option>
            <option value="Townhouse">Townhouse</option>
            <option value="Villa">Villa</option>
        </select>
    </div>
    <div class="col-6-6 form-group">
        <select class="form-control select" name="bedrooms" id="bedrooms">
            <option selected disabled >Bedrooms </option>
            <option value="Studio">Studio</option>
            <option value="1 Bedroom">1 Bedroom</option>
            <option value="2 Bedrooms">2 Bedrooms</option>
            <option value="3 Bedrooms">3 Bedrooms</option>
            <option value="4 Bedrooms">4 Bedrooms</option>
            <option value="5 Bedrooms">5 Bedrooms</option>
        </select>
    </div>
</div>

<div class="col-12 form-group">
    {{ Form::textarea('notes',null , array('placeholder'=>'Message','maxlength'=>'200' ,'class' => 'form-control' ,'id'=>'notes', 'rows'=>"3")) }}
</div>

<script>
  function updatePropertyType() {
    var propertyPurpose = document.getElementById("propertyPurpose").value;
    var buySection = document.getElementById("buySection");
    var saleSection = document.getElementById("saleSection");

    buySection.style.display = (propertyPurpose === "buy") ? "block" : "none";
    saleSection.style.display = (propertyPurpose === "sale") ? "block" : "none";
  }
</script>
