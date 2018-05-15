<div class="btn-group" data-toggle="buttons">
    <label class="btn btn-secondary">新建:</label>
    @foreach($options as $option => $label)
        <label class="btn btn-default btn-sm ">
            <input type="radio" class="transfer-catalog" value="{{ $option }}">{{$label}}
        </label>
    @endforeach
</div>