<div class="form-group">
    <label for="{{ $field->name }}" class="col-md col-form-label text-md-right">
        {{ $field->label }}
    </label>

    <div class="col-md">
        <input
            id="{{ $field->name }}"
            type="{{ $field->input_type }}"
            class="form-control {{ $field->input_class }} @error($field->key) is-invalid @enderror"
            autocomplete="{{ $field->autocomplete }}"
            placeholder="{{ $field->placeholder }}"
            wire:model.lazy="{{ $field->key }}">

        @include('livewire-forms::fields.error-help')
    </div>
</div>
