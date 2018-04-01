<div class="btn-group" data-toggle="buttons">
    @foreach($options as $option => $label)
        <label class="btn btn-default btn-sm {{ \Request::get('catalog', 'all') == $option ? 'active' : '' }}">
            <input type="radio" class="transfer-catalog" value="{{ $option }}">{{$label}}
        </label>
    @endforeach
</div>