<div class="btn-group" data-toggle="buttons">
    @foreach($options as $option => $label)
        <label class="btn btn-default btn-sm {{ \Request::get('role', 'all') == $option ? 'active' : '' }}">
            <input type="radio" class="user-role" value="{{ $option }}">{{$label}}
        </label>
    @endforeach
</div>