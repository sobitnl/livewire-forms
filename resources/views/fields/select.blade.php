<div class="form-group">
    <label for="{{ $field->name }}" class="col-md col-form-label text-md-right">
        {{ $field->label }}
    </label>

    <div class="col-md">
        <select
            id="{{ $field->name }}"
            class="custom-select {{ $field->input_class }} @error($field->key) is-invalid @enderror"
            wire:model.lazy="{{ $field->key }}">

            <option value="">{{ $field->placeholder }}</option>

            @foreach($field->options as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        @include('livewire-forms::fields.error-help')
    </div>
</div>
