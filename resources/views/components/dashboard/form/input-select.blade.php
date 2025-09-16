<div class="form-group row">
    <label  class="col-xl-3 col-md-4" for="{{ $id }}">{{ $labelTitle }}</label>
    <div class="col-xl-8 col-md-7">
        <select class="custom-select select2 w-100 form-control"
                id="{{ $id }}"
                name="{{ $name }}"
                @isset($multible) multiple @endisset
                @isset($required) required @endisset>
            <option value="" disabled @if(!isset($multible)) selected @endif>--Select Option--</option>
            @foreach($options as $key => $option)
                @php
                    $optionValue = $key;
                    $optionText = $option;
                    
                    // Handle model objects with track-by and option-lable attributes
                    if (isset($trackBy) && isset($optionLable) && is_object($option)) {
                        $optionValue = $option->{$trackBy};
                        $optionText = $option->{$optionLable};
                    }
                    
                    // Handle arrays (when toArray() is used)
                    if (is_array($option) && isset($trackBy) && isset($optionLable)) {
                        $optionValue = $option[$trackBy];
                        $optionText = $option[$optionLable];
                    }
                @endphp
                <option
                    @isset($value)
                    @if(isset($multible) ? (is_array($value) && in_array($optionValue, $value)) : $value == $optionValue )
                            selected
                    @endif

                    @endisset
                    value="{{ $optionValue }}">
                    {{ Str::headline(is_string($optionText) ? $optionText : '') }}
                </option>
            @endforeach
        </select>
        @isset($errorKey)
            @error($errorKey)
            <span class="d-block text-danger">{{ $message }}</span>
            @enderror
        @endisset
    </div>
</div>
