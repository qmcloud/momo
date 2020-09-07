<div class="input-group input-group-sm">
    @if($group)
        <div class="input-group-prepend">
            <div class="input-group-text">
                <input type="hidden" name="{{ $id }}_group" class="{{ $group_name }}-operation" value="0"/>
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="min-width: 32px;">
                    <span class="{{ $group_name }}-label">{{ $default['label'] }}</span>
                </button>
                <div class="dropdown-menu {{ $group_name }}">
                    @foreach($group as $index => $item)
                        <a href="#" data-index="{{ $index }} dropdown-item"> {{ $item['label'] }} </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-{{ $icon }}"></i></span>
        </div>

    <input type="{{ $type }}" class="form-control {{ $id }}" placeholder="{{$placeholder}}" name="{{$name}}" value="{{ request($name, $value) }}">
</div>