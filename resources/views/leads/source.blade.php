<div class="form-group sources">
    <select class="form-control select indexPage choices-multiple1 searchData" name="sources" id="choices-multiple25">
        <option selected disabled >Select Source</option>
        @foreach ($sources as $source)
            <option value="{{$source->id}}">{{$source->name}}</option>
            
        @endforeach
    </select>
    
</div>
